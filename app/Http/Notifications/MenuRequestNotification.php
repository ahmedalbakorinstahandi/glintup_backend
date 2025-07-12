<?php

namespace App\Http\Notifications;

use App\Models\Salons\SalonMenuRequest;
use App\Models\Users\User;
use App\Services\FirebaseService;

class MenuRequestNotification
{
    // طلب منيو جديد - طلبات القوائم
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

        $users = User::where('role', 'admin')->whereHas('adminPermissions', function ($query) use ($pemissionKey) {
            $query->where('key', $pemissionKey);
        })->get();

        FirebaseService::sendToTopicAndStorage(
            'role-admin',
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
