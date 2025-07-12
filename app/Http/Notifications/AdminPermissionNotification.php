<?php

namespace App\Http\Notifications;

use App\Models\Users\User;
use App\Services\FirebaseService;

class AdminPermissionNotification
{
    public static function updateEmployeePermission($user)
    {
        $title = 'notifications.admin.employee_permission.update_employee_permission_title';
        $body = 'notifications.admin.employee_permission.update_employee_permission_body';

        $admin = User::auth();
        $data = [
            'admin_user_name' => $admin->first_name . ' ' . $admin->last_name,
        ];

        FirebaseService::sendToTopicAndStorage(
            'user-' . $user->id,
            [$user->id],
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
