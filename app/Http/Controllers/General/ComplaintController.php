<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Complaints\CreateRequest;
use App\Http\Requests\General\Complaints\UpdateRequest;
use App\Http\Permissions\General\ComplaintPermission;
use App\Http\Services\General\ComplaintService;
use App\Http\Resources\General\ComplaintResource;
use App\Services\PermissionHelper;
use App\Services\ResponseService;

class ComplaintController extends Controller
{
    protected $service;
    public function __construct(ComplaintService $service)
    {

        PermissionHelper::checkAdminPermission('complaints');

        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->index(request()->all());

        return response()->json([
            'success' => true,
            'data'    => ComplaintResource::collection($items->items()),
            'meta'    => ResponseService::meta($items),
        ]);
    }

    public function show($id)
    {
        $item = $this->service->show($id);

        ComplaintPermission::canShow($item);

        return response()->json([
            'success' => true,
            'data'    => new ComplaintResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = ComplaintPermission::create($request->validated());
        $item = $this->service->create($data);
        return response()->json([
            'success' => true,
            'message' => trans('messages.complaint.item_created_successfully'),
            'data'    => new ComplaintResource($item),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $item = $this->service->show($id);

        // ComplaintPermission::canUpdate($item, $request->validated());

        $item = $this->service->update($item, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.complaint.item_updated_successfully'),
            'data'    => new ComplaintResource($item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->service->show($id);
        ComplaintPermission::canDelete($item);
        $deleted = $this->service->destroy($item);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.complaint.item_deleted_successfully')
                : trans('messages.complaint.failed_delete_item'),
        ]);
    }
}
