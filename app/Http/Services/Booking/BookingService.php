<?php

namespace App\Http\Services\Bookings;

use App\Models\Booking\Booking;
use App\Services\FilterService;
use App\Services\MessageService;

class BookingService
{
    public function index($data)
    {
        $query = Booking::query()->with(['user', 'salon']);

        $searchFields = ['code', 'notes'];
        $numericFields = [];
        $dateFields = ['date', 'created_at'];
        $exactMatchFields = ['user_id', 'salon_id', 'status', 'payment_status'];
        $inFields = ['id'];

        return FilterService::applyFilters(
            $query,
            $data,
            $searchFields,
            $numericFields,
            $dateFields,
            $exactMatchFields,
            $inFields
        );
    }

    public function show($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            MessageService::abort(404, 'messages.booking.item_not_found');
        }

        return $booking;
    }

    public function create($data)
    {
        return Booking::create($data);
    }

    public function update($booking, $data)
    {
        $booking->update($data);
        return $booking;
    }

    public function destroy($booking)
    {
        return $booking->delete();
    }
}
