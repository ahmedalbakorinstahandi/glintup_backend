<?php

namespace App\Http\Loggers;

use App\Models\Statistics\PromotionAd;
use App\Models\Users\User;
use App\Services\ActivityLogHelper;

class PromotionAdLogger
{
    public static function logChanges(PromotionAd $old, PromotionAd $new)
    {
        $user = User::auth();

        $changesAr = [];
        $changesEn = [];

        // title
        if ($old->title['ar'] != $new->title['ar']) {
            $changesAr[] = "العنوان (عربي) تغير من \"{$old->title['ar']}\" إلى \"{$new->title['ar']}\"";
            $changesEn[] = "Title (AR) changed from \"{$old->title['ar']}\" to \"{$new->title['ar']}\"";
        }
        if ($old->title['en'] != $new->title['en']) {
            $changesAr[] = "العنوان (إنجليزي) تغير من \"{$old->title['en']}\" إلى \"{$new->title['en']}\"";
            $changesEn[] = "Title (EN) changed from \"{$old->title['en']}\" to \"{$new->title['en']}\"";
        }

        // button_text
        if ($old->button_text['ar'] != $new->button_text['ar']) {
            $changesAr[] = "نص الزر (عربي) تغير من \"{$old->button_text['ar']}\" إلى \"{$new->button_text['ar']}\"";
            $changesEn[] = "Button text (AR) changed from \"{$old->button_text['ar']}\" to \"{$new->button_text['ar']}\"";
        }
        if ($old->button_text['en'] != $new->button_text['en']) {
            $changesAr[] = "نص الزر (إنجليزي) تغير من \"{$old->button_text['en']}\" إلى \"{$new->button_text['en']}\"";
            $changesEn[] = "Button text (EN) changed from \"{$old->button_text['en']}\" to \"{$new->button_text['en']}\"";
        }

        // validity
        if ($old->valid_from != $new->valid_from) {
            $changesAr[] = "تاريخ البداية تغير من {$old->valid_from} إلى {$new->valid_from}";
            $changesEn[] = "Start date changed from {$old->valid_from} to {$new->valid_from}";
        }

        if ($old->valid_to != $new->valid_to) {
            $changesAr[] = "تاريخ النهاية تغير من {$old->valid_to} إلى {$new->valid_to}";
            $changesEn[] = "End date changed from {$old->valid_to} to {$new->valid_to}";
        }

        // is_active
        if ($old->is_active != $new->is_active) {
            $fromAr = $old->is_active ? 'مفعل' : 'غير مفعل';
            $toAr   = $new->is_active ? 'مفعل' : 'غير مفعل';
            $changesAr[] = "الحالة تغيرت من {$fromAr} إلى {$toAr}";

            $fromEn = $old->is_active ? 'active' : 'inactive';
            $toEn   = $new->is_active ? 'active' : 'inactive';
            $changesEn[] = "Status changed from {$fromEn} to {$toEn}";
        }

        // status enum
        if ($old->status != $new->status) {
            $changesAr[] = "الحالة الترويجية تغيرت من {$old->status} إلى {$new->status}";
            $changesEn[] = "Promotion status changed from {$old->status} to {$new->status}";
        }

        if (!empty($changesAr)) {
            $description = [
                'ar' => "تم تعديل الإعلان الترويجي: {$old->title['ar']}\n- " . implode("\n- ", $changesAr),
                'en' => "Promotion Ad updated: {$old->title['en']}\n- " . implode("\n- ", $changesEn),
            ];

            ActivityLogHelper::createActivityLog(
                $user->id,
                'promotion_ad.updated',
                $description,
                'PromotionAd',
                $new->id,
                'salon-' . $new->salon_id,
            );
        }
    }

    public static function logCreation(PromotionAd $ad)
    {
        $user = User::auth();

        $description = [
            'ar' => "تم إنشاء إعلان ترويجي جديد بعنوان: {$ad->title['ar']} من {$ad->valid_from} إلى {$ad->valid_to}",
            'en' => "New promotion ad created titled: {$ad->title['en']} from {$ad->valid_from} to {$ad->valid_to}",
        ];

        ActivityLogHelper::createActivityLog(
            $user->id,
            'promotion_ad.created',
            $description,
            'PromotionAd',
            $ad->id,
            'salon-' . $ad->salon_id
        );
    }
}
