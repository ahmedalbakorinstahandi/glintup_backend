<?php

namespace App\Http\Resources\Salons;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkingHourResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'salon_id'       => $this->salon_id,
            'day_of_week'    => $this->day_of_week,
            // 'full_day_label' => $this->full_day_label,
            'opening_time'   => $this->opening_time?->format('H:i'),
            'closing_time'   => $this->closing_time?->format('H:i'),
            'is_closed'      => $this->is_closed,
            'break_start'    => $this->break_start?->format('H:i'),
            'break_end'      => $this->break_end?->format('H:i'),

            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
