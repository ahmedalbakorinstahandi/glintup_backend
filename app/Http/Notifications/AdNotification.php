<?php

namespace App\Http\Notifications;

use App\Services\FirebaseService;
use App\Services\LanguageService;

class AdNotification
{
    public static function newAd($ad)
    {
        $user = $ad->user;

        $title = 'notifications.admin.ad.new_ad';
        $body = 'notifications.admin.ad.new_ad_body';



        $data = [
            'promotion_ad_id' => $ad->id,
            'salon_name' => $ad->salon->merchant_commercial_name,
        ];


        $pemissionKey = 'advertisements';

        $users = NotificationHelper::getUsersAdminPermissions($pemissionKey);

        FirebaseService::sendToTokensAndStorage(
            $users->pluck('id'),
            [
                'id' => $ad->id,
                'type' => 'PromotionAd',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }

    // accept ad
    public static function approveAd($ad)
    {
        $user = $ad->user;

        $title = 'notifications.salon.ad.approve_ad';
        $body = 'notifications.salon.ad.approve_ad_body';

        $locale = LanguageService::getLocale();

        $replace = [
            'promotion_ad_id' => $ad->id,
            'salon_name' => $ad->salon->merchant_commercial_name,
            'ad_title' => $ad->title[$locale],
            'locales' => [
                'ad_title' => NotificationHelper::handleLocales($ad->title, 'ad_title'),
            ],
        ];

        $pemissionKey = 'Ads';

        $users = NotificationHelper::getUsersSalonPermissions($ad->salon_id, $pemissionKey);

        FirebaseService::sendToTokensAndStorage(
            $users->pluck('id'),
            [
                'id' => $ad->id,
                'type' => 'PromotionAd',
            ],
            $title,
            $body,
            $replace,
            $replace
        );
    }

    // reject ad
    public static function rejectAd($ad)
    {
        $user = $ad->user;

        $title = 'notifications.salon.ad.reject_ad';
        $body = 'notifications.salon.ad.reject_ad_body';

        $locale = LanguageService::getLocale();

        $replace = [
            'promotion_ad_id' => $ad->id,
            'salon_name' => $ad->salon->merchant_commercial_name,
            'ad_title' => $ad->title[$locale],
            'locales' => [
                'ad_title' => NotificationHelper::handleLocales($ad->title, 'ad_title'),
            ],
        ];

        $pemissionKey = 'Ads';

        $users = NotificationHelper::getUsersSalonPermissions($ad->salon_id, $pemissionKey);

        FirebaseService::sendToTokensAndStorage(
            $users->pluck('id'),
            [
                'id' => $ad->id,
                'type' => 'PromotionAd',
            ],
            $title,
            $body,
            $replace,
            $replace
        );
    }
}
