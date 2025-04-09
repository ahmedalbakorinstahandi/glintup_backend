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
            'amount'         => $this->amount,
            'tax'            => $this->tax,
            'discount'       => $this->discount,
            'total_amount'   => $this->total_amount,
            'status'         => $this->status,
            // 'status_label'   => $this->status_label,

            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
