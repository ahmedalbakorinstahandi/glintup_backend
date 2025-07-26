<?php

namespace App\Http\Services\Services;

use App\Models\Services\Group;
use App\Http\Permissions\Services\GroupPermission;
use App\Models\Rewards\FreeService;
use App\Models\Users\User;
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

        // $user = User::auth();

        // // Don't show service groups with empty group_services array to customers
        // if ($user->isCustomer()) {
        //     $query->whereHas('groupServices', function($q) {
        //         $q->whereNotNull('service_id');
        //     });
        // }





        $data['sort_field'] = 'orders';
        $data['sort_order'] = 'asc';


        // if group key = new  // return group with group_services last service 
        
        

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

        $validatedData['orders'] = 1;

        $group = Group::create($validatedData);

        OrderHelper::assign($group, 'orders');

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
        OrderHelper::reorder($group, $validatedData['orders'], 'orders');

        return $group;
    }
}
