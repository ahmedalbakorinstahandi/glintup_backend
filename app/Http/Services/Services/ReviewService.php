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

        return FilterService::applyFilters(
            $query,
            $data,
            ['comment', 'salon_reply', 'salon_report'],
            ['rating'],
            ['created_at'],
            ['user_id', 'salon_id'],
            ['id']
        );
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
}
