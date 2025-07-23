<?php

namespace App\Http\Loggers;

use App\Models\Users\User;
use App\Models\Services\Review;
use App\Services\ActivityLogHelper;

class ReviewLogger
{
    public static function logReply(Review $review)
    {
        if ($review->wasChanged('salon_reply') && $review->getOriginal('salon_reply') === null) {
            $user = User::auth();

            $description = [
                'ar' => "تم الرد على تقييم المستخدم ID: {$review->user_id} للصالون ID: {$review->salon_id}",
                'en' => "A reply was added to the review by user ID: {$review->user_id} for salon ID: {$review->salon_id}",
            ];

            ActivityLogHelper::createActivityLog(
                $user->id,
                'review.replied',
                $description,
                get_class($review),
                $review->id,
                'salon-' . $review->salon_id
            );
        }
    }

    public static function logReport(Review $review)
    {
        // فقط لو تم التبليغ لأول مرة
        if (
            $review->wasChanged('salon_report') &&
            $review->getOriginal('salon_report') === null
        ) {
            $user = User::auth();

            $reasonMap = [
                'inappropriate_content' => ['ar' => 'محتوى غير لائق', 'en' => 'Inappropriate content'],
                'spam'                  => ['ar' => 'سبام', 'en' => 'Spam'],
                'fake_review'           => ['ar' => 'تقييم مزيف', 'en' => 'Fake review'],
                'other'                 => ['ar' => 'أخرى', 'en' => 'Other'],
            ];

            $reason = $review->reason_for_report ?? 'other';
            $reasonAr = $reasonMap[$reason]['ar'] ?? 'غير محدد';
            $reasonEn = $reasonMap[$reason]['en'] ?? 'Unspecified';

            $description = [
                'ar' => "تم تبليغ تقييم المستخدم ID: {$review->user_id} في الصالون ID: {$review->salon_id} بسبب: {$reasonAr}",
                'en' => "A review by user ID: {$review->user_id} for salon ID: {$review->salon_id} was reported due to: {$reasonEn}",
            ];

            ActivityLogHelper::createActivityLog(
                $user->id,
                'review.reported',
                $description,
                get_class($review),
                $review->id,
                'salon-' . $review->salon_id
            );
        }
    }
}
