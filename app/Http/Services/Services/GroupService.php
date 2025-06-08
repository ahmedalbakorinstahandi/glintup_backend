<?php

namespace App\Http\Services\Services;

use App\Models\Services\Group;
use App\Http\Permissions\Services\GroupPermission;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;
use App\Services\OrderHelper;

class GroupService
{
    public function index($data)
    {
        $query = Group::query()->with(['salon', 'groupServices.service']);

        $searchFields = ['name'];
        $numericFields = [];
        $dateFields = ['created_at'];
        $exactMatchFields = [];
        $inFields = ['id'];

        $query = GroupPermission::filterIndex($query);

        if (isset($data['salon_id'])) {
            $query->where('salon_id', $data['salon_id'])->orWhereNull('salon_id');
        } else {
            $query->orWhereNull('salon_id');
        }

        $data['sort_field'] = 'orders';
        $data['sort_order'] = 'asc';

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
        $group = Group::where('id', $id)->first();

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

    public function reorder($group, $validatedData)
    {
        OrderHelper::reorder($group, $validatedData['order'], 'orders');

        return $group;
    }
}
