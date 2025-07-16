<?php

namespace App\Http\Notifications;

use App\Services\FirebaseService;

class SalonPermissionNotification
{
    public static function updateEmployeePermission($user)
    {
        $title = 'notifications.salon.employee.permission.update_title';
        $body = 'notifications.salon.employee.permission.update_body';

        $data = [
            'employee_name' => $user->first_name . ' ' . $user->last_name,
        ];

        FirebaseService::sendToTokensAndStorage(
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
