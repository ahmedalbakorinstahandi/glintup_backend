<?php

namespace App\Http\Services\Salons;

use App\Http\Permissions\Salons\SalonPermission;
use App\Http\Resources\Services\GroupResource;
use App\Models\Salons\Salon;
use App\Models\Salons\SalonPermission as SalonPermissionModel;
use App\Models\Salons\UserSalonPermission;
use App\Models\Services\Group;
use App\Models\Users\User;
use App\Services\FilterService;
use App\Services\ImageService;
use App\Services\MessageService;
use App\Services\PhoneService;

class SalonService
{
    public function getPermissions()
    {
        $user = User::auth();

        $userPermissions = UserSalonPermission::where('user_id', $user->id)
            ->with('permission')
            ->get();

        $permissions = SalonPermissionModel::whereIn(
            'id',
            $userPermissions->pluck('permission_id')
        )->get();


        // orders by filed `orders` in salon_permissions table
        $permissions = $permissions->sortBy(function ($permission) {
            return $permission->order;
        });

        return $permissions;
    }

    //getSalonData
    public function getSalonData()
    {
        $user = User::auth();

        $salon = $user->salon;

        $salon->load([
            'socialMediaSites',
            'images',
            'workingHours',
            'owner',
            'latestReviews',
            'loyaltyService',
        ]);

        return $salon;
    }

    public function index($data)
    {
        $query = Salon::query()->with(['loyaltyService', 'owner', 'images']);

        $searchFields = [
            'name',
            'email',
            'description',
            'location',
            'city',
            'country',
            'merchant_legal_name',
            'merchant_commercial_name',
            'address',
            'city_street_name',
            'contact_name',
            'contact_email',
            'business_contact_name',
            'business_contact_email',
            'bio',
            'tags',
        ];
        $numericFields = [];
        $dateFields = ['created_at'];
        $exactMatchFields = ['is_active', 'is_approved', 'type', 'city', 'country', 'id'];
        $inFields = ['id', 'type'];

        $query = SalonPermission::filterIndex($query);

        // filter_provider 
        if (isset($data['filter_provider']) && $data['filter_provider'] == 'discount') {
            $query->whereHas('services', function ($query) {
                $query->where('discount_percentage', '>', 0);
            });

            $query->orderByDesc(function ($query) {
                return $query->from('services')
                    ->whereColumn('services.salon_id', 'salons.id')
                    ->where('discount_percentage', '>', 0)
                    ->select('discount_percentage')
                    ->orderByDesc('discount_percentage')
                    ->limit(1);
            });
        }

        if (isset($data['filter_provider']) && $data['filter_provider'] == 'trending') {
            // Get salons with completed bookings in last 14 days
            $query->withCount(['bookings' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(14))
                    ->where('status', 'completed');
            }])
                ->orderBy('bookings_count', 'desc');

