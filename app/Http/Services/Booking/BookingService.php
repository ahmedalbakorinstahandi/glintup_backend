<?php

namespace App\Http\Services\Booking;

use App\Http\Permissions\Booking\BookingPermission;
use App\Http\Resources\Rewards\FreeServiceResource;
use App\Models\Booking\Booking;
use App\Models\Booking\Coupon;
use App\Models\Booking\CouponUsage;
use App\Models\Rewards\FreeService;
use App\Models\Salons\SalonCustomer;
use App\Models\Services\Service;
use App\Models\Users\User;
use App\Models\Users\WalletTransaction;
use App\Services\FilterService;
use App\Services\MessageService;

class BookingService
{
    public function index($data)
    {
        $query = Booking::query()->with(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments']);



        $searchFields = ['code', 'notes'];
        $numericFields = [];
        $dateFields = ['date', 'created_at'];
        $exactMatchFields = ['user_id', 'salon_id', 'status'];
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

        $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments']);

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

        $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments']);

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

    // return_details
    public function returnBookingDetails($data)
    {
        $user = User::auth();

        // calculate amount from services
        $total_amount = 0;

        $total_amount_with_out_free_services = 0;
        $selected_free_services = [];

        foreach ($data['services'] as $service) {
            $service = Service::find($service['id']);

            // check current user have this service in his salon for free
            $freeService = FreeService::where([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'is_used' => 0,
            ])->first();

            if ($freeService) {
                $selected_free_services[] = $freeService;
            } else {
                $total_amount_with_out_free_services += $service->getFinalPriceAttribute();
            }

            $final_price = $service->getFinalPriceAttribute();

            $total_amount += $final_price;
        }

        $total_amount_with_out_free_services_after_discount = $total_amount_with_out_free_services;
        $total_amount_after_discount = $total_amount;

        // check if user have coupon_id
        if (isset($data['coupon_id'])) {
            $coupon = Coupon::find($data['coupon_id']);

            if (!$coupon->getIsValidAttribute()) {
                MessageService::abort(422, 'messages.coupon.is_invalid');
            }

            if ($coupon) {
                $total_amount_with_out_free_services_after_discount -= $coupon->getAmountAfterDiscount($total_amount_with_out_free_services);
                $total_amount_after_discount -= $coupon->getAmountAfterDiscount($total_amount);
            }
        }

        $user_balance = $user->balance;

        // check if user have enough balance to pay for the booking with out free services and wihth free services

        $payment_method = $data['payment_method'] ?? 'partially_paid'; // partially_paid,full_paid : if partially_paid 20 % of the total amount will be deducted from the user balance 


        if ($payment_method == 'full_paid') {
            $you_have_enough_balance_to_pay_with_out_free_services = $user_balance >= $total_amount_with_out_free_services_after_discount;
            $you_have_enough_balance_to_pay_with_free_services = $user_balance >= $total_amount_after_discount;
        } elseif ($payment_method == 'partially_paid') {
            $you_have_enough_balance_to_pay_with_out_free_services = $user_balance >= ($total_amount_with_out_free_services_after_discount * 0.2);
            $you_have_enough_balance_to_pay_with_free_services = $user_balance >= ($total_amount_after_discount * 0.2);
        }


        return [

            'with_out_free_services' => [
                'total_amount' => $total_amount_with_out_free_services,
                'total_amount_after_discount' => $total_amount_with_out_free_services_after_discount,
                'discount_amount' => $total_amount_with_out_free_services - $total_amount_with_out_free_services_after_discount,
                'you_have_enough_balance_to_pay' => $you_have_enough_balance_to_pay_with_out_free_services,
                'payment_percentage' => $payment_method == 'partially_paid' ? 20 : 100,
            ],

            'with_free_services' => [
                'total_amount' => $total_amount,
                'total_amount_after_discount' => $total_amount_after_discount,
                'discount_amount' => $total_amount - $total_amount_after_discount,
                'you_have_enough_balance_to_pay' => $you_have_enough_balance_to_pay_with_free_services,
                'payment_percentage' => $payment_method == 'partially_paid' ? 20 : 100,
            ],
            'selected_free_services' => $selected_free_services,
        ];
    }

    // createFromUser
    public function createFromUser($data)
    {

        $bookingDetails = $this->returnBookingDetails($data);

        $use_free_services = $data['use_free_services'] ?? false;

        $service_amount_data = [];

        if ($use_free_services) {
            $service_amount_data = $bookingDetails['with_free_services'];
        } else {
            $service_amount_data = $bookingDetails['with_out_free_services'];
        }

        $you_have_enough_balance_to_pay =  $service_amount_data['you_have_enough_balance_to_pay'];

        if (!$you_have_enough_balance_to_pay) {
            MessageService::abort(422, 'messages.user.not_enough_balance');
        }


        $amount = $service_amount_data['total_amount_after_discount'];

        $user = User::auth();

        $user_balance = $user->balance;
        $user->balance = $user_balance - $amount;
        $user->save();




        $data['code'] = rand(100000, 999999);



        $data['user_id'] = $user->id;

        $data['created_by'] = 'customer'; // "salon","customer"
        $data['status'] = 'pending'; // "pending", "confirmed", "completed", "cancelled"

        $booking = Booking::create($data);
        $booking->code = "BOOKING" . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

        $booking->save();

        //bookingDate
        $booking->bookingDates()->create([
            'booking_id' => $booking->id,
            'date' => $data['date'],
            'time' => $data['time'],
            'created_by' => $data['created_by'] ?? 'customer', // "salon","customer"
            'status' => 'pending',
        ]);

        // create transaction for user balance
        $transaction = WalletTransaction::create(
            [
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => 'AED',
                'description' => [
                    'en' => __('messages.booking.booking_details', ['code' => $booking->code, 'salon' => $booking->salon->name], 'en'),
                    'ar' => __('messages.booking.booking_details', ['code' => $booking->code, 'salon' => $booking->salon->name], 'ar'),
                ],
                'status' => 'completed',
                'type' => 'booking',
                'is_refund' => false,
                'transactionable_id' => $booking->id,
                'transactionable_type' => Booking::class,
                'direction' => "out",
                'metadata' => [],
            ]
        );


        // booking payment
        $booking->payments()->create([
            'user_id' => $user->id,
            'salon_id' => $booking->salon_id,
            'amount' => $amount,
            'currency' => 'AED',
            'method' => 'wallet',
            'status' => 'confirm',
            'is_refund' => false,
        ]);


        // check if user have free services and use them
        if ($use_free_services) {
            foreach ($data['selected_free_services'] as $freeService) {
                $freeService->is_used = 1;
                $freeService->booking_id = $booking->id;
                $freeService->save();
            }
        }

        // check if user have coupon_id: CouponUsage
        if (isset($data['coupon_id'])) {
            CouponUsage::create([
                'coupon_id' => $data['coupon_id'],
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'used_at' => now(),
            ]);
        }



        // booking services
        if (isset($data['services'])) {
            foreach ($data['services'] as $service) {
                $booking->bookingServices()->create([
                    'service_id' => $service['id'],
                ]);
            }
        }

        $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments']);

        return $booking;
    }
}
