<?php

namespace App\Http\Notifications;

use App\Models\Salons\SalonMenuRequest;
use App\Models\Users\User;
use App\Services\FirebaseService;

class MenuRequestNotification
{
    public static function newMenuRequest($menuRequest)
    {
        $user = $menuRequest->user;


        $title = 'notifications.admin.menu_request.new_menu_request';
        $body = 'notifications.admin.menu_request.new_menu_request_body';

        $data = [
            'menu_request_id' => $menuRequest->id,
            'salon_name' => $menuRequest->salon->merchant_commercial_name,
        ];


        $pemissionKey = 'salon-menu-requests';

        $users = NotificationHelper::getUsersAdminPermissions($pemissionKey);

        FirebaseService::sendToTokensAndStorage(
            $users->pluck('id'),
            [
                'id' => $menuRequest->id,
                'type' => 'SalonMenuRequest',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }

    //طلبات القوائم - اشعار قبول او رفض الطلبات
    public static function acceptMenuRequest($menuRequest)
    {
        $user = $menuRequest->user;

        $title = 'notifications.salon.menu_request.accept_title';
        $body = 'notifications.salon.menu_request.accept_body';

        $data = [
            'menu_request_id' => $menuRequest->id,
            'salon_name' => $menuRequest->salon->merchant_commercial_name,
        ];

        $pemissionKey = 'services';

        $users = NotificationHelper::getUsersSalonPermissions($menuRequest->salon_id, $pemissionKey);

        FirebaseService::sendToTokensAndStorage(
            $users->pluck('id'),
            [
                'id' => $menuRequest->id,
                'type' => 'SalonMenuRequest',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }

    public static function rejectMenuRequest($menuRequest)
    {
        $user = $menuRequest->user;

        $title = 'notifications.salon.menu_request.reject_title';
        $body = 'notifications.salon.menu_request.reject_body';

        $data = [
            'menu_request_id' => $menuRequest->id,
            'salon_name' => $menuRequest->salon->merchant_commercial_name,
        ];

        $pemissionKey = 'services';

        $users = NotificationHelper::getUsersSalonPermissions($menuRequest->salon_id, $pemissionKey);

        FirebaseService::sendToTokensAndStorage(
            $users->pluck('id'),
            [
                'id' => $menuRequest->id,
                'type' => 'SalonMenuRequest',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
}
