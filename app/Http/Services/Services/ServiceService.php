<?php

namespace App\Http\Services\Services;

use App\Http\Permissions\Services\ServicePermission;
use App\Models\Services\Service;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;

class ServiceService
{
    public function index($data)
    {
        $query = Service::query()->with(['salon', 'groupServices.group']);

        $searchFields = ['name', 'description'];
        $numericFields = [];
        $dateFields = ['created_at'];
        $exactMatchFields = ['salon_id', 'is_active', 'gender', 'type'];
        $inFields = ['id'];// in_id[] = [1,2,3]

        $query = ServicePermission::filterIndex($query);


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
        $service = Service::find($id);

        if (!$service) {
            MessageService::abort(404, 'messages.service.item_not_found');
        }

        $service->load(['salon', 'groupServices.group']);

        return $service;
    }

    public function create($validatedData)
    {
        $validatedData = LanguageService::prepareTranslatableData($validatedData, new Service);

        $validatedData['order'] = rand(1, 1000);

        $service = Service::create($validatedData);

        $service->update(['order' => $service->id]);

        $service->load(['salon', 'groupServices.group']);


        return $service;
    }

    public function update($service, $validatedData)
    {
        $validatedData = LanguageService::prepareTranslatableData($validatedData, $service);
        $service->update($validatedData);

        $service->load(['salon', 'groupServices.group']);

        return $service;
    }

    public function destroy($service)
    {
        return $service->delete();
    }
}
