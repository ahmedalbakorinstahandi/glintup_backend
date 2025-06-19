<?php

namespace App\Http\Services\Services;

use App\Http\Permissions\Users\WalletTransactionPermission;
use App\Models\Users\WalletTransaction;
use App\Services\FilterService;
use App\Services\MessageService;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class WalletTransactionService
{
    public function createPaymentIntent($user, $amount)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
                'phone' =>  $user->phone,
            ]);

            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        $ephemeralKey = EphemeralKey::create(
            ['customer' => $user->stripe_customer_id],
            ['stripe_version' => '2023-10-16']
        );


        $walletTransaction = WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => 'aed',
            'direction' => 'in',
            'status' => 'pending',
            'type' => 'deposit',
            'description' => [
                'en' => __('messages.wallet_deposit', ['amount' => $amount], 'en'),
                'ar' => __('messages.wallet_deposit', ['amount' => $amount], 'ar'),
            ],
            'transactionable_id' => null,
            'transactionable_type' => null,
            'metadata' => [],
        ]);


        $paymentIntent = PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => 'aed',
            'customer' => $user->stripe_customer_id,
            'setup_future_usage' => 'off_session',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'transaction_id' => $walletTransaction->id,
                'phone' => $user->phone_code . ' ' . $user->phone,
                'user_id' => $user->id,
                'type' => 'deposit',
            ],
        ]);


        return [
            'transaction_id' => $walletTransaction->id,
            'client_secret' => $paymentIntent->client_secret,
            'customer_id' => $user->stripe_customer_id,
            'ephemeral_key' => $ephemeralKey->secret,
        ];
    }


    public function index($data)
    {
        $query = WalletTransaction::query()->with('user');

        $query = WalletTransactionPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['description', ['user.first_name', 'user.last_name']],
            ['amount'],
            ['created_at'],
            ['user_id', 'status', 'type', 'direction', 'is_refund'],
            ['id', 'status']
        );
    }

    public function show($id)
    {
        $item = WalletTransaction::with('user')->find($id);

        if (!$item) {
            MessageService::abort(404, 'messages.wallet_transaction.item_not_found');
        }

        return $item;
    }

    public function create($data)
    {
        return WalletTransaction::create($data);
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
}
