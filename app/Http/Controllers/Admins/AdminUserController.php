<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\User\{CreateRequest, UpdateRequest, UpdatePermissionsRequest};
use App\Http\Resources\Admins\AdminPermissionResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Services\Admins\AdminUserService;
use App\Models\Admins\AdminPermission;
use App\Models\User;
use App\Services\ResponseService;

class AdminUserController extends Controller
{
    public function __construct(protected AdminUserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->index(request()->all());
        return response()->json([
            'success' => true,
            'data' => UserResource::collection($items->items()),
            'meta' => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->service->show($id);
        return response()->json([
            'success' => true,
            'data' => new UserResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $item = $this->service->create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => trans('messages.admin_users.item_created_successfully'),
            'data' => new UserResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->service->show($id);
        $item = $this->service->update($item, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.admin_users.item_updated_successfully'),
            'data' => new UserResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->service->show($id);

        $deleted = $this->service->destroy($item);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.admin_users.item_deleted_successfully')
                : trans('messages.admin_users.failed_delete_item'),
        ]);
    }

    public function updatePermissions($id, UpdatePermissionsRequest $request)
    {
        $item = $this->service->show($id);

        $this->service->updatePermissions($item, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.admin_users.permissions_updated_successfully'),
        ]);
    }


    public function getPermissions()
    {
        $permissions = AdminPermission::all();


        return response()->json([
            'success' => true,
            'data' => AdminPermissionResource::collection($permissions),
        ]);
    }
}
