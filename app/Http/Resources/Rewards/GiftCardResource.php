<?php

namespace App\Http\Resources\Rewards;

use App\Http\Resources\Salons\SalonResource;
use App\Http\Resources\Services\ServiceResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;

class GiftCardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'code'           => $this->code,
            'sender_id'      => $this->sender_id,
            'recipient_id'   => $this->recipient_id,
            'phone_code'     => $this->phone_code,
            'phone'          => $this->phone,
            'full_phone'     => $this->full_phone,
            'type'           => $this->type,
            'amount'         => $this->amount,
            'currency'       => $this->currency,
            'services'       => $this->services,
            'services_data' => ServiceResource::collection($this->getServicesData()),
            'tax'            => $this->tax,
            'message'        => $this->message,
            'is_used'        => $this->is_used,

            'sender'         => new UserResource($this->whenLoaded('sender')),
            'recipient'      => new UserResource($this->whenLoaded('recipient')),
            'salon'          => new SalonResource($this->whenLoaded('salon')),

            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
