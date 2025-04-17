<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\SalonSocialMediaSite;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Salons\SalonSocialMediaSitePermission;

class SalonSocialMediaSiteService
{
    public function index($data)
    {
        $query = SalonSocialMediaSite::query();

        $query = SalonSocialMediaSitePermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['link'],
            [],
            ['created_at'],
            ['salon_id', 'social_media_site_id'],
            ['id']
        );
    }

    public function show($id)
    {
        $item = SalonSocialMediaSite::find($id);

        if (!$item) {
            MessageService::abort(404, 'messages.salon_social_media_site.item_not_found');
        }

        return $item;
    }

    public function create($validated)
    {
        return SalonSocialMediaSite::create($validated);
    }

    public function update($item, $validated)
    {
        $item->update($validated);
        return $item;
    }

    public function destroy($item)
    {
        return $item->delete();
    }
}
