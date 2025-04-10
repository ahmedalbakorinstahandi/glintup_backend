<?php

namespace App\Http\Permissions\Users;

use App\Models\Users\User;
use App\Services\MessageService;

class UserPermission
{
    public static function filterIndex($query)
    {
        
        $query->where('role', 'customer')->where('is_active', 1);

        return $query;
    }

    public static function canShow(User $user)
    {
        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(User $user, $data)
    {
        return true;
    }

    public static function canDelete(User $user)
    {
        return true;
    }
}
