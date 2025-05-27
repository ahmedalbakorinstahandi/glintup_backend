<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'phone_code'  => $this->phone_code,
            'phone'       => $this->phone,
            'full_phone'  => $this->full_phone,
            'avatar'      => $this->avatar,
            'avatar_url'  => $this->avatar_url,
            'user_id'     => $this->user_id,
            'user'        => new UserResource($this->whenLoaded('user')),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
