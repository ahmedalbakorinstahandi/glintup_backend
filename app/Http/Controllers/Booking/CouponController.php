<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\Coupon\CreateRequest;
use App\Http\Requests\Booking\Coupon\UpdateRequest;
use App\Http\Permissions\Booking\CouponPermission;
use App\Http\Services\Booking\CouponService;
use App\Http\Resources\Booking\CouponResource;
use App\Http\Services\Salons\SalonService;
use App\Services\PermissionHelper;
use App\Services\ResponseService;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        PermissionHelper::checkSalonPermission('coupons');
        $this->couponService = $couponService;
    }

    public function index()
    {
        $data = $this->couponService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => CouponResource::collection($data->items()),
            'meta' => ResponseService::meta($data),
        ]);
    }

    public function show($id)
    {
        $data = $this->couponService->show($id);

        CouponPermission::canShow($data);

        return response()->json([
            'success' => true,
            'data' => new CouponResource($data),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $validated = CouponPermission::create($request->validated());

        $data = $this->couponService->create($validated);

        return response()->json([
            'success' => true,
            'message' => trans('messages.coupon.item_created_successfully'),
            'data' => new CouponResource($data),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $data = $this->couponService->show($id);

        CouponPermission::canUpdate($data, $request->validated());

        $data = $this->couponService->update($data, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.coupon.item_updated_successfully'),
            'data' => new CouponResource($data),
        ]);
    }

    public function destroy($id)
    {
        $data = $this->couponService->show($id);

        CouponPermission::canDelete($data);

        $deleted = $this->couponService->destroy($data);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.coupon.item_deleted_successfully')
                : trans('messages.coupon.failed_delete_item'),
        ]);
    }


    // check if the coupon is valid for current user and return the coupon and salon selected
    public function check($id, $code)
    {
        $salonService =  new SalonService();

        $salon = $salonService->show($id);

        $data = $this->couponService->showByCode($salon, $code);

        return response()->json([
            'success' => true,
            'data' => new CouponResource($data),
            'message' => trans('messages.coupon.item_checked_successfully'),
        ]);
    }
}
