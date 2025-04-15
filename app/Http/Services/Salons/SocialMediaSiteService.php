<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\SocialMediaSite;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;

class SocialMediaSiteService
{
    public function index($data)
    {
        $query = SocialMediaSite::query();

        return FilterService::applyFilters($query, $data, ['name'], [], ['created_at'], [], ['id']);
    }

    public function show($id)
    {
        $item = SocialMediaSite::find($id);

        if (!$item) {
            MessageService::abort(404, 'messages.social_media_site.item_not_found');
        }

        return $item;
    }

    public function create($validated)
    {
        $validated = LanguageService::prepareTranslatableData($validated, new SocialMediaSite);
        return SocialMediaSite::create($validated);
    }

    public function update($item, $validated)
    {
        $validated = LanguageService::prepareTranslatableData($validated, $item);
        $item->update($validated);
        return $item;
    }

    public function destroy($item)
    {
        return $item->delete();
    }
}
