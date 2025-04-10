<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\Review\CreateRequest;
use App\Http\Requests\Services\Review\UpdateRequest;
use App\Http\Permissions\Services\ReviewPermission;
use App\Http\Services\Services\ReviewService;
use App\Http\Resources\Services\ReviewResource;
use App\Services\ResponseService;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index()
    {
        $reviews = $this->reviewService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews->items()),
            'meta' => ResponseService::meta($reviews),
        ]);
    }

    public function show($id)
    {
        $review = $this->reviewService->show($id);

        ReviewPermission::canShow($review);

        return response()->json([
            'success' => true,
            'data' => new ReviewResource($review),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = ReviewPermission::create($request->validated());

        $review = $this->reviewService->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.review.item_created_successfully'),
            'data' => new ReviewResource($review),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $review = $this->reviewService->show($id);

        ReviewPermission::canUpdate($review, $request->validated());

        $review = $this->reviewService->update($review, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.review.item_updated_successfully'),
            'data' => new ReviewResource($review),
        ]);
    }

    public function destroy($id)
    {
        $review = $this->reviewService->show($id);

        ReviewPermission::canDelete($review);

        $deleted = $this->reviewService->destroy($review);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.review.item_deleted_successfully')
                : trans('messages.review.failed_delete_item'),
        ]);
    }
}
