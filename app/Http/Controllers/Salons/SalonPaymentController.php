<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salons\SalonPayment\CreateRequest;
use App\Http\Requests\Salons\SalonPayment\UpdateRequest;
use App\Http\Permissions\Salons\SalonPaymentPermission;
use App\Http\Services\Salons\SalonPaymentService;
use App\Http\Resources\Salons\SalonPaymentResource;
use App\Services\ResponseService;

class SalonPaymentController extends Controller
{
    public function __construct(protected SalonPaymentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->index(request()->all());
        return response()->json([
            'success' => true,
            'data' => SalonPaymentResource::collection($items->items()),
            'meta' => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->service->show($id);
        SalonPaymentPermission::canShow($item);
        return response()->json([
            'success' => true,
            'data' => new SalonPaymentResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = SalonPaymentPermission::create($request->validated());
        $item = $this->service->create($data);
        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_payment.item_created_successfully'),
            'data' => new SalonPaymentResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->service->show($id);
        SalonPaymentPermission::canUpdate($item, $request->validated());
        $item = $this->service->update($item, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_payment.item_updated_successfully'),
            'data' => new SalonPaymentResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->service->show($id);
        SalonPaymentPermission::canDelete($item);
        $deleted = $this->service->destroy($item);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.salon_payment.item_deleted_successfully')
                : trans('messages.salon_payment.failed_delete_item'),
        ]);
    }
}
