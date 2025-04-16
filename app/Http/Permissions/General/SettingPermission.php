<?php

namespace App\Http\Permissions\General;

use App\Models\General\Setting;

class SettingPermission
{
    public static function filterIndex($query)
    {
        return $query;
    }

    public static function canShow(Setting $item)
    {
        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(Setting $item, $data)
    {
        return true;
    }

    public static function canDelete(Setting $item)
    {
        return true;
    }
}
