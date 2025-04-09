<?php

namespace App\Http\Resources\Salons;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;

class SalonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'owner_id'        => $this->owner_id,
            'name'            => $this->name,
            'icon'            => $this->icon,
            'icon_url'        => $this->icon_url,
            'phone'           => $this->phone,
            'phone_code'      => $this->phone_code,
            'full_phone'      => $this->full_phone,
            'email'           => $this->email,
            'description'     => $this->description,
            'location'        => $this->location,
            'location_coords' => $this->location_coordinates,
            'is_approved'     => $this->is_approved,
            'is_active'       => $this->is_active,
            'type'            => $this->type,
            'country'         => $this->country,
            'city'            => $this->city,

            'owner'           => new UserResource($this->whenLoaded('owner')),

            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'      => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
