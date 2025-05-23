<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Permissions\Services\ServicePermission;
use App\Http\Requests\Booking\BookingService\GetAvailableSlotsRequest;
use App\Http\Requests\Services\Service\CreateRequest;
use App\Http\Requests\Services\Service\UpdateRequest;
use App\Http\Resources\Services\ServiceResource;
use App\Services\ResponseService;
use App\Http\Services\Services\ServiceService;
use App\Models\Services\Service;
use App\Services\BookingAvailabilityService;
use App\Services\MessageService;

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
        $service = $this->serviceService->show($id);

        ServicePermission::canShow($service);

        return response()->json([
            'success' => true,
            'data' => new ServiceResource($service),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data =  ServicePermission::create($request->validated());


        $service = $this->serviceService->create($data);


        return response()->json([
            'success' => true,
            'message' => trans('messages.service.item_created_successfully'),
            'data' => new ServiceResource($service),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $service = $this->serviceService->show($id);

        ServicePermission::canUpdate($service, $request->validated());

        $service = $this->serviceService->update($service, $request->validated());
        return response()->json([
            'success' => true,
            'message' => trans('messages.service.item_updated_successfully'),
            'data' => new ServiceResource($service),
        ]);
    }

    public function destroy($id)
    {
        $service = $this->serviceService->show($id);
        ServicePermission::canDelete($service);
        $deleted = $this->serviceService->destroy($service);
        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.service.item_deleted_successfully')
                : trans('messages.service.failed_delete_item'),
        ]);
    }

    public function getAvailableTimes(GetAvailableSlotsRequest $request, $id)
    {
        $bookingAvailabilityService = new BookingAvailabilityService();

        $service = Service::where('id', $id)
            ->where('salon_id', $request->salon_id)
            ->first();


        if ($service) {
            $availableSlots = $bookingAvailabilityService->getAvailableSlots($request->date, $service);

            return response()->json([
                'success' => true,
                'data' => $availableSlots,
            ]);
        } else {
            MessageService::abort(404, 'messages.service.item_not_found');
        }
    }
}
