<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\GroupService\CreateRequest;
use App\Http\Requests\Services\GroupService\UpdateRequest;
use App\Http\Resources\Services\GroupServiceResource;
 use App\Http\Permissions\Services\GroupServicePermission;
use App\Http\Services\Services\GroupServiceService;
use App\Services\ResponseService;

class GroupServiceController extends Controller
{
    protected $groupServiceService;

    public function __construct(GroupServiceService $groupServiceService)
    {
        $this->groupServiceService = $groupServiceService;
    }

    public function index()
    {
         $data = $this->groupServiceService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => GroupServiceResource::collection($data->items()),
            'meta' => ResponseService::meta($data),
        ]);
    }

    public function show($id)
    {
        $model = $this->groupServiceService->show($id);
        GroupServicePermission::canShow($model);

        return response()->json([
            'success' => true,
            'data' => new GroupServiceResource($model),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = GroupServicePermission::create($request->validated());
        
        $model = $this->groupServiceService->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.group_service.item_created_successfully'),
            'data' => new GroupServiceResource($model),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $model = $this->groupServiceService->show($id);
        GroupServicePermission::canUpdate($model);
        $model = $this->groupServiceService->update($model, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.group_service.item_updated_successfully'),
            'data' => new GroupServiceResource($model),
        ]);
    }

    public function destroy($id)
    {
        $model = $this->groupServiceService->show($id);
        GroupServicePermission::canDelete($model);
        $deleted = $this->groupServiceService->destroy($model);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.group_service.item_deleted_successfully')
                : trans('messages.group_service.failed_delete_item'),
        ]);
    }
}