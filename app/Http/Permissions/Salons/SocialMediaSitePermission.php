<?php

namespace App\Http\Permissions\Salons;

use App\Models\Salons\SocialMediaSite;

class SocialMediaSitePermission
{
    public static function filterIndex($query)
    {
        return $query;
    }

    public static function canShow(SocialMediaSite $item)
    {
        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(SocialMediaSite $item, $data)
    {
        return true;
    }

    public static function canDelete(SocialMediaSite $item)
    {
        return true;
    }
}
