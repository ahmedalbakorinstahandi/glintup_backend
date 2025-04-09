<?php

namespace App\Http\Resources\Rewards;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Services\ServiceResource;

class FreeServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'service_id'    => $this->service_id,
            'source'        => $this->source,
            // 'source_label'  => $this->source_label,
            'is_used'       => $this->is_used,

            'user'          => new UserResource($this->whenLoaded('user')),
            'service'       => new ServiceResource($this->whenLoaded('service')),

            'created_at'    => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
