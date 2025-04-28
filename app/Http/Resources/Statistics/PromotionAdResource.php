<?php

namespace App\Http\Resources\Statistics;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Salons\SalonResource;

class PromotionAdResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'salon_id'     => $this->salon_id,
            'title'        => $this->title,
            'description'  => $this->description,
            'image'        => $this->image,
            'image_url'    => $this->image ? asset('storage/' . $this->image) : null,
            'valid_from'   => $this->valid_from?->format('Y-m-d'),
            'valid_to'     => $this->valid_to?->format('Y-m-d'),
            'is_active'    => $this->is_active,
            'views'        => $this->views,
            'clicks'       => $this->clicks,
            'status'       => $this->status,

            'salon'        => new SalonResource($this->whenLoaded('salon')),

            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
