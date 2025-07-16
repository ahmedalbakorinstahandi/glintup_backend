<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Permissions\Salons\SalonStaffPermission;
use App\Http\Requests\Salons\SalonStaff\CreateRequest;
use App\Http\Requests\Salons\SalonStaff\UpdatePermissionsRequest;
use App\Http\Requests\Salons\SalonStaff\UpdateRequest;
use App\Http\Services\Salons\SalonStaffService;
use App\Http\Resources\Salons\SalonStaffResource;
use App\Services\ResponseService;

class SalonStaffController extends Controller
{
    public function __construct(protected SalonStaffService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->index(request()->all());
        return response()->json([
            'success' => true,
            'data' => SalonStaffResource::collection($items->items()),
            'meta' => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->service->show($id);
        SalonStaffPermission::canShow($item);
        return response()->json([
            'success' => true,
            'data' => new SalonStaffResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = SalonStaffPermission::create($request->validated());

        $item = $this->service->create($data);
        
        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_staff.item_created_successfully'),
            'data' => new SalonStaffResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->service->show($id);
        SalonStaffPermission::canUpdate($item, $request->validated());
        $item = $this->service->update($item, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_staff.item_updated_successfully'),
            'data' => new SalonStaffResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->service->show($id);
        SalonStaffPermission::canDelete($item);
        $deleted = $this->service->destroy($item);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.salon_staff.item_deleted_successfully')
                : trans('messages.salon_staff.failed_delete_item'),
        ]);
    }

    public function updatePermissions($id, UpdatePermissionsRequest $request)
    {
        $item = $this->service->show($id);

        $this->service->updatePermissions($item, $request->validated());
        
        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_staff.permissions_updated_successfully'),
        ]);
    }

    //     public function updatePermissions(SalonStaff $staff, array $data)
    // {
    //     $userId = $staff->user_id;
    //     $salonId = $staff->salon_id;
    //     $newPermissions = collect($data['permissions'])->unique()->values()->all(); // مصفوفة نظيفة

    //     // جلب الصلاحيات الحالية من قاعدة البيانات
    //     $existingPermissions = UserSalonPermission::where('user_id', $userId)
    //         ->where('salon_id', $salonId)
    //         ->get();

    //     // حذف أي صلاحية حالية غير موجودة في الجديدة
    //     foreach ($existingPermissions as $permission) {
    //         if (!in_array($permission->permission_id, $newPermissions)) {
    //             $permission->delete();
    //         }
    //     }

    //     // إضافة أي صلاحية جديدة غير موجودة حاليًا
    //     foreach ($newPermissions as $permissionId) {
    //         $exists = $existingPermissions->firstWhere('permission_id', $permissionId);
    //         if (!$exists) {
    //             UserSalonPermission::create([
    //                 'user_id' => $userId,
    //                 'salon_id' => $salonId,
    //                 'permission_id' => $permissionId,
    //             ]);
    //         }
    //     }
    // }

}
