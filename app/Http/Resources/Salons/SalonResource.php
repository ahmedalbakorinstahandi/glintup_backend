<?php

namespace App\Http\Resources\Salons;

use App\Http\Resources\General\ImageResource;
use App\Http\Resources\Rewards\GiftCardResource;
use App\Http\Resources\Rewards\LoyaltyPointResource;
use App\Http\Resources\Services\GroupResource;
use App\Http\Resources\Services\ReviewResource;
use App\Http\Resources\Services\ServiceResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Users\UserResource;
use App\Models\Salons\Salon;
use App\Models\Salons\SalonSocialMidiaSite;
use App\Models\Users\User;
use Google\Api\ResourceDescriptor\History;

class SalonResource extends JsonResource
{
    public function toArray($request)
    {

        $user = User::auth();

        $is_admin = $user->isAdmin();

        $local_lang = app()->getLocale();

        $this_function_is_show = $request->route()->getActionMethod() == 'show';

        return [
            'id'              => $this->id,
            'owner_id'        => $this->owner_id,
            'merchant_legal_name' => $this->merchant_legal_name,
            'merchant_commercial_name' => $this->merchant_commercial_name,
            'address'         => $this->address,
            'city_street_name' => $this->city_street_name,
            'contact_name'    => $this->contact_name,
            'contact_number'  => $this->contact_number,
            'contact_email'   => $this->contact_email,
            'business_contact_name' => $this->business_contact_name,
            'business_contact_email' => $this->business_contact_email,
            'business_contact_number' => $this->business_contact_number,
            'name'            => $this->name,
            'icon'            => $this->icon,
            'icon_url'        => $this->icon_url,
            'types'           => $this->types,
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
            'block_message'   => $this->block_message,
            'bio'             => $this->bio,
            'tags'            => $this->tags,
            'is_open' => $this->isOpen(),
            'type'            => $this->type,
            'country'         => $this->country,
            'city'            => $this->city,
            'distance' => $this->when($user->isCustomer(), $this->getDistance($user)),
            'my_loyalty_service' => $this->when($user->isCustomer(), new LoyaltyPointResource($this->MyLoyaltyService())),
            'my_gift_cards' => $this->when($user->isCustomer(), GiftCardResource::collection($this->MyGiftCards())),
            'average_rating' => $this->reviews->avg('rating'),
            'is_most_booked' => $this->isMostBooked(),
            'bookings_count' => $this->when($is_admin,  $this->bookings->where('status', 'completed')->count()),
            //TODO اجمالي الايرادات 
            'total_revenue' => $this->when($is_admin,  5000),
            'total_reviews'   => $this->reviews->count(),
            'owner'           => new UserResource($this->whenLoaded('owner')),
            'working_status' => $this->getWorkingStatus($local_lang),
            'rating_percentage' => $this->when($this_function_is_show, $this->getRatingPercentageAttribute()),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'social_media_sites' => SocialMediaSiteResource::collection($this->whenLoaded('socialMediaSites')),
            'most_booked_services' => $this->when($this_function_is_show, ServiceResource::collection($this->mostBookedServices())),
            'latest_reviews' => $this->when($this_function_is_show, ReviewResource::collection($this->reviews()->latest()->take(5)->get())),
            "working_hours" => WorkingHourResource::collection($this->whenLoaded('workingHours')),

            // if not null
            'loyalty_service' => new ServiceResource($this->whenLoaded('loyaltyService')),
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'      => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
