<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\SalonPermission;
use App\Models\Salons\UserSalonPermission;
use App\Models\Users\User;

class SalonService
{
    public function getPermissions()
    {
        $user = User::auth();

        $userPermissions = UserSalonPermission::where('user_id', $user->id)
            ->with('permission')
            ->get();

        $permissions = SalonPermission::whereIn('id', $userPermissions->pluck('permission_id'))->get();

        return $permissions;
    }
}
