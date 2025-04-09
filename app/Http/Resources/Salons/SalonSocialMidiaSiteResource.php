<?php

namespace App\Http\Resources\Salons;

use Illuminate\Http\Resources\Json\JsonResource;

class SalonSocialMidiaSiteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'salon_id'             => $this->salon_id,
            'social_media_site_id' => $this->social_media_site_id,
            'link'                 => $this->link,

            'salon'                => new SalonResource($this->whenLoaded('salon')),
            'social_media_site'    => new SocialMidiaSiteResource($this->whenLoaded('socialMediaSite')),

            'created_at'           => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'           => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
