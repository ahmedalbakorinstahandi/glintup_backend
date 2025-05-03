<?php

namespace App\Http\Services\Services;

use App\Models\Services\Review;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Services\ReviewPermission;

class ReviewService
{
    public function index($data)
    {
        $query = Review::query()->with(['user', 'salon']);

        $query = ReviewPermission::filterIndex($query);

        $query = FilterService::applyFilters(
            $query,
            $data,
            ['comment', 'salon_reply', 'salon_report'],
            ['rating'],
            ['created_at'],
            ['user_id', 'salon_id', 'rating'],
            ['id'],
            false,
        );

        $reviews = $query->get();

        $totalReviews = $reviews->count();
        $averageRating = $reviews->avg('rating');
        $pendingReviews = $reviews->whereNotNull('salon_reply')->where('is_reviewed', false)->count();
        $negativeReviews = $reviews->where('rating', '<', 3)->count();


        return [
            'info' => [
                'total_reviews' => $totalReviews,
                'average_rating' => $averageRating,
                'pending_reviews' => $pendingReviews,
                'negative_reviews' => $negativeReviews,
            ],
            'data' => $reviews,
        ];

    }

    public function show($id)
    {
        $review = Review::with(['user', 'salon'])->find($id);

        if (!$review) {
            MessageService::abort(404, 'messages.review.item_not_found');
        }

        return $review;
    }

    public function create($validatedData)
    {
        return Review::create($validatedData);
    }

    public function update($review, $validatedData)
    {
        $review->update($validatedData);
        return $review;
    }

    public function destroy($review)
    {
        return $review->delete();
    }


    // replay to review
    public function reply($review, $validatedData)
    {


        $salon_reply = $validatedData['salon_reply'];



        $review->update(
            [
                'salon_reply' => $salon_reply,
                'salon_reply_at' => now(),
            ]
        );

        //TODO send notification to user

        return $review;
    }

    // report review
    public function report($review, $validatedData)
    {

        $reason_for_report = $validatedData['reason_for_report'];
        $salon_report = $validatedData['salon_report'];

        $review->update([
            'salon_report' => $salon_report,
            'reason_for_report' => $reason_for_report,
            'salon_reported_at' => now(),
        ]);

        //TODO send notification to user

        return $review;
    }
}
