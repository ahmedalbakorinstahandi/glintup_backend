<?php

namespace App\Http\Resources\General;

use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'phone_number'  => $this->phone_number,
            'content'       => $this->content,
            'hide_identity' => $this->hide_identity,
            'reviewed_by'   => $this->reviewed_by,
            'reviewed_at'   => $this->reviewed_at,
            'user_id'      => $this->hide_identity ? null : $this->user_id,
            'user'          => $this->hide_identity ? null : new UserResource($this->whenLoaded('user')),
            'reviewer'      => new UserResource($this->whenLoaded('reviewer')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