            // If no salons have bookings in last 14 days, show all salons
            if (!$query->having('bookings_count', '>', 0)->exists()) {
                $query = Salon::query()
                    ->with(['loyaltyService', 'owner', 'images']);
            }
        }

        if (isset($data['filter_provider']) && $data['filter_provider'] == 'nearby' && isset($data['latitude']) && isset($data['longitude'])) {
            $latitude = $data['latitude'];
            $longitude = $data['longitude'];

            $query->selectRaw(
                "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) as distance",
                [$latitude, $longitude, $latitude]
            )
            ->orderBy('distance');
        }

        return FilterService::applyFilters(
            $query,
            $data,
            $searchFields,
            $numericFields,
            $dateFields,
            $exactMatchFields,
            $inFields
        );
    }

    public function show($id)
    {
        $salon = Salon::where('id', $id)->first();

        if (!$salon) {
            MessageService::abort(404, 'messages.salon.item_not_found');
        }


        $salon->load(['salonSocialMediaSites.socialMediaSite', 'images', 'workingHours', 'owner', 'latestReviews', 'loyaltyService', 'myLoyaltyPoints.freeService.service', 'myLoyaltyPoints.user', 'myLoyaltyPoints.salon']);

        return $salon;
    }

    public function create($data)
    {

        $salon = Salon::create($data);

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $image) {

                $salon->images()->create(
                    [
                        'path' => $image,
                        'type' => 'salon_cover',
                    ]
                );
            }
        }


        $salon->load(['socialMediaSites', 'images', 'workingHours', 'owner', 'latestReviews', 'loyaltyService']);

        return $salon;
    }

    public function update($salon, $data)
    {

        // if (isset($data['phone'])) {
        //     $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        //     $data['phone_code'] = $phoneParts['country_code'];
        //     $data['phone'] = $phoneParts['national_number'];
        // }


        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $image) {

                $salon->images()->create(
                    [
                        'path' => $image,
                        'type' => 'salon_cover',
                    ]
                );
            }
        }

        // images_remove
        if (isset($data['images_remove']) && is_array($data['images_remove'])) {
            ImageService::removeImages($data['images_remove']);
        }

        $salon->update(
            [
                'merchant_legal_name' => $data['merchant_legal_name'] ?? $salon->merchant_legal_name,
                'merchant_commercial_name' => $data['merchant_commercial_name'] ?? $salon->merchant_commercial_name,
                'address' => $data['address'] ?? $salon->address,
                'city_street_name' => $data['city_street_name'] ?? $salon->city_street_name,
                'contact_name' => $data['contact_name'] ?? $salon->contact_name,
                'contact_number' => $data['contact_number'] ?? $salon->contact_number,
                'contact_email' => $data['contact_email'] ?? $salon->contact_email,
                'business_contact_name' => $data['business_contact_name'] ?? $salon->business_contact_name,
                'business_contact_email' => $data['business_contact_email'] ?? $salon->business_contact_email,
                'business_contact_number' => $data['business_contact_number'] ?? $salon->business_contact_number,

                'icon' => $data['icon'] ?? $salon->icon,
                'description' => $data['description'] ?? $salon->description,
                'latitude' => $data['latitude'] ?? $salon->latitude,
                'longitude' => $data['longitude'] ?? $salon->longitude,
                'types' => !isset($data['types']) ? $salon->types : implode(',', $data['types']),
                'bio' => $data['bio'] ?? $salon->bio,

                'is_active' => $data['is_active'] ?? $salon->is_active,
                'is_approved' => $data['is_approved'] ?? $salon->is_approved,
                'block_message' => $data['block_message'] ?? $salon->block_message,
                'tags' => $data['tags'] ?? $salon->tags,
                'loyalty_service_id' => $data['loyalty_service_id'] ?? $salon->loyalty_service_id,

                'vat_number' => $data['vat_number'] ?? $salon->vat_number,
                'service_location' => $data['service_location'] ?? $salon->service_location,
                'bank_name' => $data['bank_name'] ?? $salon->bank_name,
                'bank_account_number' => $data['bank_account_number'] ?? $salon->bank_account_number,
                'bank_account_holder_name' => $data['bank_account_holder_name'] ?? $salon->bank_account_holder_name,
                'bank_account_iban' => $data['bank_account_iban'] ?? $salon->bank_account_iban,
                'services_list' => $data['services_list'] ?? $salon->services_list,

                'trade_license' => $data['trade_license'] ?? $salon->trade_license,
                'vat_certificate' => $data['vat_certificate'] ?? $salon->vat_certificate,
                'bank_account_certificate' => $data['bank_account_certificate'] ?? $salon->bank_account_certificate,


                // old data
                'name' => '',
                'phone_code' => '',
                'phone' => '',
                'email' => null,
                'location' => '',
                'type' => 'salon',
                'country' => '',
                'city' => '',
            ]
        );

        $salon->load(['socialMediaSites', 'images', 'workingHours', 'owner', 'latestReviews', 'loyaltyService']);

        return $salon;
    }

    public function destroy($salon)
    {
        return $salon->delete();
    }
}
