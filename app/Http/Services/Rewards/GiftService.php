<?php

namespace App\Http\Services\Rewards;

use App\Models\Rewards\Gift;
use App\Services\FilterService;
use App\Services\LanguageService;
use App\Services\MessageService;
use App\Services\OrderHelper;

class GiftService
{
    public function index($data)
    {
        $query = Gift::query();

        $query = FilterService::applyFilters(
            $query,
            $data,
            ['name'],
            [],
            ['created_at'],
            ['is_active'],
            ['id'],
        );


        return $query;
    }

    public function show($id)
    {
        $gift = Gift::where('id', $id)->first();

        if (!$gift) {
            MessageService::abort(4040, 'Gift not found');
        }

        return $gift;
    }

    public function create($data)
    {

        $data['order'] = 0;

        $gift = Gift::create(LanguageService::prepareTranslatableData($data, new Gift));


        OrderHelper::assign($gift, 'order');

        return $gift;
    }

    public function update($gift, $data)
    {
        $gift->update(LanguageService::prepareTranslatableData($data, $gift));

        return $gift;
    }

    public function destroy($gift)
    {
        $gift->delete();

        return $gift;
    }
}
