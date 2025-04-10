<?php

namespace App\Http\Services\Booking;

use App\Http\Permissions\Booking\BookingPermission;
use App\Models\Booking\Booking;
use App\Models\Users\User;
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

        $query = BookingPermission::filterIndex($query);

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

        $booking->load(['user', 'salon']);

        return $booking;
    }

    public function create($data)
    {

        $fullPhone = str_replace(' ', '', $data['phone_code']) . str_replace(' ', '', $data['phone']);

        $user = User::whereRaw("REPLACE(CONCAT(phone_code, phone), ' ', '') = ?", [$fullPhone])
            ->where('role', 'customer')
            ->first();


        if (!$user) {
            $user = User::create([
                'phone_code' => $data['phone_code'],
                'phone'      => $data['phone'],
                'role'       => 'customer',
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'gender' => $data['gender'],
                'birth_date' => '1990-01-01',
                'password' => bcrypt('password'),
                'added_by' => 'salon',
                'is_active' => 1,
            ]);
        } else {
            // TODO :: send notification to user
        }

        $data['code'] = rand(100000, 999999);

        $data['user_id'] = $user->id;

        $booking = Booking::create($data);

        $booking->code = "BOOKKING" . $booking->id;

        $booking->save();

        $booking->load(['user', 'salon']);

        return $booking;
    }

    public function update($booking, $data)
    {
        $booking->update($data);

        $booking->load(['user', 'salon']);

        return $booking;
    }

    public function destroy($booking)
    {
        return $booking->delete();
    }
}
