<?php

namespace App\Http\Controllers\Salons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Salons\SalonHoliday\CreateRequest;
use App\Http\Requests\Salons\SalonHoliday\UpdateRequest;
use App\Http\Permissions\Salons\SalonHolidayPermission;
use App\Http\Services\Salons\SalonHolidayService;
use App\Http\Resources\Salons\SalonHolidayResource;
use App\Services\ResponseService;

class SalonHolidayController extends Controller
{
    protected $holidayService;

    public function __construct(SalonHolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    public function index()
    {
        $holidays = $this->holidayService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => SalonHolidayResource::collection($holidays->items()),
            'meta' => ResponseService::meta($holidays),
        ]);
    }

    public function show($id)
    {
        $holiday = $this->holidayService->show($id);

        SalonHolidayPermission::canShow($holiday);

        return response()->json([
            'success' => true,
            'data' => new SalonHolidayResource($holiday),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = SalonHolidayPermission::create($request->validated());

        $holiday = $this->holidayService->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_holiday.item_created_successfully'),
            'data' => new SalonHolidayResource($holiday),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $holiday = $this->holidayService->show($id);

        SalonHolidayPermission::canUpdate($holiday, $request->validated());

        $holiday = $this->holidayService->update($holiday, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.salon_holiday.item_updated_successfully'),
            'data' => new SalonHolidayResource($holiday),
        ]);
    }

    public function destroy($id)
    {
        $holiday = $this->holidayService->show($id);

        SalonHolidayPermission::canDelete($holiday);

        $deleted = $this->holidayService->destroy($holiday);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.salon_holiday.item_deleted_successfully')
                : trans('messages.salon_holiday.failed_delete_item'),
        ]);
    }
}
