<?php

namespace App\Http\Services\Rewards;

use App\Http\Notifications\GiftCardNotification;
use App\Models\Rewards\GiftCard;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Rewards\GiftCardPermission;
use App\Models\General\Setting;
use App\Models\Rewards\FreeService;
use App\Models\Salons\SalonPayment;
use App\Models\Services\Service;
use App\Models\Users\User;
use App\Models\Users\WalletTransaction;
use App\Services\PhoneService;
use App\Services\WhatsappMessageService;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GiftCardService
{
    public function index($data)
    {
        $query = GiftCard::query()->with(['sender', 'recipient', 'salon', 'theme']);


        $query = GiftCardPermission::filterIndex($query);

        if (isset($data['search']) && $data['search'] != '') {
            $data['search'] =  str_replace(' ', '', $data['search']);
            $query->whereRaw("CONCAT(phone_code, phone) LIKE ?", ['%' . $data['search'] . '%']);
        }

        if (isset($data['is_received']) && $data['is_received'] == 1) {
            $query->whereNotNull('received_at');
        }


        $query = FilterService::applyFilters(
            $query,
            $data,
            ['code', 'message'],
            ['amount'],
            ['created_at', 'received_at'],
            ['type', 'is_used', 'sender_id', 'recipient_id', 'salon_id'],
            ['id'],
            false,
        );

        $giftCards = $query->get();

        // statistics: 
        // - كم شخص له هدية غير مسجل
        // - كم شخص له هدية مسجل بعد الهدية
        // - كم شخص له هدية مسجل قبل الهدية



        $total = $giftCards->count();
        $notRegistered = $giftCards->where('recipient_id', null)->unique(function ($giftCard) {
            return $giftCard->phone_code . $giftCard->phone;
        })->count();

        $registered = $giftCards
            ->whereNotNull('recipient_id')
            ->filter(fn($g) => $g->recipient && $g->created_at > $g->recipient->register_at)
            ->unique(fn($g) => $g->phone_code . $g->phone)
            ->count();

        $registeredBefore = $giftCards
            ->whereNotNull('recipient_id')
            ->filter(fn($g) => $g->recipient && $g->created_at < $g->recipient->register_at)
            ->unique(fn($g) => $g->phone_code . $g->phone)
            ->count();



        return [
            'info' => [
                'total' => [
                    'lable' => 'الاجمالي',
                    'value' => $total,
                ],
                'not_registered' => [
                    'lable' => 'عدد الأشخاص الذين لديهم هدية غير مسجلين',
                    'value' => $notRegistered,
                ],
                'registered' => [
                    'lable' => 'عدد الأشخاص الذين لديهم هدية مسجلين بعد حصولهم عليها',
                    'value' => $registered,
                ],
                'registered_before' => [
                    'lable' => 'عدد الأشخاص الذين لديهم هدية مسجلين قبل حصولهم عليها',
                    'value' => $registeredBefore,
                ],
            ],
            'data' => $query->paginate($data['limit'] ?? 20),
        ];
    }

    public function show($id)
    {
        $item = GiftCard::with(['sender', 'recipient', 'theme'])->find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.gift_card.item_not_found');
        }

        if ($item->salon_id) {
            $item->load(['salon']);
        }

        return $item;
    }

    public function create($data)
    {

        $phoneParts = PhoneService::parsePhoneParts($data['phone']);
        $data['phone_code'] = $phoneParts['country_code'];
        $data['phone'] = $phoneParts['national_number'];



        $giftCard = GiftCard::create($data);

        $giftCard->load(['sender', 'recipient', 'theme']);

        if ($giftCard->salon_id) {
            $giftCard->load(['salon']);
        }

        return $giftCard;
    }

    public function update($item, $data)
    {
        $item->update($data);


        if ($item->salon_id) {
            $item->load(['salon']);
        }

        $item->load(['sender', 'recipient', 'theme']);

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
        $payment_type = $data['payment_type'] ?? 'wallet';

        $recipient = User::where('phone_code', $data['phone_code'])
            ->where('phone', $data['phone'])
            ->where('role', 'customer')
            ->first();

        if ($recipient) {
            if ($recipient && $recipient->id == $user->id) {
                MessageService::abort(422, 'messages.gift_card.cannot_send_to_yourself');
            }
            $data['recipient_id'] = $recipient->id;
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

        if ($payment_type === 'wallet') {
            return $this->processWalletPayment($user, $total, $data, $recipient);
        } else if ($payment_type === 'stripe') {
            return $this->processStripePayment($user, $total, $data, $recipient);
        }

        MessageService::abort(422, 'messages.invalid_payment_method');
    }

    private function processWalletPayment($user, $total, $data, $recipient)
    {
        $user_balance = $user->balance;

        if ($user_balance < $total) {
            MessageService::abort(422, 'messages.user.not_enough_balance');
        }

        $user->balance -= $total;
        $user->save();

        return $this->createGiftCard($user, $total, $data, $recipient, 'wallet');
    }

    private function processStripePayment($user, $total, $data, $recipient)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create or get Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
                'phone' => $user->phone,
            ]);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        // Create ephemeral key
        $ephemeralKey = EphemeralKey::create(
            ['customer' => $user->stripe_customer_id],
            ['stripe_version' => '2023-10-16']
        );

        // Create payment intent first
        $paymentIntent = PaymentIntent::create([
            'amount' => $total * 100, // Convert to cents
            'currency' => 'aed',
            'customer' => $user->stripe_customer_id,
            'setup_future_usage' => 'off_session',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'type' => 'gift_card',
                'user_id' => $user->id,
                'phone' => $user->phone_code . ' ' . $user->phone,
                'amount' => $total,
            ],
        ]);

        // Prepare gift card data for cache
        $giftCardData = [
            'user_id' => $user->id,
            'recipient_id' => $recipient?->id ?? null,
            'phone_code' => $data['phone_code'],
            'phone' => $data['phone'],
            'type' => $data['type'],
            'amount' => $data['amount'] ?? null,
            'currency' => $data['currency'] ?? null,
            'services' => $data['services'] ?? null,
            'message' => $data['message'],
            'salon_id' => $data['salon_id'] ?? null,
            'theme_id' => $data['theme_id'] ?? 1,
        ];

        // Store gift card data in cache for 1 hour
        $cacheKey = "gift_card_data_{$paymentIntent->id}";
        Cache::put($cacheKey, $giftCardData, 3600);

        Log::info('Gift card data stored in cache', [
            'payment_intent_id' => $paymentIntent->id,
            'cache_key' => $cacheKey,
            'user_id' => $user->id,
            'amount' => $total
        ]);

        return [
            'payment' => [
                'client_secret' => $paymentIntent->client_secret,
                'customer_id' => $user->stripe_customer_id,
                'ephemeral_key' => $ephemeralKey->secret,
                'amount' => $total,
            ]
        ];
    }

    public function createGiftCard($user, $total, $data, $recipient, $payment_method)
    {
        $message = $data['message'];

        // if (!$recipient || ($recipient && !$recipient->is_verified && $recipient->added_by == 'salon')) {
        $full_phone = str_replace(' ', '', $data['phone_code'] . $data['phone']);

        $ios_link_app = Setting::where('key', 'ios_app_url')->first()->value ?? '';
        $android_link_app = Setting::where('key', 'android_app_url')->first()->value ?? '';
        $full_name = $user->first_name . ' ' . $user->last_name;

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
            'ios_link' => $ios_link_app,
            'android_link' => $android_link_app,
            'greeting' => $recipient ? ($recipient->first_name ?? '') : (app()->getLocale() == 'ar' ? 'عزيزي' : 'Dear'),
        ]);

        WhatsappMessageService::send($full_phone, $message);



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
            'amount' => $data['amount'] ?? null,
            'currency' => $data['currency'] ?? null,
            'services' => $data['services'],
            'message' => $data['message'],
            'tax' => null,
            'salon_id' => $data['salon_id'] ?? null,
            'theme_id' => $data['theme_id'] ?? 1,
        ]);

        // transaction
        $transaction = WalletTransaction::create([
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
            'metadata' => [
                'payment_method' => $payment_method,
            ],
        ]);

        GiftCardNotification::sendGiftCardToUser($giftCard);

        $system_percentage = Setting::where('key', 'system_percentage_gift')->first()->value ?? 0;

        if ($data['type'] == 'services') {
            $salonPayment = SalonPayment::create([
                'paymentable_id' => $giftCard->id,
                'paymentable_type' => GiftCard::class,
                'user_id' => $user->id,
                'salon_id' => $data['salon_id'],
                'amount' => $total,
                'currency' => 'AED',
                'method' => $payment_method,
                'status' => 'confirm',
                'is_refund' => false,
                'system_percentage' => $system_percentage,
            ]);

            $salonPayment->code = 'SP' . str_pad($salonPayment->id, 6, '0', STR_PAD_LEFT);
            $salonPayment->save();
        }

        $giftCard->load(['sender', 'recipient', 'theme']);

        return $giftCard;
    }




    // استلام بطاقة الهدايا
    public function receive($giftCard)
    {
        $user = User::auth();


        if ($giftCard->recipient_id != $user->id) {
            MessageService::abort(422, 'messages.gift_card.not_your_gift_card');
        }


        if (!$giftCard) {
            MessageService::abort(422, 'messages.gift_card.item_not_found');
        }


        // if ($giftCard->is_used) {
        //     MessageService::abort(422, 'messages.gift_card.item_already_used');
        // }

        if ($giftCard->received_at) {
            MessageService::abort(422, 'messages.gift_card.item_already_received');
        }


        if ($giftCard->type == 'services') {
            $services = $giftCard->services;
            $salon_id = $giftCard->salon_id;
            $salon = $giftCard->salon;
            foreach ($services as $serviceId) {
                $service = Service::find($serviceId);
                if ($service) {
                    FreeService::create([
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'salon_id' => $salon_id,
                        'freeable_id' => $giftCard->id,
                        'freeable_type' => GiftCard::class,
                        'source' => 'gift',
                        'is_used' => false,
                        'booking_id' => null,
                    ]);
                }
            }

            if ($salon) {
                $giftCard->salon_id = $salon->id;
            } else {
                MessageService::abort(422, 'messages.gift_card.salon_not_found');
            }
        } elseif ($giftCard->type == 'amount') {
            $user->balance += $giftCard->amount;
            $user->save();


            // transaction
            WalletTransaction::create(
                [
                    'user_id' => $user->id,
                    'amount' => $giftCard->amount,
                    'currency' => 'AED',
                    'description' => [
                        'en' => __('messages.gift_card.received_transaction_details', ['code' => $giftCard->code, 'amount' => $giftCard->amount, 'currency' => 'AED'], 'en'),
                        'ar' => __('messages.gift_card.received_transaction_details', ['code' => $giftCard->code, 'amount' => $giftCard->amount, 'currency' => 'AED'], 'ar'),
                    ],
                    'status' => 'completed',
                    'type' => 'gift_card',
                    'is_refund' => false,
                    'transactionable_id' => $giftCard->id,
                    'transactionable_type' => GiftCard::class,
                    'direction' => "in",
                    'metadata' => [],
                ]
            );
        }



        $giftCard->is_used = true;
        $giftCard->received_at = now();
        $giftCard->save();

        GiftCardNotification::receiveGiftCard($giftCard);


        $giftCard->load(['sender', 'recipient', 'theme']);


        return $giftCard;
    }


    // get phone numbers i am send gift cards to them
    public function getSentGiftCards()
    {
        $user = User::auth();
        $giftCards = GiftCard::where('sender_id', $user->id)
            // ->where('recipient_id', null)
            ->get();


        // filter unique phone numbers and  i need only phone_code and phone and full user name if exist
        $giftCards = $giftCards->unique(function ($item) {
            return $item->phone_code . $item->phone;
        })->values()->map(function ($item) {
            return [
                'phone_number' => $item->phone_code . $item->phone,
                'full_name' => $item->recipient
                    ? trim(($item->recipient->first_name ?? '') . ' ' . ($item->recipient->last_name ?? ''))
                    : null,
            ];
        })->values()->all();

        return $giftCards;
    }
}
