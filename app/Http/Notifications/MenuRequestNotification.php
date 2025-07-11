<?php

namespace App\Http\Notifications;

use App\Models\Users\User;

class MenuRequestNotification
{
    // طلب منيو جديد - طلبات القوائم
    public static function newMenuRequest($menuRequest) 
    {
        $user = $menuRequest->user;

        $admin = User::auth();

        $title = 'notifications.admin.menu_request.new_menu_request';
    }
}
