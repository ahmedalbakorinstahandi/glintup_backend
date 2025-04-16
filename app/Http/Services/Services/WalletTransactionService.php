<?php

namespace App\Http\Services\Services;

use App\Http\Permissions\Users\WalletTransactionPermission;
use App\Models\Users\WalletTransaction;
use App\Services\FilterService;
use App\Services\MessageService;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class WalletTransactionService
{
    public function createPaymentIntent($user, $amount)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));


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
            'currency' => 'aed', // درهم اماراتي
            'payment_method_types' => ['card'],
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
        ];
    }


    public function index($data)
    {
        $query = WalletTransaction::query()->with('user');

        $query = WalletTransactionPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['description'],
            ['amount'],
            ['created_at'],
            ['user_id', 'status', 'type', 'direction', 'is_refund'],
            ['id']
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
