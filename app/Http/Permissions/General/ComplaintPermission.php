<?php

namespace App\Http\Permissions\General;

use App\Models\General\Complaint;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;

class ComplaintPermission
{
    public static function filterIndex($query)
    {



        return $query;
    }

    public static function canShow(Complaint $item)
    {
        return true;
    }

    public static function create($data)
    {

        if (Auth::check()) {
            $data['user_id'] = User::auth()->id;
        }

        return $data;
    }

    public static function canUpdate(Complaint $item, $data)
    {

        

        return true;
    }

    public static function canDelete(Complaint $item)
    {
        return true;
    }
}
