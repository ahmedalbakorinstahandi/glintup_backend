<?php

namespace App\Http\Notifications;

use App\Models\Users\User;
use App\Services\FirebaseService;

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
            'ad_title' => $ad->title,
        ];

        $pemissionKey = 'advertisements';

        $users = User::where('role', 'admin')->whereHas('adminPermissions', function ($query) use ($pemissionKey) {
            $query->where('key', $pemissionKey);
        })->get();

        FirebaseService::sendToTopicAndStorage(
            'role-admin',
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
}
