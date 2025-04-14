<?php

namespace App\Http\Permissions\Salons;

use App\Models\Salons\SalonHoliday;
use App\Models\Users\User;
use App\Services\MessageService;

class SalonHolidayPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon?->id);
        }

        return $query;
    }

    public static function canShow(SalonHoliday $holiday)
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

    public static function canUpdate(SalonHoliday $holiday, $data)
    {
        return true;
    }

    public static function canDelete(SalonHoliday $holiday)
    {
        return true;
    }
}
