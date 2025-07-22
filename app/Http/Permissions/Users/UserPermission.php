<?php

namespace App\Http\Permissions\Users;

use App\Models\Users\User;
use App\Services\MessageService;

class UserPermission
{
    public static function filterIndex($query)
    {



        $query->where('role', 'customer')
            // ->where('is_active', 1)
        ;



        // if ($user->isSalonOwner()) {
        //     $salon = $user->salon();

        //     // staff
        //     $query->whereHas('staff', function ($q) use ($salon) {
        //         $q->where('salon_id', $salon->id);
        //     });
        // }

        return $query;
    }

    public static function canShow(User $user)
    {
        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(User $user, $data)
    {

        // $editor = User::auth();

        // if ($editor->isSalonOwner()) {
        //     $salon = $editor->salon();

        //     if ($user->salon()->id != $salon->id) {
        //         MessageService::abort(403, 'messages.permission_error');
        //     }
        // } elseif (!$editor->isAdmin()) {
        //     if ($user->id != $editor->id) {
        //         MessageService::abort(403, 'messages.permission_error');
        //     }
        // }


        return true;
    }

    public static function canDelete(User $user)
    {
        return true;
    }
}
