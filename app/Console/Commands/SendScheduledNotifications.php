<?php

namespace App\Console\Commands;

use App\Http\Notifications\NotificationHelper;
use App\Http\Services\General\NotificationService;
use App\Models\Booking\BookingService;
use App\Services\FirebaseService;
use App\Services\LanguageService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-due';

    protected $description = 'Send reminders and live notifications for booking services';

    public function handle()
    {
        Log::info('Sending scheduled notifications');

        $now = Carbon::now();
        $nowFormatted = $now->format('Y-m-d H:i');

        // حالات التذكير قبل ساعتين
        $this->sendReminder($now->copy()->addHours(2)->format('Y-m-d H:i'), '2h');

        // حالات التذكير قبل 24 ساعة لخبيرات التجميل فقط
        $this->sendReminder($now->copy()->addHours(24)->format('Y-m-d H:i'), '24h');

        // إشعار وقت الخدمة الحالي
        $this->sendLiveNotification($nowFormatted);

        $this->info("✅ فحص الإشعارات اكتمل بنجاح.");
    }

    private function sendReminder(string $targetTime, string $type)
    {
        $query = BookingService::whereNotNull('start_date_time')
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('booking', function ($q) {
                $q->where('status', 'confirmed');
            })
            ->whereRaw("DATE_FORMAT(start_date_time, '%Y-%m-%d %H:%i') = ?", [$targetTime])
            ->with(['booking.user', 'service', 'booking.salon']);

        if ($type === '24h') {
            $$query->whereHas('booking.salon', function ($q) {
                $q->where('type', 'beautician');
            });
        }

        $services = $query->get();

        $locale = LanguageService::getLocale();

        foreach ($services as $service) {
            $booking = $service->booking;
            if (!$booking || !$booking->user || !$booking->salon) continue;

            $user = $booking->user;
            $salonName = $booking->salon->name;

            if ($type === '24h') {
                // $this->sendUserNotification($user, "تبقّى يوم على موعدك في {$salonName}!");
                $title = 'notifications.user.booking.reminder.title';
                $body = 'notifications.user.booking.reminder.body';
                $replace = [
                    'salon_name' => $salonName,
                    'time' => '24h',
                    'booking_id' => $booking->id,
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'service_name' => $service->service->name[$locale],
                    'locales' => [
                        'service_name' => NotificationHelper::handleLocales($service->service->name, 'service_name'),
                    ],
                ];

                $this->sendUserNotification($booking, $title, $body, $replace);
            } else {
                $title = 'notifications.user.booking.reminder.title';
                $body = 'notifications.user.booking.reminder.body';
                $replace = [
                    'salon_name' => $salonName,
                    'time' => '2h',
                    'booking_id' => $booking->id,
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'service_name' => $service->service->name[$locale],
                    'locales' => [
                        'service_name' => NotificationHelper::handleLocales($service->service->name, 'service_name'),
                    ],
                ];

                $this->sendUserNotification($booking, $title, $body, $replace);
            }
        }
    }

    private function sendLiveNotification(string $nowFormatted)
    {

        $locale = LanguageService::getLocale();
        $services = BookingService::whereNotNull('start_date_time')
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('booking', function ($q) {
                $q->where('status', 'confirmed');
            })
            ->whereRaw("DATE_FORMAT(start_date_time, '%Y-%m-%d %H:%i') = ?", [$nowFormatted])
            ->with(['booking.user', 'service', 'booking.salon'])
            ->get();

        foreach ($services as $service) {
            $booking = $service->booking;
            if (!$booking || !$booking->user || !$booking->salon) continue;

            $user = $booking->user;
            $salonName = $booking->salon->name;

            $title = 'notifications.user.booking.live.title';
            $body = 'notifications.user.booking.live.body';

            $replace = [
                'salon_name' => $salonName,
                'time' => 'live',
                'booking_id' => $booking->id,
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'service_name' => $service->service->name[$locale],
                'locales' => [
                    'service_name' => NotificationHelper::handleLocales($service->service->name, 'service_name'),
                ],
            ];

            $this->sendUserNotification($booking, $title, $body, $replace);
        }
    }

    private function sendUserNotification($booking, string $title, string $body, $replace = [])
    {


        FirebaseService::sendToTokensAndStorage(
            [$booking->user->id],
            [
                'id' => $booking->user->id,
                'type' => 'Booking',
            ],
            $title,
            $body,
            $replace,
            $replace,
        );
    }
}
