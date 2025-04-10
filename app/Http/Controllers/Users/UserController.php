<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\User\CreateRequest;
use App\Http\Requests\Users\User\UpdateRequest;
use App\Http\Permissions\Users\UserPermission;
use App\Http\Services\Users\UserService;
use App\Http\Resources\Users\UserResource;
use App\Services\ResponseService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users->items()),
            'meta' => ResponseService::meta($users),
        ]);
    }

    public function show($id)
    {
        $user = $this->userService->show($id);

        UserPermission::canShow($user);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = UserPermission::create($request->validated());

        $user = $this->userService->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.user.item_created_successfully'),
            'data' => new UserResource($user),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $user = $this->userService->show($id);

        UserPermission::canUpdate($user, $request->validated());

        $user = $this->userService->update($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.user.item_updated_successfully'),
            'data' => new UserResource($user),
        ]);
    }

    public function destroy($id)
    {
        $user = $this->userService->show($id);

        UserPermission::canDelete($user);

        $deleted = $this->userService->destroy($user);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.user.item_deleted_successfully')
                : trans('messages.user.failed_delete_item'),
        ]);
    }
}
