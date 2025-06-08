<?php

namespace App\Http\Permissions\General;

use App\Models\Users\User;

class AddressPermission
{
    public static function filterIndex($query)
    {

        $user = User::auth();

        $query->where('addressable_id', $user->id);
        $query->where('addressable_type', User::class);


        return $query;
    }

    public static function create($data)
    {
        $user = User::auth();

        
        $data['addressable_id'] = $user->id;
        $data['addressable_type'] = User::class;
        return $data;
    }
}