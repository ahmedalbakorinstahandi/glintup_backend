<?php

namespace App\Http\Resources\Services;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Salons\SalonResource;
use App\Http\Resources\Services\GroupServiceResource;

class GroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'salon_id'    => $this->salon_id,
            'name'        => $this->name,
            'key'         => $this->key,
            'can_edit'    => $this->canSalonEdit(),
            'orders'      => $this->orders,

            // العلاقات
            'salon'          => new SalonResource($this->whenLoaded('salon')),
            // 'group_services' => GroupServiceResource::collection($this->whenLoaded('groupServices')),
            'group_services' => GroupServiceResource::collection($this->getGroupServices()),

            // الوقت
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
