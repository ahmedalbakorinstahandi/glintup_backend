<?php

namespace App\Http\Notifications;

use App\Services\FirebaseService;

class LoyaltyPointNotification
{
    public static function loyaltyPointAdded($loyaltyPoint)
    {
        $user = $loyaltyPoint->user;

        $title = 'notifications.user.loyalty_point.added_one_point.title';

        $body = 'notifications.user.loyalty_point.added_one_point.body';

        $data = [
            'loyalty_point_id' => $loyaltyPoint->id,
            'salon_name' => $loyaltyPoint->salon->merchant_commercial_name,
        ];

        FirebaseService::sendToTokensAndStorage(
            [$user->id],
            [
                'id' => $loyaltyPoint->id,
                'type' => 'LoyaltyPoint',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }

    public static function loyaltyPointWonReward($loyaltyPoint)
    {
        $user = $loyaltyPoint->user;

        $title = 'notifications.user.loyalty_point.won_reward.title';
        $body = 'notifications.user.loyalty_point.won_reward.body';

        $data = [
            'loyalty_point_id' => $loyaltyPoint->id,
            'salon_name' => $loyaltyPoint->salon->merchant_commercial_name,
            'points' => $loyaltyPoint->points,
        ];

        FirebaseService::sendToTokensAndStorage(
            [$user->id],
            [
                'id' => $loyaltyPoint->id,
                'type' => 'LoyaltyPoint',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
}
