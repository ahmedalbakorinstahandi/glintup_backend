<?php

namespace App\Http\Resources\Rewards;

use Illuminate\Http\Resources\Json\JsonResource;

class GiftResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'icon'         => $this->icon,
            'icon_url'     => $this->icon_url,
            'is_active'    => $this->is_active,
            'order'        => $this->order,

            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
