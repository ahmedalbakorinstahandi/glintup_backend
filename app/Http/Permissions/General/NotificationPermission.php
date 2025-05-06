<?php


namespace App\Http\Permissions\General;

use App\Models\General\Notification;
use App\Models\Users\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;

class NotificationPermission
{
    public static function filterIndex($query)
    {
        if (Auth::check()) {
            $user = User::auth();

            $query->where('user_id', $user->id)->orWhereNull('user_id');
        } else {
            $query->whereNull('user_id');
        }


        return $query;
    }

    public static function canShow(Notification $notification)
    {
        $user = User::auth();

        if ($notification->user_id !== $user->id) {
            MessageService::abort(403, 'messages.permission_error');
        }

        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(Notification $notification, $data)
    {
        return true;
    }

    public static function canDelete(Notification $notification)
    {
        $user = User::auth();

        if ($notification->user_id !== $user->id) {
            MessageService::abort(403, 'messages.permission_error');
        }

        return true;
    }
}
