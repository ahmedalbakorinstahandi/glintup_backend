<?php

namespace App\Http\Permissions\Services;

use App\Models\Services\Review;
use App\Models\Users\User;
use App\Services\MessageService;

class ReviewPermission
{
    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user->isUserSalon()) {
            $query->where('salon_id', $user->salon?->id);
        }

        // if ($user->isCustomer()) {
        //     $query->where('user_id', $user->id);
        // }

        return $query;
    }

    public static function canShow(Review $review)
    {
        return true;
    }

    public static function create($data)
    {
        $user = User::auth();
        $data['user_id'] = $user->id;
        return $data;
    }

    public static function canUpdate(Review $review, $data)
    {
        return true;
    }

    public static function canDelete(Review $review)
    {
        return true;
    }
}
