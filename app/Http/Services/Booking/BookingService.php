<?php

namespace App\Http\Services\Booking;

use App\Http\Permissions\Booking\BookingPermission;
use App\Models\Booking\Booking;
use App\Models\Salons\SalonCustomer;
use App\Models\Users\User;
use App\Services\FilterService;
use App\Services\MessageService;

class BookingService
{
    public function index($data)
    {
        $query = Booking::query()->with(['user', 'salon', 'bookingServices.service']);



        $searchFields = ['code', 'notes'];
        $numericFields = [];
        $dateFields = ['date', 'created_at'];
        $exactMatchFields = ['user_id', 'salon_id', 'status', 'payment_status'];
        $inFields = ['id', 'bookingServices.service_id'];

        $query = BookingPermission::filterIndex($query);

        $query = FilterService::applyFilters(
            $query,
            $data,
            $searchFields,
            $numericFields,
            $dateFields,
            $exactMatchFields,
            $inFields,
            false
        );


        $bookings = $query->get();

        // status "pending", "confirmed", "completed", "cancelled"

        $bookings_status_count = [
            'all_count' => $bookings->count(),
            'pending_count' => $bookings->where('status', 'pending')->count(),
            'confirmed_count' => $bookings->where('status', 'confirmed')->count(),
            'completed_count' => $bookings->where('status', 'completed')->count(),
            'cancelled_count' => $bookings->where('status', 'cancelled')->count(),
        ];

        return [
            'data' => $query->paginate($data['limit'] ?? 20),
            'info' => $bookings_status_count,
        ];
    }

    public function show($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            MessageService::abort(404, 'messages.booking.item_not_found');
        }

        $booking->load(['user', 'salon', 'bookingServices.service']);

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


        // add user to salon customers if not exists : salon->customers()

        SalonCustomer::firstOrCreate([
            'salon_id' => $data['salon_id'],
            'user_id' => $user->id,
        ]);



        $data['code'] = rand(100000, 999999);

        $data['user_id'] = $user->id;

        $booking = Booking::create($data);

        $booking->code = "BOOKING" . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

        $booking->save();

        //bookingDate
        $booking->bookingDates()->create([
            'booking_id' => $booking->id,
            'date' => $data['date'],
            'time' => $data['time'],
            'created_by' => $data['created_by'] ?? 'salon', // "salon","customer"
            'status' => $user->added_by == 'salon' && $user->is_verified == 0 ?  'accepted' : 'pending',
        ]);


        // booking services
        if (isset($data['services'])) {
            foreach ($data['services'] as $service) {
                $booking->bookingServices()->create([
                    'service_id' => $service['id'],
                ]);
            }
        }

        $booking->load(['user', 'salon', 'bookingServices.service']);

        return $booking;
    }

    public function update($booking, $data)
    {
        $booking->update($data);

        $booking->load(['user', 'salon', 'bookingServices.service']);

        return $booking;
    }

    public function destroy($booking)
    {
        return $booking->delete();
    }
}
