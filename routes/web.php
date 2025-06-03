<?php

use App\Models\Booking\Booking;
use App\Models\Booking\Invoice;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invoice', function () {
    $invoice = (object) [
        'number' => 'INV-2025-001',
        'date' => now()->format('Y-m-d'),
        'time' => now()->format('H:i'),
        'total_before_discount' => 350,
        'coupon_discount' => 50,
        'total_after_discount' => 300,
        'payment_status' => 'مدفوع',
        'tax_number' => '123456789',
        'services' => [
            (object)[
                'name' => 'قص شعر',
                'discounted_price' => 100,
                'duration' => 30,
                'executed_at' => '14:00',
            ],
            (object)[
                'name' => 'تنظيف بشرة',
                'discounted_price' => 250,
                'duration' => 60,
                'executed_at' => '15:00',
            ],
        ],
    ];

    $salon = (object) [
        'name' => 'صالون لمسة أناقة',
        'provider_type' => 'صالون',
        'logo_url' => 'https://thumbs.dreamstime.com/b/unique-beauty-salon-logo-vector-illustration-d-design-unique-beauty-salon-logo-vector-illustration-d-design-refined-322496221.jpg',
        'address' => 'دمشق - المزة - جانب البنك',
        'commercial_number' => 'CN-789456',
        'registration_number' => 'CR-147852',
    ];

    $customer = (object) [
        'name' => 'آية أحمد',
        'phone' => '+971 526374859',
    ];

    return view('invoice', compact('invoice', 'salon', 'customer'))->with('currency', 'AED');
});



Route::get('/invoices/{id}', function ($id) {
    $myInvoice = Invoice::find($id);

    if (!$myInvoice) {
        return redirect()->back()->with('error', 'Invoice not found');
    }


    $myBooking = Booking::find($myInvoice->booking_id);

    if (!$myBooking) {
        return redirect()->back()->with('error', 'Booking not found');
    }

    $bookingServices = $myBooking->bookingServices->where('status', 'completed');


    $services = [];

    $lang = app()->getLocale();

    foreach ($bookingServices as $bookingService) {
        $services[] = (object) [
            'name' => $bookingService->service->name[$lang],
            'discounted_price' => $bookingService->price,
            'duration' => $bookingService->duration_minutes,
            'executed_at' => $bookingService->start_date_time,
        ];
    }
    $invoice = (object) [
        'number' => $myInvoice->code,
        'date' => $myInvoice->created_at->format('Y-m-d'),
        'time' => $myInvoice->created_at->format('H:i'),
        'total_before_discount' => $myInvoice->amount,
        'coupon_discount' => $myInvoice->discount,
        'total_after_discount' => $myInvoice->amount - $myInvoice->discount,
        'payment_status' => $myInvoice->status,
        'tax_number' => '123456789',
        'services' => $services,
    ];

    $salon = (object) [
        'name' => $myBooking->salon->name,
        'provider_type' => $myBooking->salon->type,
        'logo_url' => $myBooking->salon->icon_url,
        'address' => $myBooking->salon->city_street_name,
        'commercial_number' => $myBooking->salon->commercial_number,
        'registration_number' => $myBooking->salon->registration_number,

    ];

    $customer = (object) [
        'name' => $myBooking->user->full_name,
        'phone' => $myBooking->user->full_phone,
    ];

    return view('invoice', compact('invoice', 'salon', 'customer'))->with('currency', 'AED');
});
