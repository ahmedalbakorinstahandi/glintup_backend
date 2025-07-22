<?php

namespace App\Http\Controllers\Rewards;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rewards\LoyaltyPoint\CreateRequest;
use App\Http\Requests\Rewards\LoyaltyPoint\UpdateRequest;
use App\Http\Permissions\Rewards\LoyaltyPointPermission;
use App\Http\Services\Rewards\LoyaltyPointService;
use App\Http\Resources\Rewards\LoyaltyPointResource;
use App\Services\PermissionHelper;
use App\Services\ResponseService;

class LoyaltyPointController extends Controller
{

    protected $service;

    public function __construct(LoyaltyPointService $service)
    {
        PermissionHelper::checkSalonPermission('loyalty');
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->index(request()->all());
        return response()->json([
            'success' => true,
            'data' => LoyaltyPointResource::collection($items->items()),
            'meta' => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->service->show($id);
        LoyaltyPointPermission::canShow($item);
        return response()->json([
            'success' => true,
            'data' => new LoyaltyPointResource($item),
        ]);
    }

    public function receive($id)
    {
        $item = $this->service->show($id);

        LoyaltyPointPermission::canShow($item);

        $item = $this->service->receive($item);

        return response()->json([
            'success' => true,
            'message' => trans('messages.loyalty_point.item_received_successfully'),
            'data' => new LoyaltyPointResource($item),
        ]);
    }

    // public function create(CreateRequest $request)
    // {
    //     $data = LoyaltyPointPermission::create($request->validated());
    //     $item = $this->service->create($data);
    //     return response()->json([
    //         'success' => true,
    //         'message' => trans('messages.loyalty_point.item_created_successfully'),
    //         'data' => new LoyaltyPointResource($item),
    //     ]);
    // }

    // public function update($id, UpdateRequest $request)
    // {
    //     $item = $this->service->show($id);
    //     LoyaltyPointPermission::canUpdate($item, $request->validated());
    //     $item = $this->service->update($item, $request->validated());
    //     return response()->json([
    //         'success' => true,
    //         'message' => trans('messages.loyalty_point.item_updated_successfully'),
    //         'data' => new LoyaltyPointResource($item),
    //     ]);
    // }

    // public function destroy($id)
    // {
    //     $item = $this->service->show($id);
    //     LoyaltyPointPermission::canDelete($item);
    //     $deleted = $this->service->destroy($item);
    //     return response()->json([
    //         'success' => $deleted,
    //         'message' => $deleted
    //             ? trans('messages.loyalty_point.item_deleted_successfully')
    //             : trans('messages.loyalty_point.failed_delete_item'),
    //     ]);
    // }
}
