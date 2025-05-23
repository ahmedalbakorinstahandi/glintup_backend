<?php

namespace App\Http\Controllers\Booking;

use App\Http\Permissions\Booking\BookingPermission;
use App\Http\Requests\Booking\Booking\CreateNewRequest;
use App\Http\Requests\Booking\Booking\CreateRequest;
use App\Http\Requests\Booking\BookingService\GetAvailableSlotsRequest;
use App\Http\Resources\Booking\BookingResource;
use App\Http\Services\Booking\BookingService;
use App\Models\Services\Service;
use App\Services\BookingAvailabilityService;
use App\Services\MessageService;

class BookingNewController
{

    protected $bookingService;


    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }



    public function create(CreateNewRequest $request)
    {
        $data = BookingPermission::create($request->validated());

        $services = $data['services'] ?? [];

        foreach ($services as $item) {
            $service = Service::where('id', $item['service_id'])
                ->where('salon_id', $data['salon_id'])
                ->first();

            $bookingAvailabilityService = new BookingAvailabilityService();

            if ($service) {
                $bookingAvailabilityService->isSlotOptionValid($data['date'], $item['start_time'], $service);
            } else {
                MessageService::abort(404, 'messages.service.item_not_found');
            }
        }


        $booking = $this->bookingService->createNew($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.booking.item_created_successfully'),
            'data' => new BookingResource($booking),
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
