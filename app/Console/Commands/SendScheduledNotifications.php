<?php

namespace App\Console\Commands;

use App\Models\Booking\BookingService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\NotificationService;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-due';

    protected $description = 'Send notifications when a booking service time is due';

    public function handle()
    {

        // $table->enum('type', ["salon", "home_service", "beautician", "clinic"]);


        $now = Carbon::now()->format('Y-m-d H:i');

        $services = BookingService::whereNotNull('start_date_time')
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('booking', function ($q) {
                $q->where('status', 'confirmed');
            })
            ->whereRaw("DATE_FORMAT(start_date_time, '%Y-%m-%d %H:%i') = ?", [$now])
            ->with(['booking.user', 'service', 'booking.salon'])
            ->get();

        foreach ($services as $service) {
            $booking = $service->booking;

            if (!$booking || !$booking->user || !$booking->salon) continue;

            $user = $booking->user;
            $salonName = $booking->salon->name;

            // NotificationService::send($user, [
            //     'ar' => "✨ مرحبًا {$user->name}!\nحان الآن موعدك في {$salonName}، نتمنى لك وقتًا رائعًا 🕒",
            //     'en' => "✨ Hi {$user->name}!\nYour appointment at {$salonName} is now! Enjoy your time 🕒",
            // ]);
        }

        $this->info("تم فحص إشعارات الوقت الحالي بنجاح.");
    }
}
