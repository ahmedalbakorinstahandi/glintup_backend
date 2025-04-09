<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingPaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'booking_id'        => $this->booking_id,
            'amount'            => $this->amount,
            'amount_formatted'  => $this->amount_formatted,
            'type'              => $this->type,
            // 'type_label'        => $this->type_label,
            'method'            => $this->method,
            'status'            => $this->status,
            // 'status_label'      => $this->status_label,

            'created_at'        => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'        => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
