<?php

namespace App\Http\Permissions\Services;

use App\Models\Services\GroupService;
use App\Models\Users\User;
use App\Services\MessageService;

class GroupServicePermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon->id)->orWhereNull('salon_id');
        }

        return $query;
    }
    public static function canShow(GroupService $model)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($model->salon_id != $user->salon->id && $model->salon_id != null) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }

    public static function create($data)
    {

        $user = User::auth();

        if ($user->isUserSalon()) {
            $data['salon_id'] = $user->salon->id;
        }

        return $data;
    }

    public static function canUpdate(GroupService $model)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($model->salon_id != $user->salon->id && $model->salon_id != null) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }
    public static function canDelete(GroupService $model)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            if ($model->salon_id != $user->salon->id && $model->salon_id != null) {
                MessageService::abort(403, 'messages.permission_error');
            }
        }

        return true;
    }
    
}
