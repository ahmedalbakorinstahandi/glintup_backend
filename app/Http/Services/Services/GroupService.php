<?php

namespace App\Http\Services\Services;

use App\Models\Services\Group;
use App\Http\Permissions\Services\GroupPermission;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;

class GroupService
{
    public function index($data)
    {
        $query = Group::query()->with(['salon', 'groupServices.service']);

        $searchFields = ['name'];
        $numericFields = [];
        $dateFields = ['created_at'];
        $exactMatchFields = ['salon_id'];
        $inFields = ['id'];

        $query = GroupPermission::filterIndex($query);

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
        $group = Group::find($id);
        if (!$group) {
            MessageService::abort(404, 'messages.group.item_not_found');
        }

        $group->load(['salon', 'groupServices.service']);

        return $group;
    }

    public function create($validatedData)
    {
        $validatedData = LanguageService::prepareTranslatableData($validatedData, new Group);




        $group = Group::create($validatedData);

        $group->load(['salon', 'groupServices.service']);

        return $group;
    }

    public function update($group, $validatedData)
    {
        $validatedData = LanguageService::prepareTranslatableData($validatedData, $group);
        $group->update($validatedData);

        $group->load(['salon', 'groupServices.service']);

        return $group;
    }

    public function destroy($group)
    {
        return $group->delete();
    }
}
