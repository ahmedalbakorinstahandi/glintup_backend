<?php

namespace App\Http\Services\Rewards;

use App\Models\Rewards\GiftCard;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Rewards\GiftCardPermission;
use App\Models\General\Setting;
use App\Models\Salons\SalonPayment;
use App\Models\Services\Service;
use App\Models\Users\User;
use App\Models\Users\WalletTransaction;
use App\Services\WhatsappMessageService;

class GiftCardService
{
    public function index($data)
    {
        $query = GiftCard::query()->with(['sender', 'recipient', 'salon']);


        $query = GiftCardPermission::filterIndex($query);

        if (isset($data['search']) && $data['search'] != '') {
            $data['search'] =  str_replace(' ', '', $data['search']);
            $query->whereRaw("CONCAT(phone_code, phone) LIKE ?", ['%' . $data['search'] . '%']);
        }


        return FilterService::applyFilters(
            $query,
            $data,
            ['code', 'message'],
            ['amount'],
            ['created_at'],
            ['type', 'is_used', 'sender_id', 'recipient_id', 'salon_id'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = GiftCard::with(['sender', 'recipient'])->find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.gift_card.item_not_found');
        }
        return $item;
    }

    public function create($data)
    {
        return GiftCard::create($data);
    }

    public function update($item, $data)
    {
        $item->update($data);
        return $item;
    }

    public function destroy($item)
    {
        return $item->delete();
    }

    //createByUser
    public function createByUser($data)
    {

        // sender_id is current user
        // recipient_id is user with phone number and code

        $user = User::auth();

        $recipient = User::where('phone_code', $data['phone_code'])
            ->where('phone', $data['phone'])
            ->first();


        if ($recipient) {

            if ($recipient && $recipient->id == $user->id) {
                MessageService::abort(422, 'messages.gift_card.cannot_send_to_yourself');
            }

            $data['recipient_id'] = $recipient->id;

            //TODO:send notification to recipient
        }





        $total = 0;
        // type is services or amount
        if ($data['type'] == 'services') {

            $data['amount'] = null;
            $data['currency'] = null;
            $data['tax'] = null;

            $serviceIds = $data['services'];

            Service::whereIn('id', $serviceIds)->each(function ($service) use ($data) {
                if ($service->salon_id != $data['salon_id']) {
                    MessageService::abort(422, 'messages.booking.service_not_in_salon');
                }
            });

            $services = Service::whereIn('id', $data['services'])
                ->where('salon_id', $data['salon_id'])
                ->get();

            // حساب التكلفة

            foreach ($services as $service) {
                $total += $service->getFinalPriceAttribute();
            }
        } else {
            $data['services'] = null;
            $data['salon_id'] = null;

            $total = $data['amount'];
        }


        $user_balance = $user->balance;

        if ($user_balance < $total) {
            MessageService::abort(422, 'messages.user.not_enough_balance');
        }

        $user->balance -= $total;
        $user->save();





        $message = $data['message'];



        if (!$recipient || ($recipient && !$recipient->is_verified && $recipient->added_by == 'salon')) {
            $full_phone = str_replace(' ', '', $data['phone_code'] . $data['phone']);

            $website_url = "https://glintup.ae/";
            $full_name = User::auth()->first_name . ' ' . User::auth()->last_name;

            if ($data['type'] == 'amount') {
                $details = trans('messages.gift_card_amount_details', [
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                ]);
            } else {
                $serviceNames = "";

                foreach ($data['services'] as $serviceId) {
                    $service = Service::find($serviceId);

                    $lang = app()->getLocale();
                    if ($service) {
                        $serviceNames .= "-" . $service->name[$lang] . "\n";
                    }
                }

                $details = trans('messages.gift_card_service_details', [
                    'services' => $serviceNames,
                ]);
            }

            $message = trans('messages.gift_card_message', [
                'sender'  => $full_name,
                'details' => $details,
                'note'    => $data['message'],
                'link'    => $website_url,
            ]);

            WhatsappMessageService::send($full_phone, $message);
        }



        $data['sender_id'] = $user->id;
        $code = GiftCard::generateCode();
        while (GiftCard::where('code', $code)->withTrashed()->exists()) {
            $code = GiftCard::generateCode();
        }


        $giftCard = GiftCard::create([
            'code' => $code,
            'sender_id' => $data['sender_id'],
            'recipient_id' => $recipient?->id ?? null,
            'phone_code' => str_replace(' ', '', $data['phone_code']),
            'phone' => str_replace(' ', '', $data['phone']),
            'type' => $data['type'],
            'amount' => $total,
            'currency' => $data['currency'] ?? null,
            'services' => $data['services'],
            'message' => $message,
            'tax' => null,
            'salon_id' => $data['salon_id'] ?? null,
        ]);


        // transaction
        $transaction = WalletTransaction::create(
            [
                'user_id' => $user->id,
                'amount' => $total,
                'currency' => 'AED',
                'description' => [
                    'en' => __('messages.gift_card.transaction_details', ['code' => $giftCard->code, 'amount' => $total, 'currency' => 'AED'], 'en'),
                    'ar' => __('messages.gift_card.transaction_details', ['code' => $giftCard->code, 'amount' => $total, 'currency' => 'AED'], 'ar'),
                ],
                'status' => 'completed',
                'type' => 'gift_card',
                'is_refund' => false,
                'transactionable_id' => $giftCard->id,
                'transactionable_type' => GiftCard::class,
                'direction' => "out",
                'metadata' => [],
            ]
        );

        $system_percentage = Setting::where('key', 'system_percentage_gift')->first()->value ?? 0;


        if ($data['type'] == 'services') {
            SalonPayment::create([
                'paymentable_id' => $giftCard->id,
                'paymentable_type' => GiftCard::class,
                'user_id' => $user->id,
                'salon_id' => $data['salon_id'],
                'amount' => $total,
                'currency' => 'AED',
                'method' => 'wallet',
                'status' => 'confirm',
                'is_refund' => false,
                'system_percentage' => $system_percentage,
            ]);
        }



        return $giftCard;
    }


    // 'phone_code' => 'required|string',
    // 'phone' => 'required|string',
    // 'type' => 'required|in:services,amount',
    // 'amount' => 'required_if:type,amount|numeric',
    // 'currency' => 'required_if:type,amount|string',
    // 'salon_id' => 'required_if:type,services|exists:salons,id,deleted_at,NULL',
    // 'services' => 'nullable|array|max:3',
    // 'services.*' => 'required_if:type,services|exists:services,id,deleted_at,NULL',
    // 'message' => 'required|string',
}
