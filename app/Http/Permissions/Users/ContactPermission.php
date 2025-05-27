<?php

namespace App\Http\Permissions\Users;

use App\Models\Users\Contact;
use App\Models\Users\User;
use App\Services\MessageService;

class ContactPermission
{

    public static function filterIndex($query)
    {

        $user = User::auth();

        $query->where('user_id', $user->id);

        return $query;
    }

    public static function canShow(Contact $item)
    {

        $user = User::auth();

        if ($item->user_id !== $user->id) {
            MessageService::abort(503, 'messages.permission_error');
        }

        return true;
    }

    public static function create($data)
    {

        $user = User::auth();

        $data['user_id'] = $user->id;

        return $data;
    }

    public static function canUpdate(Contact $item, $data)
    {
        $user = User::auth();

        if ($item->user_id !== $user->id) {
            MessageService::abort(503, 'messages.permission_error');
        }

        $data['user_id'] = $user->id;

        return $data;
    }

    public static function canDelete(Contact $item)
    {
        $user = User::auth();

        if ($item->user_id !== $user->id) {
            MessageService::abort(503, 'messages.permission_error');
        }

        return true;
    }
}
