<?php

namespace App\Http\Resources\Services;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Services\GroupResource;
use App\Http\Resources\Services\ServiceResource;
use App\Http\Resources\Salons\SalonResource;

class GroupServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'group_id'   => $this->group_id,
            'service_id' => $this->service_id,
            'salon_id'   => $this->salon_id,
            'order'      => $this->order,

            'group'      => new GroupResource($this->whenLoaded('group')),
            'service'    => new ServiceResource($this->whenLoaded('service')),
            'salon'      => new SalonResource($this->whenLoaded('salon')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
