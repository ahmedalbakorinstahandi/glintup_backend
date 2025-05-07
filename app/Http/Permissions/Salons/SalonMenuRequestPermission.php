<?php

namespace App\Http\Permissions\Salons;

use App\Models\Users\User;

class SalonMenuRequestPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon_id);
        }

        return $query;
    }
}
