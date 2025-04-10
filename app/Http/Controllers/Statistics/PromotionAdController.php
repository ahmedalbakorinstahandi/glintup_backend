<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\PromotionAd\CreateRequest;
use App\Http\Requests\Statistics\PromotionAd\UpdateRequest;
use App\Http\Permissions\Statistics\PromotionAdPermission;
use App\Http\Services\Statistics\PromotionAdService;
use App\Http\Resources\Statistics\PromotionAdResource;
use App\Services\ResponseService;

class PromotionAdController extends Controller
{
    protected $promotionAdService;

    public function __construct(PromotionAdService $promotionAdService)
    {
        $this->promotionAdService = $promotionAdService;
    }

    public function index()
    {
        $ads = $this->promotionAdService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => PromotionAdResource::collection($ads->items()),
            'meta' => ResponseService::meta($ads),
        ]);
    }

    public function show($id)
    {
        $ad = $this->promotionAdService->show($id);

        PromotionAdPermission::canShow($ad);

        return response()->json([
            'success' => true,
            'data' => new PromotionAdResource($ad),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = PromotionAdPermission::create($request->validated());

        $ad = $this->promotionAdService->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.promotion_ad.item_created_successfully'),
            'data' => new PromotionAdResource($ad),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $ad = $this->promotionAdService->show($id);

        PromotionAdPermission::canUpdate($ad, $request->validated());

        $ad = $this->promotionAdService->update($ad, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.promotion_ad.item_updated_successfully'),
            'data' => new PromotionAdResource($ad),
        ]);
    }

    public function destroy($id)
    {
        $ad = $this->promotionAdService->show($id);

        PromotionAdPermission::canDelete($ad);

        $deleted = $this->promotionAdService->destroy($ad);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.promotion_ad.item_deleted_successfully')
                : trans('messages.promotion_ad.failed_delete_item'),
        ]);
    }
}
