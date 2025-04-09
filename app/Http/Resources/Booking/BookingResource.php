<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Salons\SalonResource;
use App\Http\Resources\Booking\BookingServiceResource;
use App\Http\Resources\Booking\BookingPaymentResource;
use App\Http\Resources\Booking\InvoiceResource;
use App\Http\Resources\Users\RefundResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'code'             => $this->code,
            'user_id'          => $this->user_id,
            'salon_id'         => $this->salon_id,
            'date'             => $this->date?->format('Y-m-d'),
            'time'             => $this->time?->format('H:i'),
            'status'           => $this->status,
            // 'status_label'     => $this->status_label,
            'payment_status'   => $this->payment_status,
            // 'payment_status_label' => $this->payment_status_label,
            'notes'            => $this->notes,
            'salon_notes'      => $this->salon_notes,

            // العلاقات
            'user'             => new UserResource($this->whenLoaded('user')),
            'salon'            => new SalonResource($this->whenLoaded('salon')),
            'booking_services' => BookingServiceResource::collection($this->whenLoaded('bookingServices')),
            'payments'         => BookingPaymentResource::collection($this->whenLoaded('payments')),
            'invoice'          => new InvoiceResource($this->whenLoaded('invoice')),
            'refund'           => new RefundResource($this->whenLoaded('refund')),

            // التواريخ
            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'       => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
