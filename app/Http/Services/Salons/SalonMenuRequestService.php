<?php


namespace App\Http\Services\Salons;

use App\Http\Notifications\MenuRequestNotification;
use App\Http\Permissions\Salons\SalonMenuRequestPermission;
use App\Models\General\Setting;
use App\Models\Salons\SalonMenuRequest;
use App\Models\Users\User;
use App\Models\Users\WalletTransaction;
use App\Services\FilterService;
use App\Services\MessageService;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SalonMenuRequestService
{
    public function index($data)
    {
        $query = SalonMenuRequest::with(['salon']);


        $query = SalonMenuRequestPermission::filterIndex($query);

        $query =  FilterService::applyFilters(
            $query,
            $data,
            ['notes', 'admin_note'],
            ['cost'],
            ['approved_at', 'rejected_at', 'created_at'],
            ['salon_id', 'status'],
            ['status', 'id'],
        );

        return $query;
    }


    public function show($id)
    {
        $request = SalonMenuRequest::find($id);


        if (!$request) {
            MessageService::abort(404, 'messages.salon_menu_request.not_found');
        }

        return $request;
    }

    public function create($data)
    {

        $user = User::auth();

        // get from settings
        $menuRequestCost = Setting::where('key', 'menu_request_cost')->first()->value;

        // create transaction 
        $walletTransaction = WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $menuRequestCost,
            'currency' => 'aed',
            'description' => [
                'en' => __('messages.menu_request_payment_description', [], 'en'),
                'ar' => __('messages.menu_request_payment_description', [], 'ar'),
            ],
            'type' => 'menu_request',
            'transactionable_id' => null,
            'transactionable_type' => null,
            'direction' => 'out',
            'status' => 'pending',
            'metadata' => [],
        ]);

        // Create Stripe checkout session
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'aed',
                    'product_data' => [
                        'name' => trans('messages.menu_request_payment_description'),
                    ],
                    'unit_amount' => $menuRequestCost * 100, // amount in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $data['success_url'],
            'cancel_url' => $data['cancel_url'],
            'metadata' => [
                'transaction_id' => $walletTransaction->id,
                'phone' => "{$user->phone_code} {$user->phone}",
                'user_id' => $user->id,
                'type' => 'menu_request',

                'data_salon_id' => $user->salon->id,
                'data_notes' => $data['notes'],
                'data_cost' => $menuRequestCost,
                'data_status' => 'pending',

            ],
        ]);




        $walletTransaction->update([
            'metadata' => [
                [
                    'checkout_session' => $checkoutSession->id,
                    'stripe_payment_id' => $checkoutSession->payment_intent,
                    'phone' => $user->phone_code . ' ' . $user->phone,
                    'user_id' => $user->id,
                    'salon_id' => $user->salon->id,
                    'type' => 'menu_request',
                ]
            ],
        ]);



        return [
            'checkout_session' => $checkoutSession->id,
            'stripe_payment_id' => $checkoutSession->payment_intent,
        ];
    }

    public function update($request, $data)
    {
        if (isset($data['status'])) {
            if ($data['status'] == 'approved' && $request->status == 'pending') {
                $request->update([
                    'approved_at' => now(),
                ]);

                MenuRequestNotification::acceptMenuRequest($request);
            } elseif ($data['status'] == 'rejected' && $request->status == 'pending') {
                $request->update([
                    'rejected_at' => now(),
                ]);

                MenuRequestNotification::rejectMenuRequest($request);
            }
        }

        $request->update($data);

        return $request;
    }

    public function destroy($request)
    {
        return $request->delete();
    }
}
