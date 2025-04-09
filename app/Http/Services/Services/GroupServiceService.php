<?php

namespace App\Http\Services\Services;

use App\Models\Services\GroupService;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Services\GroupServicePermission;

class GroupServiceService
{
    public function index($data)
    {
        $query = GroupService::query()->with(['group', 'service', 'salon']);

        $searchFields = [];
        $numericFields = ['order'];
        $dateFields = ['created_at'];
        $exactMatchFields = ['group_id', 'salon_id', 'service_id'];
        $inFields = ['id'];

        $query = GroupServicePermission::filterIndex($query);

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
        $groupService = GroupService::find($id);
        if (!$groupService) {
            MessageService::abort(404, 'messages.group_service.item_not_found');
        }
        return $groupService;
    }

    public function create($data)
    {

        $data['order'] = rand(1, 1000);

        $groupService = GroupService::create($data);

        $groupService->update(['order' => $groupService->id]);

        $groupService->load(['group', 'service', 'salon']);

        return $groupService;
    }

    public function update($groupService, $data)
    {
        $groupService->update($data);

        $groupService->load(['group', 'service', 'salon']);

        return $groupService;
    }

    public function destroy($groupService)
    {
        return $groupService->delete();
    }
}
