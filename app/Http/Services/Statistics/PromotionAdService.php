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
        // Check if the ad duration is more than 3 days
        $startDate = new \DateTime($data['valid_from']);
        $endDate = new \DateTime($data['valid_to']);
        $interval = $startDate->diff($endDate);

        $maxDuration = 3;
        if ($interval->days > $maxDuration) {
            MessageService::abort(400, 'messages.ad_duration_exceeds_limit', ['max_duration' => $maxDuration]);
        }

        // Check if start date is after end date
        if ($startDate > $endDate) {
            MessageService::abort(400, 'messages.start_date_after_end_date');
        }

        // // Check if there are already 5 approved ads in the given time range
        // $existingAds = Ad::where('status', 'approved')
        //     ->where(function ($query) use ($startDate, $endDate) {
        //         $query->whereBetween('start_date', [$startDate, $endDate])
        //             ->orWhereBetween('end_date', [$startDate, $endDate])
        //             ->orWhere(function ($query) use ($startDate, $endDate) {
        //                 $query->where('start_date', '<=', $startDate)
        //                     ->where('end_date', '>=', $endDate);
        //             });
        //     })
        //     ->count();

        // $maxApprovedAds = 5;
        // if ($existingAds >= $maxApprovedAds) {
        //     MessageService::abort(400, 'messages.ad_time_slot_full', ['max_approved_ads' => $maxApprovedAds]);
        // }


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


        $data['salon_id'] = $user->salon->id;

        // Create the ad
        $ad = PromotionAd::create($data);

        // Deduct the ad price from the user's balance
        WalletTransaction::create([
            'salon_id' => $user->id,
            'amount' => $amount,
            'type' => 'ad',
            'direction' => 'out',
            'status' => 'completed',
            'message' => [
                #TODO: translate later
                'en' => 'Payment for ad #' . $ad->id,
                'ar' => 'دفع للإعلان #' . $ad->id,
            ],
            'metadata' => json_encode([
                'ad_id' => $ad->id,
            ]),

        ]);

        $user->wallet_balance -= $amount;
        $user->save();

        return $ad;
    }
}
