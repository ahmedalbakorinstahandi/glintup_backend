<?php

namespace App\Http\Resources\Services;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Salons\SalonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'user_id'            => $this->user_id,
            'salon_id'           => $this->salon_id,
            'rating'             => $this->rating,
            'stars'              => $this->stars,
            'comment'            => $this->comment,
            'salon_reply'        => $this->salon_reply,
            'salon_report'       => $this->salon_report,
            'salon_reported_at'  => $this->salon_reported_at,

            'user'   => new UserResource($this->whenLoaded('user')),
            'salon'  => new SalonResource($this->whenLoaded('salon')),

            'created_at'         => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'         => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
