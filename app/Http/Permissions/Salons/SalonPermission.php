<?php

namespace App\Http\Permissions\Salons;

use App\Models\Salons\Salon;

class SalonPermission
{
    public static function filterIndex($request)
    {

        return $request;
    }
    public static function canShow(Salon $salon)
    {
        return true;
    }
    public static function create($data)
    {
        return $data;
    }
    public static function canUpdate(Salon $salon)
    {
        return true;
    }
    public static function canDelete(Salon $salon)
    {
        return true;
    }
}
