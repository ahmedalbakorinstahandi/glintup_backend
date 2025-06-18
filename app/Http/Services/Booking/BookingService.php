<?php

namespace App\Http\Services\Booking;

use App\Http\Permissions\Booking\BookingPermission;
use App\Http\Resources\Rewards\FreeServiceResource;
use App\Models\Booking\Booking;
use App\Models\Booking\Coupon;
use App\Models\Booking\CouponUsage;
use App\Models\General\Address;
use App\Models\General\Setting;
use App\Models\General\Status;
use App\Models\Rewards\FreeService;
use App\Models\Rewards\LoyaltyPoint;
use App\Models\Salons\SalonCustomer;
use App\Models\Salons\SalonPayment;
use App\Models\Services\Service;
use App\Models\Users\User;
use App\Models\Users\WalletTransaction;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Services\PhoneService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function index($data)
    {
        $query = Booking::query()->with([
            'user',
            'salon',
            'bookingServices.service',
            'bookingDates',
            'transactions',
            'couponUsage',
            'payments'
        ]);

        $searchFields = [];

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

        if (!empty($data['search'])) {
            $search = $data['search'];
            $numericSearch = preg_replace('/[^0-9]/', '', $search); // Get only numbers for phone search

            $query->orWhereHas('user', function ($q) use ($search, $numericSearch) {
                // Search by name - trim extra spaces and make case insensitive
                $nameSearch = trim($search);
                if (!empty($nameSearch)) {
                    $q->whereRaw("LOWER(CONCAT(TRIM(first_name), ' ', TRIM(last_name))) LIKE ?", ["%" . strtolower($nameSearch) . "%"]);
                }
                // Search by phone number
                if (!empty($numericSearch)) {
                    $q->orWhereRaw("REPLACE(CONCAT(REPLACE(phone_code, '+', ''), phone), ' ', '') LIKE ?", ["%{$numericSearch}%"]);
                }
            });

            // Search in additional salon fields
            $query->orWhereHas('salon', function($q) use ($search) {
                $q->where('merchant_commercial_name', 'LIKE', "%{$search}%")
                  ->orWhere('merchant_legal_name', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('city_street_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_number', 'LIKE', "%{$search}%")
                  ->orWhere('contact_email', 'LIKE', "%{$search}%")
                  ->orWhere('business_contact_name', 'LIKE', "%{$search}%")
                  ->orWhere('business_contact_email', 'LIKE', "%{$search}%")
                  ->orWhere('business_contact_number', 'LIKE', "%{$search}%")
                  ->orWhere('tags', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('country', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });

            // Search by booking code and notes
            $query->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
        }

        $bookings = $query->get();

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
        $booking = Booking::where('id', $id)->first();

        if (!$booking) {
            MessageService::abort(404, 'messages.booking.item_not_found');
        }

        $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments', 'address']);

        return $booking;
    }

    public function create($data)
    {
        $phoneParts = PhoneService::parsePhoneParts($data['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'customer')
            ->first();


        if (!$user) {
            $user = User::create([
                'phone_code' =>  $countryCode,
                'phone'      =>  $phoneNumber,
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

        $booking->load([
            'user',
            'salon',
            'bookingServices.service',
            'bookingDates',
            'transactions',
            'couponUsage',
            'payments'
        ]);

        return $booking;
    }


    public function createNew($data)
    {
        $phoneParts = PhoneService::parsePhoneParts($data['phone']);
        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('phone', $phoneNumber)
            ->where('phone_code', $countryCode)
            ->where('role', 'customer')
            ->first();


        if (!$user) {
            $user = User::create([
                'phone_code' =>  $countryCode,
                'phone'      =>  $phoneNumber,
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

        // test value
        $data['time']  = '00:00:00';

        $data['status'] = 'confirmed';

        $booking = Booking::create($data);

        $booking->code = "BOOKING" . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

        $booking->save();

        // booking services
        if (isset($data['services'])) {
            foreach ($data['services'] as $service) {

                $serviceObject = Service::where('id', $service['id'])->first();

                $booking->bookingServices()->create([
                    'service_id' => $service['id'],
                    'price' => $serviceObject->price,
                    'currency' => $serviceObject->currency,
                    'discount_percentage' => $serviceObject->discount_percentage,
                    'start_date_time' => Carbon::parse($data['date'] . ' ' . $service['start_time']),
                    'end_date_time' => Carbon::parse($data['date'] . ' ' . $service['end_time']),
                    'duration_minutes' => $serviceObject->duration_minutes,
                    'status' => 'confirmed',
                ]);
            }
        }

        $booking->load([
            'user',
            'salon',
            'bookingServices.service',
            'transactions',
            'couponUsage',
            'payments'
        ]);

        return $booking;
    }

    public function update($booking, $data)
    {

        $old_status = $booking->status;

        $booking->update($data);

        $booking->load(['user', 'salon', 'bookingServices.service']);


        if ($old_status != 'completed' && $booking->status == 'completed') {
            // add point to loyalty program
            $user = User::find($booking->user_id);

            // if user have loyalty program and points less than 5 add 1 point to him else create new loyalty program with 1 point
            $loyaltyProgram = LoyaltyPoint::where('user_id', $user->id)->where('points', '<', 5)->first();
            if ($loyaltyProgram) {
                $loyaltyProgram->points += 1;
                $loyaltyProgram->save();


                if ($loyaltyProgram->points == 5) {
                    //TODO send notification to user 
                    // مبروك لقد حصلت على 5 نقاط ولاء
                    $loyaltyProgram->taken_at = now();
                    $loyaltyProgram->save();
                } else {
                    //TODO send notification to user 
                    // اما بزالنا نضيف لك نقطة ولاء جديدة
                }
            } else {
                LoyaltyPoint::create([
                    'user_id' => $user->id,
                    'salon_id' => $booking->salon_id,
                    'points' => 1,
                ]);
            }
        }
        // cancelled booking
        if ($old_status != 'cancelled' && $booking->status == 'cancelled') {
            // TODO :: send notification to user
            $this->cancelBooking($booking, true);
        }

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
                $total_amount_with_out_free_services_after_discount = $coupon->getAmountAfterDiscount($total_amount_with_out_free_services);
                $total_amount_after_discount = $coupon->getAmountAfterDiscount($total_amount);
            }
        }

        $user_balance = $user->balance;

        // check if user have enough balance to pay for the booking with out free services and wihth free services

        $payment_method = $data['payment_method'] ?? 'partially_paid'; // partially_paid,full_paid : if partially_paid 20 % of the total amount will be deducted from the user balance 

        // المبلغ الذي يجب دفعه 
        $amount_to_pay_with_out_free_services = $total_amount_with_out_free_services_after_discount;
        $amount_to_pay_with_free_services = $total_amount_after_discount;

        if ($payment_method == 'full_paid') {
            $you_have_enough_balance_to_pay_with_out_free_services = $user_balance >= $total_amount_with_out_free_services_after_discount;
            $you_have_enough_balance_to_pay_with_free_services = $user_balance >= $total_amount_after_discount;
        } elseif ($payment_method == 'partially_paid') {
            $you_have_enough_balance_to_pay_with_out_free_services = $user_balance >= ($total_amount_with_out_free_services_after_discount * 0.2);
            $you_have_enough_balance_to_pay_with_free_services = $user_balance >= ($total_amount_after_discount * 0.2);



            $amount_to_pay_with_out_free_services = $total_amount_with_out_free_services_after_discount * 0.2;
            $amount_to_pay_with_free_services = $total_amount_after_discount * 0.2;
        }


        // get  services
        $services = Service::whereIn('id', array_column($data['services'], 'id'))->get();

        return [

            'with_free_services' => [
                'total_amount' => $total_amount_with_out_free_services,
                'total_amount_after_discount' => $total_amount_with_out_free_services_after_discount,
                'discount_amount' => $total_amount_with_out_free_services - $total_amount_with_out_free_services_after_discount,
                'you_have_enough_balance_to_pay' => $you_have_enough_balance_to_pay_with_out_free_services,
                'amount_to_pay_with_out_free_services' => $amount_to_pay_with_out_free_services,
                'payment_percentage' => $payment_method == 'partially_paid' ? 20 : 100,
            ],

            'with_out_free_services' => [
                'total_amount' => $total_amount,
                'total_amount_after_discount' => $total_amount_after_discount,
                'discount_amount' => $total_amount - $total_amount_after_discount,
                'you_have_enough_balance_to_pay' => $you_have_enough_balance_to_pay_with_free_services,
                'amount_to_pay_with_free_services' => $amount_to_pay_with_free_services,
                'payment_percentage' => $payment_method == 'partially_paid' ? 20 : 100,
            ],
            'selected_free_services' => $selected_free_services,
            'services' => $services,
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
                    'en' => __('messages.booking.booking_details', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'en'),
                    'ar' => __('messages.booking.booking_details', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'ar'),
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


        $salon = $booking->salon;

        $salon_type = $salon->type; // "salon", "home_service", "beautician", "clinic"
        // salons_provider_percentage,clinics_provider_percentage,home_service_provider_percentage,makeup_artists_provider_percentage

        switch ($salon_type) {
            case 'salon':
                $system_percentage = Setting::where('key', 'salons_provider_percentage')->first()->value ?? 0;
                break;
            case 'home_service':
                $system_percentage = Setting::where('key', 'home_service_provider_percentage')->first()->value ?? 0;
                break;
            case 'beautician':
                $system_percentage = Setting::where('key', 'makeup_artists_provider_percentage')->first()->value ?? 0;
                break;
            case 'clinic':
                $system_percentage = Setting::where('key', 'clinics_provider_percentage')->first()->value ?? 0;
                break;
            default:
                $system_percentage = 0;
        }

        // booking payment
        $salonPayment =  SalonPayment::create([
            'paymentable_id' => $booking->id,
            'paymentable_type' => Booking::class,
            'user_id' => $user->id,
            'salon_id' => $booking->salon_id,
            'amount' => $amount,
            'currency' => 'AED',
            'method' => 'wallet',
            'status' => 'confirm',
            'is_refund' => false,
            'system_percentage' => $system_percentage,
        ]);

        $salonPayment->code = 'SP' . str_pad($salonPayment->id, 6, '0', STR_PAD_LEFT);
        $salonPayment->save();


        // check if user have free services and use them
        if ($use_free_services) {
            foreach ($bookingDetails['selected_free_services'] as $freeService) {
                if ($freeService) {
                    $freeService->is_used = 1;
                    $freeService->booking_id = $booking->id;
                    $freeService->save();
                }
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

    public function createFromUserNew($data)
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
        $data['status'] = 'confirmed'; // "pending", "confirmed", "completed", "cancelled"

        // test value
        $data['time']  = '00:00:00';

        $booking = Booking::create($data);
        $booking->code = "BOOKING" . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

        $booking->save();


        // booking service location
        if ($data['address_id']) {

            $address = Address::find($data['address_id']);

            $booking->address()->create([
                'addressable_id' => $booking->id,
                'addressable_type' => Booking::class,
                'name' => $address->name,
                'address' => $address->address,
                'address_secondary' => $address->address_secondary,
                'city' => $address->city,
                'country' => $address->country,
                'postal_code' => $address->postal_code,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'city_place_id' => $address->city_place_id
            ]);
        }

        // create transaction for user balance
        $transaction = WalletTransaction::create(
            [
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => 'AED',
                'description' => [
                    'en' => __('messages.booking.booking_details', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'en'),
                    'ar' => __('messages.booking.booking_details', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'ar'),
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


        $salon = $booking->salon;

        $salon_type = $salon->type; // "salon", "home_service", "beautician", "clinic"
        // salons_provider_percentage,clinics_provider_percentage,home_service_provider_percentage,makeup_artists_provider_percentage

        switch ($salon_type) {
            case 'salon':
                $system_percentage = Setting::where('key', 'salons_provider_percentage')->first()->value ?? 0;
                break;
            case 'home_service':
                $system_percentage = Setting::where('key', 'home_service_provider_percentage')->first()->value ?? 0;
                break;
            case 'beautician':
                $system_percentage = Setting::where('key', 'makeup_artists_provider_percentage')->first()->value ?? 0;
                break;
            case 'clinic':
                $system_percentage = Setting::where('key', 'clinics_provider_percentage')->first()->value ?? 0;
                break;
            default:
                $system_percentage = 0;
        }

        // booking payment
        $salonPayment =  SalonPayment::create([
            'paymentable_id' => $booking->id,
            'paymentable_type' => Booking::class,
            'user_id' => $user->id,
            'salon_id' => $booking->salon_id,
            'amount' => $amount,
            'currency' => 'AED',
            'method' => 'wallet',
            'status' => 'confirm',
            'is_refund' => false,
            'system_percentage' => $system_percentage,
        ]);

        $salonPayment->code = 'SP' . str_pad($salonPayment->id, 6, '0', STR_PAD_LEFT);
        $salonPayment->save();


        // check if user have free services and use them
        if ($use_free_services) {
            foreach ($bookingDetails['selected_free_services'] as $freeService) {
                if (is_object($freeService)) {
                    $freeService->is_used = 1;
                    $freeService->booking_id = $booking->id;
                    $freeService->save();
                }
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
                $serviceObject = Service::find($service['id']);

                $booking->bookingServices()->create([
                    'service_id' => $service['id'],
                    'price' => $serviceObject->price,
                    'currency' => $serviceObject->currency,
                    'discount_percentage' => $serviceObject->discount_percentage,
                    'start_date_time' => Carbon::parse($data['date'] . ' ' . $service['start_time']),
                    'end_date_time' => Carbon::parse($data['date'] . ' ' . $service['end_time']),
                    'duration_minutes' => $serviceObject->duration_minutes,
                    'status' => 'confirmed',
                ]);
            }
        }

        $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments']);

        return $booking;
    }





    public function cancelBooking($booking, $cancelBySalon)
    {

        // check if booking is already cancelled
        if ($booking->status == 'cancelled') {
            MessageService::abort(422, 'messages.booking.already_cancelled_booking');
        }

        if ($booking->status == 'completed') {
            MessageService::abort(422, 'messages.booking.cannot_cancel_completed_booking');
        }

        $booking->status = 'cancelled';
        $booking->save();

        // TODO :: send notification to salon

        $amountRefund = $booking->getTotalPriceAttribute();

        $user = $booking->user;
        $salon = $booking->salon;
        $user_balance = $user->balance;

        // if salon make up artist refund 80% of the amount to the user
        if (!$cancelBySalon && $salon->isMakeupArtist()) {
            $amountRefund = $amountRefund * 0.8;
            $user->balance = $user_balance + $amountRefund;
        } else {
            $user->balance = $user_balance +  $amountRefund;
        }
        $user->save();


        // check if user have free services and use them
        if ($booking->freeServices) {
            foreach ($booking->freeServices as $freeService) {
                if ($freeService) {
                    $freeService->is_used = 0;
                    $freeService->booking_id = null;
                    $freeService->save();
                }
            }
        }



        // create transaction for user balance
        $transaction = WalletTransaction::create(
            [
                'user_id' => $user->id,
                'amount' => $amountRefund,
                'currency' => 'AED',
                'description' => [
                    'en' => __('messages.booking.booking_cancelled', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'en'),
                    'ar' => __('messages.booking.booking_cancelled', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'ar'),
                ],
                'status' => 'completed',
                'type' => 'booking',
                'is_refund' => true,
                'transactionable_id' => $booking->id,
                'transactionable_type' => Booking::class,
                'direction' => "in",
                'metadata' => [],
            ]
        );


        // booking payment
        $salonPayment =   SalonPayment::create([
            'paymentable_id' => $booking->id,
            'paymentable_type' => Booking::class,
            'user_id' => $user->id,
            'salon_id' => $booking->salon_id,
            'amount' => $amountRefund,
            'currency' => 'AED',
            'method' => 'wallet',
            'status' => 'confirm',
            'is_refund' => true,
        ]);

        $salonPayment->code = 'SP' . str_pad($salonPayment->id, 6, '0', STR_PAD_LEFT);
        $salonPayment->save();

        // TODO :: send notification to user


        return $booking;
    }



    // reschedule booking from user
    public function rescheduleBooking($booking, $data)
    {

        if ($booking->status == 'completed') {
            MessageService::abort(422, 'messages.booking.cannot_reschedule_completed_booking');
        }

        if ($booking->status == 'cancelled') {
            MessageService::abort(422, 'messages.booking.cannot_reschedule_cancelled_booking');
        }

        // TODO :: check if the new date and time is available in the salon calendar // salon work, holidays, booking dates, services capacity 

        // check if same date and time
        if ($booking->date == $data['date'] && $booking->time == $data['time']) {
            MessageService::abort(422, 'messages.booking.same_date_time');
        }

        // update booking date
        $booking->bookingDates()->update([
            'date' => $data['date'],
            'time' => $data['time'],
            'status' => 'accepted',
        ]);

        $booking->update([
            'date' => $data['date'],
            'time' => $data['time'],
        ]);

        // TODO :: send notification to salon


        return $booking;
    }


    public function updateFromUser($booking, $data)
    {
        if ($booking->status === 'completed') {
            MessageService::abort(422, 'messages.booking.cannot_update_completed_booking');
        }

        if ($booking->status === 'cancelled') {
            MessageService::abort(422, 'messages.booking.cannot_update_cancelled_booking');
        }

        $user = User::auth();

        // استدعاء تفاصيل الحجز الجديدة
        $bookingDetails = $this->returnBookingDetails($data);
        $use_free_services = $data['use_free_services'] ?? false;
        $service_amount_data = $use_free_services
            ? $bookingDetails['with_free_services']
            : $bookingDetails['with_out_free_services'];

        $payment_percentage = $service_amount_data['payment_percentage'];
        $new_total_after_discount = $service_amount_data['total_amount_after_discount'];

        // المعاملة القديمة
        $old_transaction = $booking->transactions()->where('is_refund', false)->first();
        $old_amount_paid = $old_transaction->amount ?? 0;

        // احسب الفرق
        $amount_diff = $new_total_after_discount - $old_amount_paid;

        if ($amount_diff > 0) {
            // خصم الفرق من رصيد المستخدم
            if ($user->balance < $amount_diff) {
                MessageService::abort(422, 'messages.user.not_enough_balance');
            }
            $user->balance -= $amount_diff;
        } elseif ($amount_diff < 0) {
            // استرجاع الفرق للمستخدم
            $user->balance += abs($amount_diff);
        }
        $user->save();

        // تحديث الخدمات
        $current_services = $booking->bookingServices()->pluck('service_id')->toArray();
        $new_services = array_column($data['services'], 'id');

        $to_add = array_diff($new_services, $current_services);
        $to_remove = array_diff($current_services, $new_services);

        if (count($to_remove) > 0) {
            $booking->bookingServices()->whereIn('service_id', $to_remove)->delete();
        }

        foreach ($to_add as $service_id) {
            $booking->bookingServices()->create(['service_id' => $service_id]);
        }

        // حذف الدفعات السابقة
        $booking->payments()->delete();

        // إنشاء دفعة جديدة
        $system_percentage = Setting::where('key', 'system_percentage_booking')->first()->value ?? 0;
        $salonPayment =  SalonPayment::create([
            'paymentable_id' => $booking->id,
            'paymentable_type' => Booking::class,
            'user_id' => $user->id,
            'salon_id' => $booking->salon_id,
            'amount' => $new_total_after_discount,
            'currency' => 'AED',
            'method' => 'wallet',
            'status' => 'confirm',
            'is_refund' => false,
            'system_percentage' => $system_percentage,
        ]);

        $salonPayment->code = 'SP' . str_pad($salonPayment->id, 6, '0', STR_PAD_LEFT);
        $salonPayment->save();

        // حذف المعاملة السابقة
        $old_transaction?->delete();

        // إنشاء معاملة جديدة
        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $new_total_after_discount,
            'currency' => 'AED',
            'description' => [
                'en' => __('messages.booking.booking_updated', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'en'),
                'ar' => __('messages.booking.booking_updated', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'ar'),
            ],
            'status' => 'completed',
            'type' => 'booking',
            'is_refund' => false,
            'transactionable_id' => $booking->id,
            'transactionable_type' => Booking::class,
            'direction' => 'out',
            'metadata' => [],
        ]);

        // تحديث الخدمات المجانية
        if ($booking->freeServices) {
            foreach ($booking->freeServices as $freeService) {
                $freeService->is_used = 0;
                $freeService->booking_id = null;
                $freeService->save();
            }
        }

        if ($use_free_services) {
            foreach ($bookingDetails['selected_free_services'] as $freeService) {
                $freeService->is_used = 1;
                $freeService->booking_id = $booking->id;
                $freeService->save();
            }
        }

        // التعامل مع الكوبون المستخدم سابقًا
        $old_coupon_usage = $booking->couponUsage;
        $old_coupon_id = $old_coupon_usage?->coupon_id;
        $new_coupon_id = $data['coupon_id'] ?? null;

        if ($old_coupon_id && $old_coupon_id !== $new_coupon_id) {
            $old_coupon_usage->delete();
        }

        if ($new_coupon_id) {
            $coupon = Coupon::find($new_coupon_id);

            if (!$coupon || !$coupon->getIsValidAttribute()) {
                MessageService::abort(422, 'messages.coupon.is_invalid');
            }

            if (!$old_coupon_id || $old_coupon_id !== $new_coupon_id) {
                CouponUsage::create([
                    'coupon_id' => $new_coupon_id,
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'used_at' => now(),
                ]);
            }
        }

        $booking->load([
            'user',
            'salon',
            'bookingServices.service',
            'bookingDates',
            'transactions',
            'couponUsage',
            'payments',
        ]);

        return $booking;
    }



    public function updateFromUser2($booking, $data)
    {
        if ($booking->status === 'completed') {
            MessageService::abort(422, 'messages.booking.cannot_update_completed_booking');
        }

        if ($booking->status === 'cancelled') {
            MessageService::abort(422, 'messages.booking.cannot_update_cancelled_booking');
        }

        $user = User::auth();

        // حساب تفاصيل الحجز الجديدة
        $bookingDetails = $this->returnBookingDetails($data);
        $use_free_services = $data['use_free_services'] ?? false;
        $service_amount_data = $use_free_services
            ? $bookingDetails['with_free_services']
            : $bookingDetails['with_out_free_services'];

        $payment_percentage = $service_amount_data['payment_percentage'];
        $new_total_after_discount = $service_amount_data['total_amount_after_discount'];

        // السعر المدفوع سابقًا
        $old_transaction = $booking->transactions()->where('is_refund', false)->first();
        $old_amount_paid = $old_transaction->amount ?? 0;

        // حساب الفارق
        $amount_diff = $new_total_after_discount - $old_amount_paid;

        // تعديل رصيد المستخدم
        if ($amount_diff > 0) {
            if ($user->balance < $amount_diff) {
                MessageService::abort(422, 'messages.user.not_enough_balance');
            }
            $user->balance -= $amount_diff;
        } elseif ($amount_diff < 0) {
            $user->balance += abs($amount_diff);
        }
        $user->save();

        // تعديل الخدمات
        $current_services = $booking->bookingServices()->pluck('service_id')->toArray();
        $new_services = array_column($data['services'], 'id');

        $to_add = array_diff($new_services, $current_services);
        $to_remove = array_diff($current_services, $new_services);

        if (count($to_remove) > 0) {
            $booking->bookingServices()->whereIn('service_id', $to_remove)->delete();
        }

        foreach ($to_add as $service_id) {
            $booking->bookingServices()->create(['service_id' => $service_id]);
        }

        // إضافة دفعة جديدة ومعاملة جديدة بناءً على الفارق
        if ($amount_diff != 0) {
            $is_refund = $amount_diff < 0;
            $diff_amount = abs($amount_diff);

            // حركة مالية
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $diff_amount,
                'currency' => 'AED',
                'description' => [
                    'en' => __('messages.booking.booking_updated', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'en'),
                    'ar' => __('messages.booking.booking_updated', ['code' => $booking->code, 'salon' => $booking->salon->merchant_commercial_name], 'ar'),
                ],
                'status' => 'completed',
                'type' => 'booking',
                'is_refund' => $is_refund,
                'transactionable_id' => $booking->id,
                'transactionable_type' => Booking::class,
                'direction' => $is_refund ? 'in' : 'out',
                'metadata' => [],
            ]);

            // دفعة للصالون
            $system_percentage = Setting::where('key', 'system_percentage_booking')->first()->value ?? 0;

            $salonPayment =  SalonPayment::create([
                'paymentable_id' => $booking->id,
                'paymentable_type' => Booking::class,
                'user_id' => $user->id,
                'salon_id' => $booking->salon_id,
                'amount' => $diff_amount,
                'currency' => 'AED',
                'method' => 'wallet',
                'status' => 'confirm',
                'is_refund' => $is_refund,
                'system_percentage' => $system_percentage,
            ]);

            $salonPayment->code = 'SP' . str_pad($salonPayment->id, 6, '0', STR_PAD_LEFT);
            $salonPayment->save();
        }

        // تنظيف الخدمات المجانية القديمة
        if ($booking->freeServices) {
            foreach ($booking->freeServices as $freeService) {
                $freeService->is_used = 0;
                $freeService->booking_id = null;
                $freeService->save();
            }
        }

        // إضافة الخدمات المجانية الجديدة
        if ($use_free_services) {
            foreach ($bookingDetails['selected_free_services'] as $freeService) {
                $freeService->is_used = 1;
                $freeService->booking_id = $booking->id;
                $freeService->save();
            }
        }

        // التعامل مع الكوبون القديم والجديد
        $old_coupon_usage = $booking->couponUsage;
        $old_coupon_id = $old_coupon_usage?->coupon_id;
        $new_coupon_id = $data['coupon_id'] ?? null;

        if ($old_coupon_id && $old_coupon_id !== $new_coupon_id) {
            $old_coupon_usage->delete();
        }

        if ($new_coupon_id) {
            $coupon = Coupon::find($new_coupon_id);

            if (!$coupon || !$coupon->getIsValidAttribute()) {
                MessageService::abort(422, 'messages.coupon.is_invalid');
            }

            if (!$old_coupon_id || $old_coupon_id !== $new_coupon_id) {
                CouponUsage::create([
                    'coupon_id' => $new_coupon_id,
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'used_at' => now(),
                ]);
            }
        }

        $booking->load([
            'user',
            'salon',
            'bookingServices.service',
            'bookingDates',
            'transactions',
            'couponUsage',
            'payments',
        ]);

        return $booking;
    }







    public function cancelBookingService(Booking $booking, int $bookingServiceId)
    {
        if ($booking->status === 'cancelled' || $booking->status === 'completed' || $booking->status === 'Rejected') {
            MessageService::abort(422, 'messages.booking.cannot_cancel_service');
        }

        $user = $booking->user;

        $service = $booking->bookingServices()->where('id', $bookingServiceId)->first();

        if (!$service || $service->status === 'cancelled' || $service->status === 'rejected') {
            MessageService::abort(422, 'messages.booking.service_not_found_or_cancelled');
        }

        if ($service->status === 'completed') {
            MessageService::abort(422, 'messages.booking.cannot_cancel_service');
        }


        $refundAllowed = $booking->created_by === 'customer';


        $amount = $service->getFinalPriceAttribute();

        if ($refundAllowed && $amount > 0) {
            $user->balance += $amount;
            $user->save();

            // حركة مالية
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => 'AED',
                'description' => [
                    'en' => __('messages.booking.service_cancelled', ['code' => $booking->code], 'en'),
                    'ar' => __('messages.booking.service_cancelled', ['code' => $booking->code], 'ar'),
                ],
                'status' => 'completed',
                'type' => 'booking',
                'is_refund' => true,
                'transactionable_id' => $booking->id,
                'transactionable_type' => Booking::class,
                'direction' => 'in',
                'metadata' => ['booking_service_id' => $bookingServiceId],
            ]);
        }

        $service->status = 'cancelled';
        $service->save();

        Status::create([
            'name' => 'cancelled',
            'statusable_id' => $service->id,
            'statusable_type' => BookingService::class,
            'created_by' => $user->id,
        ]);


        // TODO send notification to salon

        return  $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments']);
    }


    public function cancelBookingFully(Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            MessageService::abort(422, 'messages.booking.already_cancelled_booking');
        }

        if ($booking->status === 'completed') {
            MessageService::abort(422, 'messages.booking.cannot_cancel_completed_booking');
        }

        $booking->status = 'cancelled';
        $booking->save();

        $user = User::auth();
        Status::create([
            'name' => 'cancelled',
            'statusable_id' => $booking->id,
            'statusable_type' => Booking::class,
            'created_by' => $user->id,
        ]);

        $user = $booking->user;
        $refundAllowed = $booking->created_by === 'customer';

        $amountRefund = $booking->bookingServices()
            ->where('status', '!=', 'cancelled')
            ->sum(DB::raw('price - (price * discount_percentage / 100)'));

        if ($refundAllowed && $amountRefund > 0) {
            $user->balance += $amountRefund;
            $user->save();

            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amountRefund,
                'currency' => 'AED',
                'description' => [
                    'en' => __('messages.booking.booking_cancelled', ['code' => $booking->code], 'en'),
                    'ar' => __('messages.booking.booking_cancelled', ['code' => $booking->code], 'ar'),
                ],
                'status' => 'completed',
                'type' => 'booking',
                'is_refund' => true,
                'transactionable_id' => $booking->id,
                'transactionable_type' => Booking::class,
                'direction' => 'in',
                'metadata' => [],
            ]);
        }

        foreach ($booking->bookingServices as $service) {
            if ($service->status !== 'cancelled') {
                $service->status = 'cancelled';

                Status::create([
                    'name' => 'cancelled',
                    'statusable_id' => $service->id,
                    'statusable_type' => BookingService::class,
                    'created_by' => $user->id,
                ]);
                $service->save();
            }
        }

        // إلغاء الخدمات المجانية
        foreach ($booking->freeServices ?? [] as $freeService) {
            $freeService->is_used = 0;
            $freeService->booking_id = null;
            $freeService->save();
        }

        // إلغاء كوبون الحجز إن وُجد
        if ($booking->couponUsage) {
            $booking->couponUsage->delete();
        }

        // تسجيل الدفع كاسترجاع
        SalonPayment::create([
            'paymentable_id' => $booking->id,
            'paymentable_type' => Booking::class,
            'user_id' => $user->id,
            'salon_id' => $booking->salon_id,
            'amount' => $amountRefund,
            'currency' => 'AED',
            'method' => 'wallet',
            'status' => 'confirm',
            'is_refund' => true,
            'system_percentage' => 0,
            'code' => 'SP' . str_pad(SalonPayment::max('id') + 1, 6, '0', STR_PAD_LEFT),
        ]);

        return $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments']);
    }






    // rescheduleBookingServices
    public function rescheduleBookingServices(Booking $booking, array $services)
    {
        if ($booking->status === 'completed') {
            MessageService::abort(422, 'messages.booking.cannot_reschedule_completed_booking');
        }

        if ($booking->status === 'cancelled') {
            MessageService::abort(422, 'messages.booking.cannot_reschedule_cancelled_booking');
        }

        if ($booking->status === 'Rejected') {
            MessageService::abort(422, 'messages.booking.cannot_reschedule_rejected_booking');
        }

        foreach ($services as $service) {
            $bookingService = $service['booking_service'];

            //     "message": "Could not parse '2025-06-25 00:00:00 13:00': Failed to parse time string (2025-06-25 00:00:00 13:00) at position 20 (1): Double time specification",

            $bookingService->update([
                'start_date_time' => Carbon::parse($booking->date->format('Y-m-d') . ' ' . $service['start_time']),
                'end_date_time' => Carbon::parse($booking->date->format('Y-m-d') . ' ' . $service['end_time']),
            ]);

            // إضافة سجل الحالة
            Status::create([
                'name' => 'rescheduled',
                'statusable_id' => $bookingService->id,
                'statusable_type' => BookingService::class,
                'created_by' => User::auth()->id,
            ]);
        }

        // TODO: إرسال إشعار للصالون

        return $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments']);
    }
}
