<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Http\Services\Statistics\AdStatisticService;
use App\Models\Statistics\PromotionAd;
use App\Services\MessageService;
use Illuminate\Http\Request;

class AdStatisticController extends Controller
{

    protected $adStatisticService;

    public function __construct(AdStatisticService $adStatisticService)
    {
        $this->adStatisticService = $adStatisticService;
    }


    public function clicked($id)
    {


        $ad = PromotionAd::find($id);

        if (!$ad || $ad->isValid() == false) {
            MessageService::abort(
                404,
                'messages.promotion_ad.item_not_found',
            );
        }



        $this->adStatisticService->clicked($ad);


        return response()->json(
            [
                'success' => true,
            ]
        );
    }

    public function viewed($id)
    {

        $ad = PromotionAd::find($id);

        if (!$ad || $ad->isValid() == false) {
            MessageService::abort(
                404,
                'messages.promotion_ad.item_not_found',
            );
        }

        $this->adStatisticService->viewed($ad);

        return response()->json(
            [
                'success' => true,
            ]
        );
    }
}
