<?php


namespace App\Http\Permissions\Services;

use App\Models\Services\Service;


class ServicePermission
{
    public static function filterIndex($query)
    {
        return $query;
    }

    // can show
    public static function canShow($data)
    {



        return true;
    }

    // can create
    public static function canCreate()
    {
        return true;
    }

    // can update
    public static function canUpdate()
    {
        return true;
    }

    // can delete
    public static function canDelete()
    {
        return true;
    }
}
