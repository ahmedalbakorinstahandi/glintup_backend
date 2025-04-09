<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Booking\BookingResource;
use App\Http\Resources\Booking\CouponResource;

class CouponUsageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'coupon_id'  => $this->coupon_id,
            'user_id'    => $this->user_id,
            'booking_id' => $this->booking_id,
            'used_at'    => $this->used_at?->format('Y-m-d H:i:s'),

            'coupon'     => new CouponResource($this->whenLoaded('coupon')),
            'user'       => new UserResource($this->whenLoaded('user')),
            'booking'    => new BookingResource($this->whenLoaded('booking')),
        ];
    }
}
