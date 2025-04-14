<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\SalonHoliday;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Salons\SalonHolidayPermission;

class SalonHolidayService
{
    public function index($data)
    {
        $query = SalonHoliday::query()->with('salon');

        $query = SalonHolidayPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['reason'],
            [],
            ['holiday_date'],
            ['salon_id', 'is_full_day'],
            ['id']
        );
    }

    public function show($id)
    {
        $holiday = SalonHoliday::with('salon')->find($id);

        if (!$holiday) {
            MessageService::abort(404, 'messages.salon_holiday.item_not_found');
        }

        return $holiday;
    }

    public function create($data)
    {
        return SalonHoliday::create($data);
    }

    public function update($holiday, $data)
    {
        $holiday->update($data);
        return $holiday;
    }

    public function destroy($holiday)
    {
        return $holiday->delete();
    }
}
