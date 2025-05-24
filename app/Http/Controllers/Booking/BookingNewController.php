<?php

namespace App\Http\Controllers\Booking;

use App\Http\Permissions\Booking\BookingPermission;
use App\Http\Requests\Booking\Booking\CreateFromUserNewRequest;
use App\Http\Requests\Booking\Booking\CreateFromUserRequest;
use App\Http\Requests\Booking\Booking\CreateNewRequest;
use App\Http\Requests\Booking\Booking\CreateRequest;
use App\Http\Requests\Booking\BookingService\GetAvailableSlotsRequest;
use App\Http\Resources\Booking\BookingResource;
use App\Http\Services\Booking\BookingService;
use App\Models\Services\Service;
use App\Services\BookingAvailabilityService;
use App\Services\MessageService;
use Carbon\Carbon;

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
            $service = Service::where('id', $item['id'])
                ->where('salon_id', $data['salon_id'])
                ->first();

            $bookingAvailabilityService = new BookingAvailabilityService();

            // Convert date string to Carbon instance
            $date = Carbon::parse($data['date']);

            if ($service) {
                $bookingAvailabilityService->isSlotOptionValid($date, $item['start_time'],$item['end_time'], $service);
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



    public function createFromUser(CreateFromUserNewRequest $request)
    {
        $data = BookingPermission::create($request->validated());

        $services = $data['services'] ?? [];

        foreach ($services as $item) {
            $service = Service::where('id', $item['id'])
                ->where('salon_id', $data['salon_id'])
                ->first();

            $bookingAvailabilityService = new BookingAvailabilityService();

            // Convert date string to Carbon instance
            $date = Carbon::parse($data['date']);

            if ($service) {
                $bookingAvailabilityService->isSlotOptionValid($date, $item['start_time'], $item['end_time'], $service);
            } else {
                MessageService::abort(404, 'messages.service.item_not_found');
            }
        }

        $booking = $this->bookingService->createFromUserNew($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.booking.item_created_successfully'),
            'data' => new BookingResource($booking),
        ]);
    }


    
}
