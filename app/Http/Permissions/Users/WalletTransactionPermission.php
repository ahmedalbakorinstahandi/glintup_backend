<?php

namespace App\Http\Permissions\Users;

use App\Models\Users\User;
use App\Models\Users\WalletTransaction;
use App\Services\MessageService;

class WalletTransactionPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function canShow(WalletTransaction $item)
    {

        $user = User::auth();

        if (!$user->isAdmin()) {
            if ($item->user_id != $user->id) {
                MessageService::abort(503, 'messages.permission_error');
            }
        }

        return true;
    }

    public static function create($data)
    {
        return $data;
    }

    public static function canUpdate(WalletTransaction $item, $data)
    {
        return true;
    }

    public static function canDelete(WalletTransaction $item)
    {

        return true;
    }
}
