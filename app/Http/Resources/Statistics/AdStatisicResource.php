<?php

namespace App\Http\Resources\Statistics;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Http\Resources\Statistics\PromotionAdResource;

class AdStatisicResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'promotion_ad_id'  => $this->promotion_ad_id,
            'viewed'           => $this->viewed,
            'clicked'          => $this->clicked,

            'user'             => new UserResource($this->whenLoaded('user')),
            'promotion_ad'     => new PromotionAdResource($this->whenLoaded('promotionAd')),

            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'       => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
