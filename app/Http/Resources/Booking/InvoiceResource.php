<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'code'           => $this->code,
            'booking_id'     => $this->booking_id,
            'tax'            => $this->tax,
            'total_before_discount' => $this->booking->getTotalPriceBeforeDiscountAttribute(),
            'total_amount'   => $this->getTotalAmountAttribute(),
            'total_paid'     => $this->booking->payments->where('status', 'confirm')->sum('amount'),
            'discount'       => $this->booking->couponUsage?->coupon?->discount_value ?? 0,
            'coupon_discount' => $this->getCouponDiscount(),
            'status'         => $this->status,
            'invoice_url'    => url('/api/invoices/' . $this->code),

            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function getCouponDiscount()
    {
        if (!$this->booking->couponUsage || !$this->booking->couponUsage->coupon) {
            return '0 AED';
        }

        $coupon = $this->booking->couponUsage->coupon;
        $value = $coupon->discount_value;
        $type = $coupon->discount_type;

        return $value . ' ' . ($type === 'percentage' ? '%' : 'AED');
    }
}
