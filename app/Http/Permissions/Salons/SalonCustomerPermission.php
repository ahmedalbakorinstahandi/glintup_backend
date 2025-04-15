<?php


namespace App\Http\Permissions\Salons;

use App\Models\Salons\SalonCustomer;
use App\Models\Users\User;

class SalonCustomerPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon?->id);
        }

        if ($user->isCustomer()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function canShow(SalonCustomer $item)
    {
        return true;
    }

    public static function create($data)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $data['salon_id'] = $user->salon?->id;
        }

        return $data;
    }

    public static function canUpdate(SalonCustomer $item, $data)
    {
        return true;
    }

    public static function canDelete(SalonCustomer $item)
    {
        return true;
    }
}
