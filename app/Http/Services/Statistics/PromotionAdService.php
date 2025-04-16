<?php

namespace App\Http\Services\Statistics;

use App\Models\Statistics\PromotionAd;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;
use App\Http\Permissions\Statistics\PromotionAdPermission;
use App\Models\General\Setting;
use App\Models\Users\User;
use App\Models\Users\WalletTransaction;
use App\Services\ImageService;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PromotionAdService
{
    public function index($data)
    {
        $query = PromotionAd::query()->with('salon');

        $query = PromotionAdPermission::filterIndex($query);

        return FilterService::applyFilters($query, $data, ['title', 'description'], [], ['valid_from', 'valid_to'], ['salon_id', 'is_active'], ['id']);
    }

    public function show($id)
    {
        $ad = PromotionAd::with('salon')->find($id);

        if (!$ad) {
            MessageService::abort(404, 'messages.promotion_ad.item_not_found');
        }

        return $ad;
    }

    public function create($validatedData)
    {
        $validatedData = LanguageService::prepareTranslatableData($validatedData, new PromotionAd);

        $validatedData['clicks'] = 0;
        $validatedData['views'] = 0;

        return PromotionAd::create($validatedData);
    }

    public function update($ad, $validatedData)
    {
        $validatedData = LanguageService::prepareTranslatableData($validatedData, $ad);
        $ad->update($validatedData);
        return $ad;
    }

    public function destroy($ad)
    {
        return $ad->delete();
    }


    public function requestPostAd($data, $get_details)
    {
        $validatedData = LanguageService::prepareTranslatableData($data, new PromotionAd);


        // Check if the ad duration is more than 3 days
        $startDate = new \DateTime($data['valid_from']);
        $endDate = new \DateTime($data['valid_to']);
        $interval = $startDate->diff($endDate);

        $maxDuration = 14;
        if ($interval->days > $maxDuration) {
            MessageService::abort(400, 'messages.ad_duration_exceeds_limit', ['max_duration' => $maxDuration]);
        }

        // Check if start date is after end date
        if ($startDate > $endDate) {
            MessageService::abort(400, 'messages.start_date_after_end_date');
        }

        $ad_price_day = Setting::where('key', 'adver_cost_per_day')->first()->value;

        $hours = $interval->h;
        $minutes = $interval->i;
        $amount = $interval->days * $ad_price_day + ($hours / 24) * $ad_price_day + ($minutes / 1440) * $ad_price_day;

        if ($get_details) {
            return [
                'amount' => $amount,
                'ad_price_day' => $ad_price_day,
                'days' => $interval->days,
                'hours' => $hours,
                'minutes' => $minutes,
            ];
        }

        $user = User::auth();

        // Create the ad
        $ad = PromotionAd::create([
            'salon_id' => $user->salon->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'image' => $data['image'],
            'valid_from' => $data['valid_from'],
            'valid_to' => $data['valid_to'],
            'is_active' => true,
            'views' => 0,
            'clicks' => 0,
            'status' => 'draft',
        ]);

        // Store payment session in the database
        $walletTransaction = WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => 'aed',
            'description' => [
                'en' => __('messages.ad_payment_description', ['ad_id' => $ad->id], 'en'),
                'ar' => __('messages.ad_payment_description', ['ad_id' => $ad->id], 'ar'),
            ],
            'type' => 'ad',
            'transactionable_id' => $ad->id,
            'transactionable_type' => PromotionAd::class,
            'direction' => 'out',
            'status' => 'pending',
            'metadata' => [],
        ]);

        // Create Stripe checkout session
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'aed',
                    'product_data' => [
                        'name' => trans('messages.ad_payment_description', ['ad_id' => $ad->id]),
                    ],
                    'unit_amount' => $amount * 100, // amount in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $data['success_url'],
            'cancel_url' => $data['cancel_url'],
            'metadata' => [
                'transaction_id' => $walletTransaction->id,
                'phone' => $user->phone_code . ' ' . $user->phone,
                'user_id' => $user->id,
                'type' => 'ad',
                'ad_id' => $ad->id,
            ],
        ]);


        $walletTransaction->update([
            'metadata' => [
                [
                    'checkout_session' => $checkoutSession->id,
                    'stripe_payment_id' => $checkoutSession->payment_intent,
                    'phone' => $user->phone_code . ' ' . $user->phone,
                    'ad_id' => $ad->id,
                    'user_id' => $user->id,
                    'salon_id' => $user->salon->id,
                    'type' => 'ad',
                ]
            ],
        ]);



        // Return checkout session details
        return [
            'stripe' => [
                'checkout_session' => $checkoutSession->id,
                'stripe_payment_id' => $checkoutSession->payment_intent,
            ],
            'ad' => $ad,
        ];
    }
}
