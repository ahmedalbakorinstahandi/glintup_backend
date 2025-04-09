<?php

namespace App\Http\Resources\General;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;

class ActivityLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'action'      => $this->action,
            'description' => $this->description,

            'user'        => new UserResource($this->whenLoaded('user')),

            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
