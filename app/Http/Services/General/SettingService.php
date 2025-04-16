<?php

namespace App\Http\Services\General;

use App\Http\Permissions\General\SettingPermission;
use App\Models\General\Setting;
use App\Services\FilterService;
use App\Services\MessageService;

class SettingService
{
    public function index($data)
    {
        $query = Setting::query();
        $query = SettingPermission::filterIndex($query);
        return FilterService::applyFilters(
            $query,
            $data,
            ['key', 'value'],
            [],
            ['created_at'],
            ['type', 'is_settings'],
            ['id', 'key'] // in_key[] = ['key1', 'key2']
        );
    }


    // multi data updates
    public function updateSettings($data)
    {
        foreach ($data as $item) {
            $setting = Setting::where('key', $item['key'])->first();
            if (!$setting) {
                MessageService::abort(404, 'messages.setting.item_not_found');
            }
            $setting->update(['value' => $item['value']]);
        }

        return [];
    }

    public function show($id)
    {
        $item = Setting::find($id);
        if (!$item) {
            MessageService::abort(404, 'messages.setting.item_not_found');
        }
        return $item;
    }

    public function create($data)
    {
        return Setting::create($data);
    }

    public function update($item, $data)
    {
        $item->update($data);
        return $item;
    }

    public function destroy($item)
    {
        return $item->delete();
    }
}
