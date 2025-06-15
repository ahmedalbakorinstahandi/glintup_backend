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
use Illuminate\Support\Facades\Auth;

class SalonResource extends JsonResource
{
    public function toArray($request)
    {
        $is_admin = false;
        $is_customer = false;

        $is_salon_or_admin = false;

        if (Auth::check()) {
            $user = User::auth();

            $is_admin = $user->isAdmin();
            $is_customer = $user->isCustomer();
            $is_salon_or_admin = $is_admin || ($user->isUserSalon() && $user->salon->id == $this->id);
        }

        $local_lang = app()->getLocale();

        $this_function_is_show = $request->route()->getActionMethod() == 'show';


        $data = [
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
            'service_location' => $this->service_location,
            'service_location_text' => $this->getServiceLocationTextAttribute(),
            'bank_name' => $this->when($is_salon_or_admin, $this->bank_name),
            'bank_account_number' => $this->when($is_salon_or_admin, $this->bank_account_number),
            'bank_account_holder_name' => $this->when($is_salon_or_admin, $this->bank_account_holder_name),
            'bank_account_iban' => $this->when($is_salon_or_admin, $this->bank_account_iban),
            'services_list' => $this->when($is_salon_or_admin, $this->services_list),

            'trade_license' => $this->when($is_salon_or_admin, $this->trade_license),
            'vat_certificate' => $this->when($is_salon_or_admin, $this->vat_certificate),
            'bank_account_certificate' => $this->when($is_salon_or_admin, $this->bank_account_certificate),
            'vat_number' => $this->when($is_salon_or_admin, $this->vat_number),

            'trade_license_url' => $this->when($is_salon_or_admin, $this->trade_license_url),
            'vat_certificate_url' => $this->when($is_salon_or_admin, $this->vat_certificate_url),
            'bank_account_certificate_url' => $this->when($is_salon_or_admin, $this->bank_account_certificate_url),
            'services_list_url' => $this->when($is_salon_or_admin, $this->services_list_url),


            'discount_percentage' =>  $this->when(request()->has('filter_provider') && request()->filter_provider === 'discount', $this->getServiceWithHighestDiscountPercentage()),
            'average_rating' => number_format($this->reviews->avg('rating'), 1),
            'is_most_booked' => $this->isMostBooked(),
            'bookings_count' => $this->when($is_salon_or_admin,  $this->bookings->where('status', 'completed')->count()),
            //TODO اجمالي الايرادات 
            'total_revenue' => $this->when($is_salon_or_admin,  5000),
            'can_review' => $this->when($is_customer, $this->canUserReview()),
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


        if ($is_customer) {
            $user_data = [
                'distance' =>  $this->when($is_customer,  $this->getDistance($user)),
                'my_loyalty_points' => $this->when($is_customer, new LoyaltyPointResource($this->whenLoaded('myLoyaltyPoints'))),
                'my_gift_cards' => $this->when($is_customer, GiftCardResource::collection($this->MyGiftCards())),
            ];

            $data = array_merge($data, $user_data);
        }


        return $data;
    }
}
