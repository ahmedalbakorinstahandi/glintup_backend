<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Permissions\Services\ServicePermission;
use App\Http\Requests\Services\Service\CreateRequest;
use App\Http\Requests\Services\Service\UpdateRequest;
use App\Http\Resources\Services\ServiceResource;
use App\Services\ResponseService;
use App\Services\Services\ServiceService;

class ServiceController extends Controller
{

    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index()
    {

        $services = $this->serviceService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services->items()),
            'meta' => ResponseService::meta($services),
        ]);
    }

    public function show($id)
    {
        ServicePermission::canShow();
        $service = $this->serviceService->show($id);
        return response()->json([
            'success' => true,
            'data' => new ServiceResource($service),
        ]);
    }

    public function create(CreateRequest $request)
    {
        ServicePermission::canCreate($request->validated());


        $service = $this->serviceService->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.service.item_created_successfully'),
            'data' => new ServiceResource($service),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        ServicePermission::canUpdate();


        $service = $this->serviceService->show($id);

        
        $service = $this->serviceService->update($service, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.service.item_updated_successfully'),
            'data' => new ServiceResource($service),
        ]);
    }

    public function destroy($id)
    {
        ServicePermission::canDelete();
        $service = $this->serviceService->show($id);
        $deleted = $this->serviceService->destroy($service);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.service.item_deleted_successfully')
                : trans('messages.service.failed_delete_item'),
        ]);
    }
}
