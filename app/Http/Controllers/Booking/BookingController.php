<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Http\Permissions\Booking\BookingPermission;
use App\Http\Requests\Booking\Booking\CreateFromUserRequest;
use App\Http\Requests\Booking\Booking\CreateRequest;
use App\Http\Requests\Booking\Booking\GetBookingDetailsRequest;
use App\Http\Requests\Booking\Booking\RescheduleRequest;
use App\Http\Requests\Booking\Booking\UpdateRequest;
use App\Http\Resources\Booking\BookingResource;
use App\Http\Resources\Rewards\FreeServiceResource;
use App\Http\Resources\Services\ServiceResource;
use App\Http\Services\Booking\BookingService; 
use App\Services\ResponseService;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        $bookings = $this->bookingService->index(request()->all());

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings['data']->items()),
            'meta' => ResponseService::meta($bookings['data']),
            'info' => $bookings['info'],
        ]);
    }

    public function show($id)
    {
        $booking = $this->bookingService->show($id);

        BookingPermission::canShow($booking);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = BookingPermission::create($request->validated());

        $booking = $this->bookingService->create($data);

        return response()->json([
            'success' => true,
            'message' => trans('messages.booking.item_created_successfully'),
            'data' => new BookingResource($booking),
        ]);
    }

    public function update($id, UpdateRequest $request)
    {
        $booking = $this->bookingService->show($id);

        BookingPermission::canUpdate($booking, $request->validated());

        $booking = $this->bookingService->update($booking, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.booking.item_updated_successfully'),
            'data' => new BookingResource($booking),
        ]);
    }

    public function destroy($id)
    {
        $booking = $this->bookingService->show($id);

        BookingPermission::canDelete($booking);

        $deleted = $this->bookingService->destroy($booking);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? trans('messages.booking.item_deleted_successfully')
                : trans('messages.booking.failed_delete_item'),
        ]);
    }


    // create booking from user
    public function createFromUser(CreateFromUserRequest $request)
    {
        $data = BookingPermission::create($request->validated());
        $booking = $this->bookingService->createFromUser($data);

        return response()->json(
            [
                'success' => true,
                'data' => new BookingResource($booking),
            ]
        );
    }

    // returnBookingDetails
    public function returnBookingDetails(GetBookingDetailsRequest $request)
    {
        $data = BookingPermission::create($request->validated());

        $data = $this->bookingService->returnBookingDetails($request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'with_free_services' => $data['with_free_services'],
                'with_out_free_services' => $data['with_out_free_services'],
                'selected_free_services' =>  FreeServiceResource::collection($data['selected_free_services']),
                'services' => ServiceResource::collection($data['services']),
            ],
        ]);
    }


    // rescheduleBooking
    public function rescheduleBooking($id, RescheduleRequest $request)
    {
        $booking = $this->bookingService->show($id);

        BookingPermission::canUpdate($booking);

        $booking = $this->bookingService->rescheduleBooking($booking, $request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('messages.booking.reschedule_successfully'),
            'data' => new BookingResource($booking),
        ]);
    }

    // cancelBooking
    public function cancelBooking($id)
    {
        $booking = $this->bookingService->show($id);

        BookingPermission::canUpdate($booking);

        $booking = $this->bookingService->cancelBooking($booking, false);

        return response()->json([
            'success' => true,
            'message' => trans('messages.booking.cancel_successfully'),
            'data' => new BookingResource($booking),
        ]);
    }
}
