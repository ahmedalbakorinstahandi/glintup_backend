<?php

namespace App\Http\Notifications;

use App\Models\Users\User;
use App\Services\FirebaseService;

class UserNotification
{

    public static function newUser($user) {
        $title = 'notifications.admin.user.new_user';
        $body = 'notifications.admin.user.new_user_body';

        $data = [
            'full_user_name' => $user->first_name . ' ' . $user->last_name,
        ];

        $pemissionKey = 'users';
        $users = User::where('role', 'admin')->whereHas('adminPermissions', function ($query) use ($pemissionKey) {
            $query->where('key', $pemissionKey);
        })->get();

        FirebaseService::sendToTopicAndStorage(
            'role-admin',
            $users->pluck('id'),
            [
                'id' => $user->id,
                'type' => 'User',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
}
