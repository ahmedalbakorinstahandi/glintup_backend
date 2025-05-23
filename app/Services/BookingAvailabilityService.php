<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Services\Service;
use App\Models\Booking\BookingService;

class BookingAvailabilityService
{
    public function getAvailableSlots(Carbon $date, Service $service): array
    {
        $dayOfWeek = strtolower($date->format('l'));
        $salon = $service->salon;

        $workingHours = $salon->workingHours()->where('day_of_week', $dayOfWeek)->first();
        if (!$workingHours || $workingHours->is_closed) return [];

        $open = Carbon::parse($workingHours->opening_time);
        $close = Carbon::parse($workingHours->closing_time);
        $breakStart = $workingHours->break_start ? Carbon::parse($workingHours->break_start) : null;
        $breakEnd = $workingHours->break_end ? Carbon::parse($workingHours->break_end) : null;

        $slotSize = $this->getSlotSize($service->duration_minutes);
        $slots = [];

        // جلب العطلة الجزئية أو الكاملة إن وجدت
        $holiday = $salon->holidays()
            ->whereDate('holiday_date', $date)
            ->first();
        $isFullDayHoliday = $holiday && $holiday->is_full_day;
        $holidayStart = $holiday && $holiday->start_time ? Carbon::parse($holiday->start_time) : null;
        $holidayEnd = $holiday && $holiday->end_time ? Carbon::parse($holiday->end_time) : null;

        $start = $open->copy();
        while ($start->copy()->addMinutes($slotSize)->lte($close)) {
            $end = $start->copy()->addMinutes($slotSize);

            // فحص التعارض مع الاستراحة
            $inBreak = $breakStart && $breakEnd &&
                ($start->between($breakStart, $breakEnd) || $end->between($breakStart, $breakEnd));

            // فحص التعارض مع العطلة
            $inHoliday = $isFullDayHoliday ||
                ($holidayStart && $holidayEnd &&
                    ($start->between($holidayStart, $holidayEnd) || $end->between($holidayStart, $holidayEnd)));

            // فحص الحجوزات الحالية
            $overlapCount = BookingService::where('service_id', $service->id)
                ->whereDate('start_date_time', $date)
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_date_time', '<', $end)
                        ->where('end_date_time', '>', $start);
                })
                ->count();

            $isAvailable = !$inBreak && !$inHoliday && $overlapCount < $service->capacity;

            $slots[] = [
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
                'available' => $isAvailable
            ];

            $start->addMinutes($slotSize);
        }

        return $slots;
    }

    public function getAvailableSlots2(Carbon $date, Service $service): array
    {
        $dayOfWeek = strtolower($date->format('l')); // مثل: monday
        $salon = $service->salon;

        $workingHours = $salon->workingHours()->where('day_of_week', $dayOfWeek)->first();
        if (!$workingHours || $workingHours->is_closed) return [];

        $open = Carbon::parse($workingHours->opening_time);
        $close = Carbon::parse($workingHours->closing_time);
        $breakStart = Carbon::parse($workingHours->break_start);
        $breakEnd = Carbon::parse($workingHours->break_end);

        $slotSize = $this->getSlotSize($service->duration_minutes);
        $slots = [];

        for ($start = $open->copy(); $start->addMinutes($slotSize)->lte($close); $start->subMinutes($slotSize)) {
            $end = $start->copy()->addMinutes($slotSize);

            // تجاهل الفترات التي تقاطع الاستراحة
            if (!($end->lte($breakStart) || $start->gte($breakEnd))) {
                $start->addMinutes($slotSize);
                continue;
            }

            // فحص الحجوزات الحالية
            $overlapCount = BookingService::where('service_id', $service->id)
                ->whereDate('start_date_time', $date)
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_date_time', '<', $end)
                        ->where('end_date_time', '>', $start);
                })
                ->count();

            $slots[] = [
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
                'available' => $overlapCount < $service->capacity
            ];

            $start->addMinutes($slotSize);
        }

        return $slots;
    }



    private function getSlotSize(int $duration): int
    {
        if ($duration <= 60) return 60;
        if ($duration <= 120) return 120;
        return ceil($duration / 60) * 60;
    }


    public function isSlotOptionValid(Carbon $date, string $startTime, string $endTime, Service $service): bool
    {
        $slots = $this->getAvailableSlots($date, $service);

        foreach ($slots as $slot) {
            if ($slot['start'] === $startTime && $slot['end'] == $endTime && $slot['available']) {
                return true;
            }
        }

        MessageService::abort(
            422,
            'messages.booking.slot_not_available',
            [
                'service_name' => $service->name,
                'start_time' => $startTime,
                'date' => $date->format('Y-m-d'),
            ]
        );
    }
}
