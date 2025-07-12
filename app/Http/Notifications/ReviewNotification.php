<?php

namespace App\Http\Notifications;

use App\Models\Users\User;
use App\Services\FirebaseService;

class ReviewNotification
{
    public static function newReview($review)
    {
        $user = $review->user;

        $title = 'notifications.admin.review.new_review';
        $body = 'notifications.admin.review.new_review_body';

        $data = [
            'review_id' => $review->id,
            'full_user_name' => $user->first_name . ' ' . $user->last_name,
            'review_content' => strlen($review->comment) > 100 ? substr($review->comment, 0, 100) . '...' : $review->comment,
        ];

        $pemissionKey = 'reviews';

        $users = User::where('role', 'admin')->whereHas('adminPermissions', function ($query) use ($pemissionKey) {
            $query->where('key', $pemissionKey);
        })->get();

        FirebaseService::sendToTopicAndStorage(
            'role-admin',
            $users->pluck('id'),
            [
                'id' => $review->id,
                'type' => 'Review',
            ],
            $title,
            $body,
            $data,
            $data
        );
    }
}
