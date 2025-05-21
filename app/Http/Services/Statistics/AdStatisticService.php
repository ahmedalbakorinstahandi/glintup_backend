<?php


namespace App\Http\Services\Statistics;

use App\Models\Statistics\AdStatistic;
use App\Models\Users\User;

class AdStatisticService
{


    // clicked
    public function clicked($ad)
    {

        $user = User::auth();

        $adStatistic = AdStatistic::where('user_id', $user->id)
            ->where('promotion_ad_id', $ad->id)
            ->first();

        if ($adStatistic) {
            $adStatistic->clicked = true;
            $adStatistic->save();
        } else {
            AdStatistic::create([
                'user_id' => $user->id,
                'promotion_ad_id' => $ad->id,
                'clicked' => true,
            ]);
        }

        $ad->clicks += 1;
        $ad->save();

        return true;
    }

    // viewed
    public function viewed($ad)
    {

        $user = User::auth();

        $adStatistic = AdStatistic::where('user_id', $user->id)
            ->where('promotion_ad_id', $ad->id)
            ->first();

        if ($adStatistic) {
            $adStatistic->viewed = true;
            $adStatistic->save();
        } else {
            AdStatistic::create([
                'user_id' => $user->id,
                'promotion_ad_id' => $ad->id,
                'viewed' => true,
            ]);
        }

        $ad->views += 1;
        $ad->save();

        return true;
    }
}
