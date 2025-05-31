<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Services\ServiceResource;

class BookingServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'booking_id' => $this->booking_id,
            'service_id' => $this->service_id,

            'price'               => $this->price,
            'final_price'         => $this->final_price,
            'currency'            => $this->currency,
            'discount_percentage' => $this->discount_percentage,
            'start_date_time'     => $this->start_date_time ? $this->start_date_time->format('Y-m-d H:i:s') : null,
            'end_date_time'       => $this->end_date_time ? $this->end_date_time->format('Y-m-d H:i:s') : null,
            'duration_minutes'    => $this->duration_minutes,
            'status'              => $this->status,
            'notes'               => $this->notes,

            'service'    => new ServiceResource($this->whenLoaded('service')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
