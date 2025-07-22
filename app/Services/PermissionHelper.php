<?php

namespace App\Services;

use App\Models\Users\User;
use App\Services\MessageService;

class PermissionHelper
{
    public static function checkAdminPermission($permission)
    {
        $user = User::auth();


        if ($user) {
            if ($user->isAdmin()) {
                // Check if admin has the required permission
                $hasPermission = $user->adminPermissions()
                    ->where('key', $permission)
                    ->exists();

                if (!$hasPermission) {
                    MessageService::abort(403, 'messages.permission.error');
                }
            }
        }
    }
    public static function checkSalonPermission($permission)
    {
        $user = User::auth();


        if ($user) {
            if ($user->isUserSalon()) {
                // Check if salon user has the required permission
                $hasPermission = $user->salonPermissions()
                    ->whereHas('permission', function ($query) use ($permission) {
                        $query->where('key', $permission);
                    })
                    ->exists();

                if (!$hasPermission) {
                    MessageService::abort(403, 'messages.permission.error');
                }
            }
        }
    }
}
