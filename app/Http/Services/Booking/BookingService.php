<?php

namespace App\Http\Services\Booking;

use App\Http\Notifications\BookingNotification;
use App\Http\Notifications\LoyaltyPointNotification;
use App\Http\Permissions\Booking\BookingPermission;
use App\Http\Resources\Rewards\FreeServiceResource;
use App\Models\Booking\Booking;
use App\Models\Booking\Coupon;
use App\Models\Booking\CouponUsage;
use App\Models\Booking\Invoice;
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
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
        $inFields = ['id', 'bookingServices.service_id', 'status'];

        $query = BookingPermission::filterIndex($query);

        // البحث المبسط والفعال
        if (!empty($data['search'])) {
            $search = trim($data['search']);

            $query->where(function ($q) use ($search) {
                // البحث في كود الحجز
                $q->where('code', 'LIKE', "%{$search}%")
                    // البحث في ملاحظات الحجز
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    // البحث في اسم العميل
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%")
                        ;
                    })
                    // البحث في اسم الصالون
                    ->orWhereHas('salon', function ($salonQuery) use ($search) {
                        $salonQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('merchant_commercial_name', 'LIKE', "%{$search}%");
                    });
            });
        }

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

        $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments', 'address', 'invoice']);

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
            BookingNotification::newBookingForUser($user);
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
            BookingNotification::newBookingForUser($user);
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

        $invoice = $this->createInvoice($booking);

        $booking->load([
            'user',
            'salon',
            'bookingServices.service',
            'transactions',
            'couponUsage',
            'payments',
            'invoice'
        ]);


        BookingNotification::newBookingForUser($booking);

        return $booking;
    }

    public function update($booking, $data)
    {

        $old_status = $booking->status;

        $booking->update($data);

        $booking->load(['user', 'salon', 'bookingServices.service']);


        if ($old_status != 'completed' && $booking->status == 'completed') {

            BookingNotification::bookingCompleted($booking);

            // add point to loyalty program
            $user = User::find($booking->user_id);

            // if user have loyalty program and points less than 5 add 1 point to him else create new loyalty program with 1 point
            $loyaltyProgram = LoyaltyPoint::where('user_id', $user->id)->where('points', '<', 5)->first();
            if ($loyaltyProgram) {
                $loyaltyProgram->points += 1;
                $loyaltyProgram->save();


                if ($loyaltyProgram->points == 5) {
                    LoyaltyPointNotification::loyaltyPointWonReward($loyaltyProgram);
                    $loyaltyProgram->taken_at = now();
                    $loyaltyProgram->save();
                } else {
                    LoyaltyPointNotification::loyaltyPointAdded($loyaltyProgram);
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
            BookingNotification::bookingCancelled($booking);
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

        $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments', 'invoice']);

        return $booking;
    }

    public function createFromUserNew($data)
    {
        $bookingDetails = $this->returnBookingDetails($data);
        $use_free_services = $data['use_free_services'] ?? false;
        $payment_method = $data['payment_type'] ?? 'wallet';

        $service_amount_data = $use_free_services ?
            $bookingDetails['with_free_services'] :
            $bookingDetails['with_out_free_services'];

        $amount = $service_amount_data['total_amount_after_discount'];
        $user = User::auth();

        if ($payment_method === 'wallet') {
            return $this->processWalletPayment($user, $amount, $data, $service_amount_data, $use_free_services, $bookingDetails);
        } else if ($payment_method === 'stripe') {
            return $this->processStripePayment($user, $amount, $data, $service_amount_data, $use_free_services, $bookingDetails);
        }

        MessageService::abort(422, 'messages.invalid_payment_method');
    }

    private function processWalletPayment($user, $amount, $data, $service_amount_data, $use_free_services, $bookingDetails)
    {
        if (!$service_amount_data['you_have_enough_balance_to_pay']) {
            MessageService::abort(422, 'messages.user.not_enough_balance');
        }

        $user_balance = $user->balance;
        $user->balance = $user_balance - $amount;
        $user->save();

        return $this->createBooking($user, $amount, $data, 'wallet', $use_free_services, $bookingDetails);
    }

    private function processStripePayment($user, $amount, $data, $service_amount_data, $use_free_services, $bookingDetails)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create or get Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
                'phone' => $user->phone,
            ]);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        // Create ephemeral key
        $ephemeralKey = EphemeralKey::create(
            ['customer' => $user->stripe_customer_id],
            ['stripe_version' => '2023-10-16']
        );

        // Create payment intent first
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount * 100, // Convert to cents
            'currency' => 'aed',
            'customer' => $user->stripe_customer_id,
            'setup_future_usage' => 'off_session',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'type' => 'booking',
                'user_id' => $user->id,
                'phone' => $user->phone_code . ' ' . $user->phone,
                'amount' => $amount,
            ],
        ]);

        // Prepare booking data for cache
        $bookingData = [
            'user_id' => $user->id,
            'salon_id' => $data['salon_id'],
            'date' => $data['date'],
            'time' => $data['time'] ?? '00:00:00',
            'services' => $data['services'],
            'address_id' => $data['address_id'] ?? null,
            'coupon_id' => $data['coupon_id'] ?? null,
            'use_free_services' => $use_free_services,
            'created_by' => 'customer',
            'booking_details' => $bookingDetails,
            'notes' => $data['notes'] ?? null,
        ];

        // Store booking data in cache for 1 hour
        $cacheKey = "booking_data_{$paymentIntent->id}";
        Cache::put($cacheKey, $bookingData, 3600);

        Log::info('Booking data stored in cache', [
            'payment_intent_id' => $paymentIntent->id,
            'cache_key' => $cacheKey,
            'user_id' => $user->id,
            'amount' => $amount
        ]);

        return [
            'payment' => [
                'client_secret' => $paymentIntent->client_secret,
                'customer_id' => $user->stripe_customer_id,
                'ephemeral_key' => $ephemeralKey->secret,
                'amount' => $amount,
            ]
        ];
    }

    public function createBooking($user, $amount, $data, $payment_method, $use_free_services, $bookingDetails)
    {
        $data['code'] = rand(100000, 999999);
        $data['user_id'] = $user->id;
        $data['created_by'] = 'customer';
        $data['status'] = 'confirmed';
        $data['time'] = $data['time'] ?? '00:00:00';
        $data['notes'] = $data['notes'] ?? null;

        $booking = Booking::create($data);
        $booking->code = "BOOKING" . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
        $booking->save();

        // Handle address if provided
        if (isset($data['address_id'])) {
            $this->createBookingAddress($booking, $data['address_id']);
        }

        // Create transaction
        $this->createBookingTransaction($booking, $user, $amount, $payment_method);

        // Create salon payment
        $this->createSalonPayment($booking, $user, $amount, $payment_method);

        // Handle free services if used
        if ($use_free_services) {
            $this->handleFreeServices($booking, $bookingDetails);
        }

        // Handle coupon if provided
        if (isset($data['coupon_id'])) {
            $this->createCouponUsage($booking, $data['coupon_id'], $user);
        }

        // Create booking services
        if (isset($data['services'])) {
            $this->createBookingServices($booking, $data);
        }

        // Create invoice
        $invoice = $this->createInvoice($booking);

        BookingNotification::newBooking($booking);

        $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments', 'invoice']);

        return $booking;
    }

    // Helper methods moved from main function
    private function createBookingAddress($booking, $address_id)
    {
        $address = Address::find($address_id);
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

    private function createBookingTransaction($booking, $user, $amount, $payment_method)
    {
        return WalletTransaction::create([
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
            'metadata' => [
                'payment_method' => $payment_method
            ],
        ]);
    }

    private function createSalonPayment($booking, $user, $amount, $payment_method)
    {
        $system_percentage = $this->getSalonSystemPercentage($booking->salon);

        $payment = SalonPayment::create([
            'paymentable_id' => $booking->id,
            'paymentable_type' => Booking::class,
            'user_id' => $user->id,
            'salon_id' => $booking->salon_id,
            'amount' => $amount,
            'currency' => 'AED',
            'method' => $payment_method,
            'status' => 'confirm',
            'is_refund' => false,
            'system_percentage' => $system_percentage,
        ]);

        $payment->code = 'SP' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
        $payment->save();

        return $payment;
    }

    private function getSalonSystemPercentage($salon)
    {
        $type_mapping = [
            'salon' => 'salons_provider_percentage',
            'home_service' => 'home_service_provider_percentage',
            'beautician' => 'makeup_artists_provider_percentage',
            'clinic' => 'clinics_provider_percentage'
        ];

        $setting_key = $type_mapping[$salon->type] ?? null;
        return $setting_key ? (Setting::where('key', $setting_key)->first()->value ?? 0) : 0;
    }

    private function handleFreeServices($booking, $bookingDetails)
    {
        foreach ($bookingDetails['selected_free_services'] as $freeService) {
            if (is_object($freeService)) {
                $freeService->is_used = 1;
                $freeService->booking_id = $booking->id;
                $freeService->save();
            }
        }
    }

    private function createCouponUsage($booking, $coupon_id, $user)
    {
        return CouponUsage::create([
            'coupon_id' => $coupon_id,
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'used_at' => now(),
        ]);
    }

    private function createBookingServices($booking, $data)
    {
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
                'status' => $booking->status,
            ]);
        }
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

        return  $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments', 'invoice']);
    }

    // completed service
    public function completedService(Booking $booking, int $bookingServiceId)
    {
        $bookingService = $booking->bookingServices()->where('id', $bookingServiceId)->first();

        if (!$bookingService) {
            MessageService::abort(422, 'messages.booking.service_not_found');
        }

        if ($bookingService->status === 'completed') {
            MessageService::abort(422, 'messages.booking.service_already_completed');
        }

        if ($bookingService->status === 'cancelled') {
            MessageService::abort(422, 'messages.booking.service_already_cancelled');
        }

        if ($bookingService->status === 'rejected') {
            MessageService::abort(422, 'messages.booking.service_already_rejected');
        }


        $bookingService->status = 'completed';

        Status::create([
            'name' => 'completed',
            'statusable_id' => $bookingService->id,
            'statusable_type' => BookingService::class,
            'created_by' => User::auth()->id,
        ]);

        $bookingService->save();

        $foundServicesNotCompleted = $booking->bookingServices()->whereIn('statues', ['pending', 'confirmed'])->first();

        if (!$foundServicesNotCompleted) {
            $booking->status = 'completed';
            $booking->save();

            Status::create([
                'name' => 'completed',
                'statusable_id' => $booking->id,
                'statusable_type' => Booking::class,
                'created_by' => User::auth()->id,
            ]);

            BookingNotification::bookingCompleted($booking);
        } else {
            BookingNotification::bookingServiceCompleted($booking, $bookingService);
        }



        return $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments', 'invoice']);
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

        return $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments', 'invoice']);
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

        return $booking->load(['user', 'salon', 'bookingServices.service', 'bookingDates', 'transactions', 'couponUsage', 'payments', 'invoice']);
    }


    public function createInvoice(Booking $booking)
    {
        $invoice = Invoice::create([
            'code' => 'GLT-INV-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
            'booking_id' => $booking->id,
            'tax' => 0,
        ]);

        return $invoice;
    }
}
