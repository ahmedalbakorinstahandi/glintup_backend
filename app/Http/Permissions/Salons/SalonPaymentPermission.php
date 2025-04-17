<?php

namespace App\Http\Permissions\Salons;

use App\Models\Salons\SalonPayment;
use App\Models\Users\User;
use App\Services\MessageService;

class SalonPaymentPermission
{
    public static function filterIndex($query)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon->id);
        }

        if ($user->isCustomer()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function canShow(SalonPayment $item)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($item->salon_id != $user->salon->id) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(SalonPayment $item, $data)
    {
        return true;
    }

    public static function canDelete(SalonPayment $item)
    {
        return true;
    }
}
