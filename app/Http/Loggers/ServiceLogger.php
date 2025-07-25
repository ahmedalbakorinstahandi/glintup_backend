<?php

namespace App\Http\Loggers;

use App\Models\Services\Service;
use App\Models\Users\User;
use App\Services\ActivityLogHelper;
use Illuminate\Support\Facades\Log;

class ServiceLogger
{
    public static function logChanges(Service $old, Service $new)
    {

        $user = User::auth();



        $changesAr = [];
        $changesEn = [];

        if ($old->price != $new->price) {
            $changesAr[] = "السعر تغير من {$old->price} إلى {$new->price}";
            $changesEn[] = "Price changed from {$old->price} to {$new->price}";
        }

        if ($old->discount_percentage != $new->discount_percentage) {
            $changesAr[] = "الخصم تغير من {$old->discount_percentage}% إلى {$new->discount_percentage}%";
            $changesEn[] = "Discount changed from {$old->discount_percentage}% to {$new->discount_percentage}%";
        }

        if ($old->is_active != $new->is_active) {
            $fromAr = $old->is_active ? 'مفعل' : 'غير مفعل';
            $toAr   = $new->is_active ? 'مفعل' : 'غير مفعل';
            $changesAr[] = "الحالة تغيرت من {$fromAr} إلى {$toAr}";
            $changesEn[] = "Status changed from " . ($old->is_active ? 'active' : 'inactive') . " to " . ($new->is_active ? 'active' : 'inactive');
        }

        if ($old->capacity != $new->capacity) {
            $changesAr[] = "الطاقة الاستيعابية تغيرت من {$old->capacity} إلى {$new->capacity}";
            $changesEn[] = "Capacity changed from {$old->capacity} to {$new->capacity}";
        }


        if (!empty($changesAr)) {
            $description = [
                'ar' => "تم تعديل الخدمة: {$old->name['ar']}\n- " . implode("\n- ", $changesAr),
                'en' => "Service updated: {$old->name['en']}\n- " . implode("\n- ", $changesEn),
            ];


            ActivityLogHelper::createActivityLog(
                $user->id,
                'service.updated',
                $description,
                'Service',
                $new->id,
                'salon-' . $new->salon_id,
            );
        }
    }
}
