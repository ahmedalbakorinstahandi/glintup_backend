<?php

namespace App\Http\Services\Salons;

use App\Models\Salons\WorkingHour;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\Salons\WorkingHourPermission;

class WorkingHourService
{
    public function index($data)
    {
        $query = WorkingHour::query()->with('salon');

        $query = WorkingHourPermission::filterIndex($query);

        return FilterService::applyFilters($query, $data, [], [], ['created_at'], ['salon_id', 'day_of_week', 'is_closed'], ['id']);
    }

    public function show($id)
    {
        $item = WorkingHour::with('salon')->find($id);

        if (!$item) {
            MessageService::abort(404, 'messages.working_hour.item_not_found');
        }

        return $item;
    }

    public function create($validated)
    {

        $dayOfWeek = $validated['day_of_week'];

        $existingWorkingHour = WorkingHour::where('day_of_week', $dayOfWeek)->where('salon_id', $validated['salon_id'])->first();

        if ($existingWorkingHour) {
            MessageService::abort(422, 'messages.working_hour.this_day_of_week_already_exists');
        }


        // if is closed is existing and true, then set opening_time, closing_time, break_start and break_end to null
        if (isset($validated['is_closed']) && $validated['is_closed']) {
            $validated['opening_time'] = null;
            $validated['closing_time'] = null;
            $validated['break_start'] = null;
            $validated['break_end'] = null;
        }

        return WorkingHour::create($validated);
    }

    public function update($item, $validated)
    {

        if (isset($validated['is_closed']) && $validated['is_closed']) {
            $validated['opening_time'] = null;
            $validated['closing_time'] = null;
            $validated['break_start'] = null;
            $validated['break_end'] = null;
        }

        $item->update($validated);

        return $item;
    }

    public function destroy($item)
    {
        return $item->delete();
    }
}
