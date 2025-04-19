<?php

namespace App\Http\Resources\Rewards;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Salons\SalonResource;

class LoyaltyPointResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'salon_id'    => $this->salon_id,
            'points'      => $this->points,
            // 'description' => $this->description,
            'taken_at'    => $this->taken_at ? (new \DateTime($this->taken_at))->format('Y-m-d H:i:s') : null,
            'used_at'     => $this->used_at ? (new \DateTime($this->used_at))->format('Y-m-d H:i:s') : null,

            'free_service' => new FreeServiceResource($this->whenLoaded('freeService')),
            'user'        => new UserResource($this->whenLoaded('user')),
            'salon'       => new SalonResource($this->whenLoaded('salon')),

            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
