<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salons\SalonCustomer\CreateRequest;
use App\Http\Requests\Salons\SalonCustomer\UpdateRequest;
use App\Http\Permissions\Salons\SalonCustomerPermission;
use App\Http\Services\Salons\SalonCustomerService;
use App\Http\Resources\Salons\SalonCustomerResource;
use App\Services\ResponseService;

class SalonCustomerController extends Controller
{
    protected $service;

    public function __construct(SalonCustomerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => SalonCustomerResource::collection($items->items()),
            'meta' => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->service->show($id);

        SalonCustomerPermission::canShow($item);

        return response()->json([
            'success' => true,
            'data' => new SalonCustomerResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = SalonCustomerPermission::create($request->validated());

        $item = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_customer.item_created_successfully'),
            'data' => new SalonCustomerResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->service->show($id);

        SalonCustomerPermission::canUpdate($item, $request->validated());

        $item = $this->service->update($item, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_customer.item_updated_successfully'),
            'data' => new SalonCustomerResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->service->show($id);

        SalonCustomerPermission::canDelete($item);

        $deleted = $this->service->destroy($item);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.salon_customer.item_deleted_successfully')
                : trans('messages.salon_customer.failed_delete_item'),
        ]);
    }
}
