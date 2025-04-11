<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salons\WorkingHour\CreateRequest;
use App\Http\Requests\Salons\WorkingHour\UpdateRequest;
use App\Http\Permissions\Salons\WorkingHourPermission;
use App\Http\Services\Salons\WorkingHourService;
use App\Http\Resources\Salons\WorkingHourResource;
use App\Services\ResponseService;

class WorkingHourController extends Controller
{
    protected $workingHourService;

    public function __construct(WorkingHourService $workingHourService)
    {
        $this->workingHourService = $workingHourService;
    }

    public function index()
    {
        $data = $this->workingHourService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => WorkingHourResource::collection($data->items()),
            'meta' => ResponseService::meta($data),
        ]);
    }

    public function show($id)
    {
        $item = $this->workingHourService->show($id);

        WorkingHourPermission::canShow($item);

        return response()->json([
            'success' => true,
            'data' => new WorkingHourResource($item),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $validated = WorkingHourPermission::create($request->validated());

        $data = $this->workingHourService->create($validated);

        return response()->json([
            'success' => true,
            'message' => trans('messages.working_hour.item_created_successfully'),
            'data' => new WorkingHourResource($data),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $data = $this->workingHourService->show($id);

        WorkingHourPermission::canUpdate($data, $request->validated());

        $data = $this->workingHourService->update($data, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.working_hour.item_updated_successfully'),
            'data' => new WorkingHourResource($data),
        ]);
    }

    public function destroy($id)
    {
        $data = $this->workingHourService->show($id);

        WorkingHourPermission::canDelete($data);

        $deleted = $this->workingHourService->destroy($data);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.working_hour.item_deleted_successfully')
                : trans('messages.working_hour.failed_delete_item'),
        ]);
    }
}
