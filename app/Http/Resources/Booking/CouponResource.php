<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Salons\SalonResource;

class CouponResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'code'               => $this->code,
            'salon_id'           => $this->salon_id,
            'discount_type'      => $this->discount_type,
            'discount_value'     => $this->discount_value,
            'discount_label'     => $this->discount_label,
            'max_uses'           => $this->max_uses,
            'max_uses_per_user'  => $this->max_uses_per_user,
            'start_date'         => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date'           => $this->end_date?->format('Y-m-d H:i:s'),
            'min_age'            => $this->min_age,
            'max_age'            => $this->max_age,
            'gender'             => $this->gender,
            'is_active'          => $this->is_active,
            'is_expired'         => $this->is_expired,

            'salon'              => new SalonResource($this->whenLoaded('salon')),

            'created_at'         => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'         => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
