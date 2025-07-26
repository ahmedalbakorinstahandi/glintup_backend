<?php

namespace App\Http\Loggers;

use App\Models\Salons\Salon;
use App\Models\Users\User;
use App\Services\ActivityLogHelper;

class SalonLogger
{
    public static function logLoyaltyChanges(Salon $salon)
    {
        $user = User::auth();

        $changesAr = [];
        $changesEn = [];

        // تحقق من تغيير loyalty_service_id
        if ($salon->wasChanged('loyalty_service_id')) {
            $old = $salon->getOriginal('loyalty_service_id');
            $new = $salon->loyalty_service_id;

            $changesAr[] = "تم تعديل خدمة الولاء (كارت الولاء) من " . ($old ?? 'لا يوجد') . " إلى " . ($new ?? 'لا يوجد');
            $changesEn[] = "Loyalty service (loyal card) changed from " . ($old ?? 'none') . " to " . ($new ?? 'none');
        }

        // إذا كان عندك free service حقل منفصل ضيفه بنفس الطريقة:
        // مثال: free_service_id
        if ($salon->wasChanged('free_service_id')) {
            $old = $salon->getOriginal('free_service_id');
            $new = $salon->free_service_id;

            $changesAr[] = "تم تعديل خدمة مجانية من " . ($old ?? 'لا يوجد') . " إلى " . ($new ?? 'لا يوجد');
            $changesEn[] = "Free service changed from " . ($old ?? 'none') . " to " . ($new ?? 'none');
        }

        if (!empty($changesAr)) {
            $description = [
                'ar' => "تم تعديل خصائص الولاء للصالون {$salon->name}:\n- " . implode("\n- ", $changesAr),
                'en' => "Loyalty features updated for salon {$salon->name}:\n- " . implode("\n- ", $changesEn),
            ];

            ActivityLogHelper::createActivityLog(
                $user->id,
                'salon.loyalty_updated',
                $description,
                Salon::class,
                $salon->id,
                'salon-' . $salon->id
            );
        }
    }
}

