<?php

namespace App\Http\Resources\Salons;

use App\Http\Resources\General\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Models\Salons\Salon;
use App\Models\Salons\SalonSocialMidiaSite;
use App\Models\Users\User;

class SalonResource extends JsonResource
{
    public function toArray($request)
    {

        $user = User::auth();

        return [
            'id'              => $this->id,
            'owner_id'        => $this->owner_id,
            'name'            => $this->name,
            'icon'            => $this->icon,
            'icon_url'        => $this->icon_url,
            'phone'           => $this->phone,
            'whats_app_link' => $this->whats_app_link,
            'phone_code'      => $this->phone_code,
            'full_phone'      => $this->full_phone,

            'email'           => $this->email,
            'description'     => $this->description,
            'location'        => $this->location,
            'location_coords' => $this->location_coordinates,
            'is_approved'     => $this->is_approved,
            'is_active'       => $this->is_active,
            'type'            => $this->type,
            'country'         => $this->country,
            'city'            => $this->city,
            'distance' => $this->when($user->isCustomer(), $this->getDistance($user)),
            'average_rating' => $this->reviews->avg('rating'),
            'total_reviews'   => $this->reviews->count(),
            'owner'           => new UserResource($this->whenLoaded('owner')),
            'working_status' => "مفتوح (8:00 AM - 10:00 PM)",
            'rating_percentage' => $this->getRatingPercentageAttribute(),
            'images' => ImageResource::collection($this->whenLoaded('images')),

            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'      => $this->updated_at?->format('Y-m-d H:i:s'),


            'social_media_sites' => SocialMediaSiteResource::collection($this->whenLoaded('socialMediaSites')),
        ];
    }
}
