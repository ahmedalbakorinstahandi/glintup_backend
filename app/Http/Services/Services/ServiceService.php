<?php

namespace App\Services\Services;

use App\Models\Services\Service;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;

class ServiceService
{
    public function index($data)
    {
        $query = Service::query()->with('salon');

        $searchFields = ['name', 'description'];
        $numericFields = [];
        $dateFields = ['created_at'];
        $exactMatchFields = ['salon_id', 'is_active', 'gender', 'type'];
        $inFields = ['id'];


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

        return $service;
    }

    public function create($validatedData)
    {
        $validatedData = LanguageService::prepareTranslatableData($validatedData, new Service);
        $Service = Service::create($validatedData);

        return $Service;
    }

    public function update($service, $validatedData)
    {
        $validatedData = LanguageService::prepareTranslatableData($validatedData, $service);
        $service->update($validatedData);
        return $service;
    }

    public function destroy($service)
    {
        return $service->delete();
    }
}
