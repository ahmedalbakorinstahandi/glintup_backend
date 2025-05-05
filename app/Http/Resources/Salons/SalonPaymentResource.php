<?php

namespace App\Http\Resources\Salons;

use App\Http\Resources\Booking\BookingResource;
use App\Http\Resources\Rewards\GiftCardResource;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SalonPaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'code'              => $this->code,
            'amount'            => $this->amount,
            'currency'          => $this->currency,
            'method'            => $this->method,
            'status'            => $this->status,
            'is_refund'         => $this->is_refund,
            'system_percentage' => $this->system_percentage,
            'paymentable_id'    => $this->paymentable_id,
            'paymentable_type'  => $this->paymentable_type,
            'user_id'           => $this->user_id,
            'user'             => new UserResource($this->whenLoaded('user')),
            'salon_id'          => $this->salon_id,
            'salon'             => new SalonResource($this->whenLoaded('salon')),

            // 'paymentable'      => $this->paymentable_type == "App\\Models\\Booking\\Booking" ?
            //     BookingResource::make($this->whenLoaded('paymentable')) :
            //     GiftCardResource::make($this->whenLoaded('paymentable')),

            // gift card relation if paymentable_type is GiftCard
            'gift_card' => $this->paymentable_type == "App\\Models\\Rewards\\GiftCard" ?
                GiftCardResource::make($this->whenLoaded('paymentable')) :
                null,
            // booking relation if paymentable_type is Booking
            'booking' => $this->paymentable_type == "App\\Models\\Booking\\Booking" ?
                BookingResource::make($this->whenLoaded('paymentable')) :
                null,

            'created_at'        => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'        => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
