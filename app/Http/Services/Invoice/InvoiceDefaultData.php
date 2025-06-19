<?php

namespace App\Http\Services\Invoice;

use App\Models\Booking\Invoice;

class InvoiceDefaultData
{
    public static function getDefaultData($invoice, $lang): array
    {

        $booking = $invoice->booking;

        $salon = $booking->salon;

        $services = $booking->bookingServices;

        $services = [];
        foreach ($services as $service) {
            $services[] = [
                'name' => $service->service->name[$lang],
                'duration' => $service->duration_minutes,
                'discounted_price' => $service->getFinalPriceAttribute()
            ];
        }

        return [
            'logo' => 'https://i.ibb.co/zH8KXpy6/Group-3.png',
            'app_name' => 'GlintUp',
            'salon' => [
                'name' => $salon->merchant_commercial_name,
                'provider_type' => trans("enums.salon_type.{$salon->type}", [], $lang),
                'address' => $salon->city_street_name,
                'tax_number' => $salon->vat_number
            ],
            'invoice' => [
                'number' => $invoice->code,
                'booking_number' => $booking->code,
                'date' => $invoice->created_at->format('d-m-Y'),
                'time' => $invoice->created_at->format('H:i'),
                'payment_status' => trans("enums.payment_method.{$invoice->status}", [], $lang),
                'total_before_discount' => $booking->getTotalPriceBeforeDiscountAttribute(),
                'coupon_discount' => $booking->couponUsage ? ($booking->couponUsage->coupon->discount_value . ' ' . ($booking->couponUsage->coupon->discount_type == 'percentage' ? '%' : 'AED')) : null,
                'total_after_discount' => $booking->getTotalPriceAttribute(),
                'notes' => ''
            ],
            'customer' => [
                'name' => $booking->user->first_name . ' ' . $booking->user->last_name,
                'phone' => $booking->user->phone_code . $booking->user->phone
            ],
            'services' => $services,
            'currency' => 'AED',
            'footer' => [
                'thanks_message' => trans('messages.invoice.thanks_message', [], $lang),
                'cancel_policy' => trans('messages.invoice.cancel_policy', [], $lang),
                'service_policy' => trans('messages.invoice.service_policy', [], $lang)
            ]
        ];
    }
}
