<?php

namespace App\Http\Resources\Services;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Salons\SalonResource;
use App\Http\Resources\Rewards\FreeServiceResource;
use App\Http\Resources\Booking\BookingServiceResource;
use App\Http\Resources\Services\GroupServiceResource;

class ServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'salon_id'            => $this->salon_id,
            'name'                => $this->name,
            'description'         => $this->description,
            'icon'                => $this->icon,
            'icon_url'            => $this->icon_url,
            'duration_minutes'    => $this->duration_minutes,
            'price'               => $this->price,
            'final_price'         => $this->final_price,
            'currency'            => $this->currency,
            'discount_percentage' => $this->discount_percentage,
            'gender'              => $this->gender,
            'is_active'           => $this->is_active,
            'is_home_service'     => $this->is_home_service,
            'is_beautician'       => $this->is_beautician,
            'capacity'            => $this->capacity,
            'order'               => $this->order,

            // العلاقات
            'salon'          => new SalonResource($this->whenLoaded('salon')),
            'free_services'  => FreeServiceResource::collection($this->whenLoaded('freeServices')),
            'bookings'       => BookingServiceResource::collection($this->whenLoaded('bookings')),
            'group_services' => GroupServiceResource::collection($this->whenLoaded('groupServices')),

            // الوقت
            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
