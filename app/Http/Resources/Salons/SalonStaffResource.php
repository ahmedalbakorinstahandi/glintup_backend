<?php

namespace App\Http\Resources\Salons;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Salons\SalonResource;

class SalonStaffResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'salon_id'   => $this->salon_id,
            'user_id'    => $this->user_id,
            'position'   => $this->position,
            'is_active'  => $this->is_active,

            'salon'      => new SalonResource($this->whenLoaded('salon')),
            'user'       => new UserResource($this->whenLoaded('user')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
