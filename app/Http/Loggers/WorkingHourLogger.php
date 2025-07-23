<?php

namespace App\Http\Loggers;

use App\Models\Salons\WorkingHour;
use App\Models\Users\User;
use App\Services\ActivityLogHelper;

class WorkingHourLogger
{
    public static function logChanges(WorkingHour $old, WorkingHour $new)
    {
        $user = User::auth();

        $day = ucfirst($old->day_of_week); // example: Monday
        $daysAr = [
            'sunday'    => 'الأحد',
            'monday'    => 'الاثنين',
            'tuesday'   => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday'  => 'الخميس',
            'friday'    => 'الجمعة',
            'saturday'  => 'السبت',
        ];

        $dayAr = $daysAr[$old->day_of_week] ?? $old->day_of_week;

        $changesAr = [];
        $changesEn = [];

        if ($old->is_closed != $new->is_closed) {
            $fromAr = $old->is_closed ? 'مغلق' : 'مفتوح';
            $toAr = $new->is_closed ? 'مغلق' : 'مفتوح';
            $changesAr[] = "الحالة في {$dayAr} تغيرت من {$fromAr} إلى {$toAr}";

            $fromEn = $old->is_closed ? 'closed' : 'open';
            $toEn = $new->is_closed ? 'closed' : 'open';
            $changesEn[] = "Status on {$day} changed from {$fromEn} to {$toEn}";
        }

        if ($old->opening_time != $new->opening_time) {
            $changesAr[] = "وقت الفتح في {$dayAr} تغير من {$old->opening_time} إلى {$new->opening_time}";
            $changesEn[] = "Opening time on {$day} changed from {$old->opening_time} to {$new->opening_time}";
        }

        if ($old->closing_time != $new->closing_time) {
            $changesAr[] = "وقت الإغلاق في {$dayAr} تغير من {$old->closing_time} إلى {$new->closing_time}";
            $changesEn[] = "Closing time on {$day} changed from {$old->closing_time} to {$new->closing_time}";
        }

        if ($old->break_start != $new->break_start) {
            $changesAr[] = "بداية الاستراحة في {$dayAr} تغيرت من {$old->break_start} إلى {$new->break_start}";
            $changesEn[] = "Break start on {$day} changed from {$old->break_start} to {$new->break_start}";
        }

        if ($old->break_end != $new->break_end) {
            $changesAr[] = "نهاية الاستراحة في {$dayAr} تغيرت من {$old->break_end} إلى {$new->break_end}";
            $changesEn[] = "Break end on {$day} changed from {$old->break_end} to {$new->break_end}";
        }

        if (!empty($changesAr)) {
            $description = [
                'ar' => "تم تعديل أوقات العمل ليوم {$dayAr}:\n- " . implode("\n- ", $changesAr),
                'en' => "Working hours updated for {$day}:\n- " . implode("\n- ", $changesEn),
            ];

            ActivityLogHelper::createActivityLog(
                $user->id,
                'working_hour.updated',
                $description,
                get_class($new),
                $new->id,
                'salon-' . $new->salon_id
            );
        }
    }
}
