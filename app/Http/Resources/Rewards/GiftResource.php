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
            'type'         => $this->type,
            // 'type_label'   => $this->type_label,
            'amount'       => $this->amount,
            'currency'     => $this->currency,
            'order'        => $this->order,

            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
