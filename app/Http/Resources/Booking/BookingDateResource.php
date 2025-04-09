<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingDateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'booking_id'       => $this->booking_id,
            'date'             => $this->date?->format('Y-m-d'),
            'time'             => $this->time?->format('H:i'),
            'full_datetime'    => $this->full_datetime,
            'created_by'       => $this->created_by,
            // 'created_by_label' => $this->created_by_label,
            'status'           => $this->status,
            // 'status_label'     => $this->status_label,

            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'       => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
