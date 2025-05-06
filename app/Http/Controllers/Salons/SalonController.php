<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Permissions\Salons\SalonPermission;
use App\Http\Requests\Salons\Salon\CreateRequest;
use App\Http\Requests\Salons\Salon\UpdateRequest;
use App\Http\Resources\Salons\SalonPermissionResource;
use App\Http\Resources\Salons\SalonResource;
use App\Http\Services\Salons\SalonService;
use App\Models\Users\User;
use App\Services\MessageService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class SalonController extends Controller
{
    protected $salonService;

    public function __construct(SalonService $salonService)

    {
        $this->salonService = $salonService;
    }

    public function getPermissions()
    {
        $permissions = $this->salonService->getPermissions();

        return response()->json([
            'success' => true,
            'data' => SalonPermissionResource::collection($permissions),
        ]);
    }

    // salon data
    public function getSalonData()
    {
        $salon = $this->salonService->getSalonData();

        return response()->json([
            'success' => true,
            'data' => new SalonResource($salon),
        ]);
    }

    public function index()
    {
        $salons = $this->salonService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => SalonResource::collection($salons->items()),
            'meta' => ResponseService::meta($salons),
        ]);
    }

    public function show($id)
    {
        $salon = $this->salonService->show($id);

        SalonPermission::canShow($salon);

        return response()->json([
            'success' => true,
            'data' => new SalonResource($salon),
        ]);
    }

    // public function create(CreateRequest $request)
    // {
    //     $data = SalonPermission::create($request->validated());
    //     $salon = $this->salonService->create($data);

    //     return response()->json([
    //         'success' => true,
    //         'message' => trans('messages.salon.item_created_successfully'),
    //         'data' => new SalonResource($salon),
    //     ]);
    // }

  
    public function update($id, UpdateRequest $request)
    {
        $salon = $this->salonService->show($id);


        SalonPermission::canUpdate($salon);

        $salon = $this->salonService->update($salon, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.salon.item_updated_successfully'),
            'data' => new SalonResource($salon),
        ]);
    }

    public function updateMySalon(UpdateRequest $request)
    {

        $user = User::auth();
 
        $id = $user->salon->id;

        $salon = $this->salonService->show($id);


        SalonPermission::canUpdate($salon);

        $salon = $this->salonService->update($salon, $request->validated());

        

        return response()->json([
            'success' => true,
            'message' => trans('messages.salon.item_updated_successfully'),
            'data' => new SalonResource($salon),
        ]);
    }

    public function destroy($id)
    {
        $salon = $this->salonService->show($id);
        SalonPermission::canDelete($salon);
        $deleted = $this->salonService->destroy($salon);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.salon.item_deleted_successfully')
                : trans('messages.salon.failed_delete_item'),
        ]);
    }
}
