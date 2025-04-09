<?php

namespace App\Http\Resources\Salons;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Salons\SalonResource;

class SalonHolidayResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'salon_id'      => $this->salon_id,
            'holiday_date'  => $this->holiday_date?->format('Y-m-d'),
            'reason'        => $this->reason,
            'is_full_day'   => $this->is_full_day,
            'is_partial'    => $this->is_partial,
            'start_time'    => $this->start_time?->format('H:i'),
            'end_time'      => $this->end_time?->format('H:i'),

            'salon'         => new SalonResource($this->whenLoaded('salon')),

            'created_at'    => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
