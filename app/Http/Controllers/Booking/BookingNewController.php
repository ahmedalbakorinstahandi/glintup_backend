<?php

namespace App\Http\Controllers\Booking;

use App\Http\Permissions\Booking\BookingPermission;
use App\Http\Requests\Booking\Booking\CreateFromUserNewRequest;
use App\Http\Requests\Booking\Booking\CreateFromUserRequest;
use App\Http\Requests\Booking\Booking\CreateNewRequest;
use App\Http\Requests\Booking\Booking\CreateRequest;
use App\Http\Requests\Booking\Booking\RescheduleServicesRequest;
use App\Http\Requests\Booking\BookingService\GetAvailableSlotsRequest;
use App\Http\Resources\Booking\BookingResource;
use App\Http\Services\Booking\BookingService;
use App\Models\Booking\BookingService as BookingBookingService;
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
                $bookingAvailabilityService->isSlotOptionValid($date, $item['start_time'], $item['end_time'], $service);
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

        $result = $this->bookingService->createFromUserNew($data);

        // Check if it's a payment response (Stripe) or direct booking (wallet)
        if (is_array($result) && isset($result['payment'])) {
            return response()->json([
                'success' => true,
                'message' => trans('messages.booking.payment_required'),
                'data' => $result['payment'],
            ]);
        } else {
            // Direct booking object (wallet payment)
            return response()->json([
                'success' => true,
                'message' => trans('messages.booking.item_created_successfully'),
                'data' => new BookingResource($result),
            ]);
        }
    }



    // reschedule booking services
    public function rescheduleBookingServices($id, RescheduleServicesRequest $request)
    {

        $booking = $this->bookingService->show($id);


        BookingPermission::canUpdate($booking);

        $data = $request->validated();

        $bookingServices = $data['services'] ?? [];
        $services = [];

        foreach ($bookingServices as $item) {
            $bookingService = $booking->bookingServices()
                ->where('id', $item['id'])
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'completed')
                ->first();

            if (!$bookingService) {
                MessageService::abort(422, 'messages.booking.service_not_found_or_invalid');
            }

            $service = Service::where('id', $bookingService->service_id)
                ->where('salon_id', $booking->salon_id)
                ->first();

            if (!$service) {
                MessageService::abort(404, 'messages.service.item_not_found');
            }

            $bookingAvailabilityService = new BookingAvailabilityService();
            $date = $booking->date;

            $bookingAvailabilityService->isSlotOptionValid(
                $date,
                $item['start_time'],
                $item['end_time'],
                $service,
            );

            $services[] = [
                'booking_service' => $bookingService,
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time'],
            ];
        }

        $booking = $this->bookingService->rescheduleBookingServices($booking, $services);

        return response()->json([
            'success' => true,
            'message' => trans('messages.booking.services_rescheduled_successfully'),
            'data' => new BookingResource($booking),
        ]);
    }
}
