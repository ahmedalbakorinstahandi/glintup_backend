<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Booking\BookingResource;

class RefundResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'booking_id'       => $this->booking_id,
            'user_id'          => $this->user_id,
            'amount'           => $this->amount,
            'formatted_amount' => $this->formatted_amount,
            'reason'           => $this->reason,
            'status'           => $this->status,
            'is_approved'      => $this->is_approved,
            'booking'          => new BookingResource($this->whenLoaded('booking')),
            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'       => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
