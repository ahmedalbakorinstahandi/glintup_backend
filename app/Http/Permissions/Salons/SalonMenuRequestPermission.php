<?php

namespace App\Http\Permissions\Salons;

use App\Models\Users\User;
use App\Services\MessageService;

class SalonMenuRequestPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon->id);
        }

        return $query;
    }

    public static function show($request)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($request->salon_id != $user->salon->id) {
                MessageService::abort(503, 'messages.permission_error');
            }
        }

        return $request;
    }
}
