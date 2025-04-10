<?php

namespace App\Http\Services\Statistics;

use App\Models\Statistics\PromotionAd;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;
use App\Http\Permissions\Statistics\PromotionAdPermission;

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
}
