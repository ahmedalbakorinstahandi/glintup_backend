<?php

namespace App\Http\Permissions\Rewards;

use App\Models\Rewards\GiftCard;

class GiftCardPermission
{
    public static function filterIndex($query)
    {


        

        return $query;
    }

    public static function canShow(GiftCard $item)
    {
        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(GiftCard $item, $data)
    {
        return true;
    }

    public static function canDelete(GiftCard $item)
    {
        return true;
    }
}
