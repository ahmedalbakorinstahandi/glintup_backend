<?php

namespace App\Services;

use App\Models\Users\User;
use App\Services\MessageService;

class PermissionHelper
{
    public static function checkAdminPermission($permission)
    {
        $user = User::auth();

        // Check if admin has the required permission
        $hasPermission = $user->adminPermissions()
            ->where('key', $permission)
            ->exists();

        if ($user && $user->isAdmin() && !$hasPermission) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }
}
