<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Booking\Booking;
use App\Models\Salons\Salon;
use App\Models\Statistics\PromotionAd;
use App\Models\Users\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // statistics dashboard end point
    public function index()
    {
        // daily,weekly,monthly,yearly
        $date = request('date', 'daily');

        $salons_count = 0;
        $users_count = 0;
        $all_bookings_count = 0;
        $pending_booking_count = 0;
        $confirmed_booking_count = 0;
        $completed_booking_count = 0;
        $canceled_booking_count = 0;
        // TODO: get the total revenue 
        $total_revenue = 5000;
        // TODO: get the total complaints
        $new_complaints_count = 4;
        //إعلانات للمراجعة
        $ads_count = 0;






        switch ($date) {
            case 'daily':
                $salons_count = Salon::whereDate('created_at', now()->toDateString())->count();
                $users_count = User::whereDate('created_at', now()->toDateString())->where('role', 'customer')->count();
                $all_bookings_count = Booking::whereDate('date', now()->toDateString())->count();
                $pending_booking_count = Booking::whereDate('date', now()->toDateString())->where('status', 'pending')->count();
                $confirmed_booking_count = Booking::whereDate('date', now()->toDateString())->where('status', 'confirmed')->count();
                $completed_booking_count = Booking::whereDate('date', now()->toDateString())->where('status', 'completed')->count();
                $canceled_booking_count = Booking::whereDate('date', now()->toDateString())->where('status', 'canceled')->count();
                $ads_count = PromotionAd::whereDate('created_at', now()->toDateString())->count();
                break;
            case 'weekly':
                $salons_count = Salon::where('created_at', '>=', now()->subWeek())->count();
                $users_count = User::where('created_at', '>=', now()->subWeek())->where('role', 'customer')->count();
                $all_bookings_count = Booking::where('date', '>=', now()->subWeek())->count();
                $pending_booking_count = Booking::where('date', '>=', now()->subWeek())->where('status', 'pending')->count();
                $confirmed_booking_count = Booking::where('date', '>=', now()->subWeek())->where('status', 'confirmed')->count();
                $completed_booking_count = Booking::where('date', '>=', now()->subWeek())->where('status', 'completed')->count();
                $canceled_booking_count = Booking::where('date', '>=', now()->subWeek())->where('status', 'canceled')->count();
                $ads_count = PromotionAd::where('created_at', '>=', now()->subWeek())->count();
                break;
            case 'monthly':
                $salons_count = Salon::where('created_at', '>=', now()->subMonth())->count();
                $users_count = User::where('created_at', '>=', now()->subMonth())->where('role', 'customer')->count();
                $all_bookings_count = Booking::where('date', '>=', now()->subMonth())->count();
                $pending_booking_count = Booking::where('date', '>=', now()->subMonth())->where('status', 'pending')->count();
                $confirmed_booking_count = Booking::where('date', '>=', now()->subMonth())->where('status', 'confirmed')->count();
                $completed_booking_count = Booking::where('date', '>=', now()->subMonth())->where('status', 'completed')->count();
                $canceled_booking_count = Booking::where('date', '>=', now()->subMonth())->where('status', 'canceled')->count();
                $ads_count = PromotionAd::where('created_at', '>=', now()->subMonth())->count();
                break;
            case 'yearly':
                $salons_count = Salon::where('created_at', '>=', now()->subYear())->count();
                $users_count = User::where('created_at', '>=', now()->subYear())->where('role', 'customer')->count();
                $all_bookings_count = Booking::where('date', '>=', now()->subYear())->count();
                $pending_booking_count = Booking::where('date', '>=', now()->subYear())->where('status', 'pending')->count();
                $confirmed_booking_count = Booking::where('date', '>=', now()->subYear())->where('status', 'confirmed')->count();
                $completed_booking_count = Booking::where('date', '>=', now()->subYear())->where('status', 'completed')->count();
                $canceled_booking_count = Booking::where('date', '>=', now()->subYear())->where('status', 'canceled')->count();
                $ads_count = PromotionAd::where('created_at', '>=', now()->subYear())->count();
                break;
            default:
                $salons_count = Salon::where('created_at', '>=', now()->subDay())->count();
                $users_count = User::where('created_at', '>=', now()->subDay())->where('role', 'customer')->count();
                $all_bookings_count = Booking::where('date', '>=', now()->subDay())->count();
                $pending_booking_count = Booking::where('date', '>=', now()->subDay())->where('status', 'pending')->count();
                $confirmed_booking_count = Booking::where('date', '>=', now()->subDay())->where('status', 'confirmed')->count();
                $completed_booking_count = Booking::where('date', '>=', now()->subDay())->where('status', 'completed')->count();
                $canceled_booking_count = Booking::where('date', '>=', now()->subDay())->where('status', 'canceled')->count();
                $ads_count = PromotionAd::where('created_at', '>=', now()->subDay())->count();
                break;
        }


        // fast tasks
        // task 1
        $ads_review_count = PromotionAd::whereDate('created_at', now()->toDateString())->where('status', 'in_review')->count();
        $total_ads_today = PromotionAd::whereDate('created_at', now()->toDateString())->count();
        $review_percentage = $total_ads_today > 0 ? ($ads_review_count / $total_ads_today) * 100 : 0;

        // TODO task 2 : complaints 
        $complaints_review_count = 0;
        $total_complaints_today = 0;
        $complaints_review_percentage = $total_complaints_today > 0 ? ($complaints_review_count / $total_complaints_today) * 100 : 0;

        //   task 3 : تفعيل صالونات 
        $salons_review_count = Salon::whereDate('created_at', now()->toDateString())->where('is_approved', false)->count();
        $total_salons_today = Salon::whereDate('created_at', now()->toDateString())->count();
        $salons_review_percentage = $total_salons_today > 0 ? ($salons_review_count / $total_salons_today) * 100 : 0;


        // الحجوزات لآخر 7 أيام
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->toDateString());
        }

        $weekly_appointments = $dates->map(function ($date) {
            $dayName = \Carbon\Carbon::parse($date)->locale('ar')->dayName;
            $total = Booking::whereDate('date', $date)->count();
            return [
                'name' => $dayName,
                'total' => $total,
            ];
        });


        // last 5 bookings // load relationships: salon, user
        $last_bookings = Booking::orderBy('created_at', 'desc')->take(5)->with(['salon', 'user'])->get();

        // last 5 salons
        $bset_salons = Salon::orderBy('created_at', 'desc')->take(5)->get();


        // Monthly salons revenue
        $monthlySalonsRevenue = [
            [
                'name' => 'يناير',
                'total' => 2400,
            ],
            [
                'name' => 'فبراير',
                'total' => 1398,
            ],
            [
                'name' => 'مارس',
                'total' => 9800,
            ],
            [
                'name' => 'أبريل',
                'total' => 3908,
            ],
            [
                'name' => 'مايو',
                'total' => 4800,
            ],
            [
                'name' => 'يونيو',
                'total' => 3800,
            ],
            [
                'name' => 'يوليو',
                'total' => 4300,
            ],
        ];


        return response()->json([
            'success' => true,
            'data' => [
                'salons_registered_count' => $salons_count,
                'users_registered_count' => $users_count,
                'bookings' => [
                    'all_bookings_count' => $all_bookings_count,
                    'pending_booking_count' => $pending_booking_count,
                    'confirmed_booking_count' => $confirmed_booking_count,
                    'completed_booking_count' => $completed_booking_count,
                    'canceled_booking_count' => $canceled_booking_count,
                ],
                'total_revenue' => $total_revenue,
                'new_complaints_count' => $new_complaints_count,
                'ads_count' => $ads_count,
                'fast_tasks' => [
                    'ads' => [
                        'ads_review_count' => $ads_review_count,
                        'total_ads_today' => $total_ads_today,
                        'review_percentage' => $review_percentage,
                    ],
                    'complaints' => [
                        'complaints_review_count' => $complaints_review_count,
                        'total_complaints_today' => $total_complaints_today,
                        'review_percentage' => $complaints_review_percentage,
                    ],
                    'salons' => [
                        'salons_review_count' => $salons_review_count,
                        'total_salons_today' => $total_salons_today,
                        'review_percentage' => $salons_review_percentage,
                    ],
                ],
                'weekly_appointments' => $weekly_appointments,
                'monthly_salons_revenue' => $monthlySalonsRevenue,
                'last_bookings' => $last_bookings,
                'bset_salons' => $bset_salons,
            ],
        ]);
    }


    // get statistics for salon 
    public function salonStatistics()
    {
        $user = User::auth();

        $salonId = $user->salon->id;

        // Earnings
        $earnings = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->sum('total_price');

        // Appointments Count
        $appointmentsCount = Booking::where('salon_id', $salonId)->count();

        // Reviews Count
        $reviewsCount = Booking::where('salon_id', $salonId)
            ->whereNotNull('review')
            ->count();

        // New Clients Count
        $newClientsCount = User::whereHas('bookings', function ($query) use ($salonId) {
            $query->where('salon_id', $salonId);
        })->where('created_at', '>=', now()->subMonth())->count();

        // Invoices not paid
        $unpaidInvoicesCount = Booking::where('salon_id', $salonId)
            ->where('status', 'pending')
            ->count();

        // Revenue Overview
        $monthlyData = [
            ['name' => 'Jan', 'income' => 4000, 'expenses' => 2400],
            ['name' => 'Feb', 'income' => 5000, 'expenses' => 3000],
            ['name' => 'Mar', 'income' => 6000, 'expenses' => 3200],
            ['name' => 'Apr', 'income' => 5500, 'expenses' => 3500],
            ['name' => 'May', 'income' => 7000, 'expenses' => 4000],
            ['name' => 'Jun', 'income' => 6500, 'expenses' => 3800],
            ['name' => 'Jul', 'income' => 7500, 'expenses' => 4200],
            ['name' => 'Aug', 'income' => 8000, 'expenses' => 4800],
            ['name' => 'Sep', 'income' => 7000, 'expenses' => 4300],
            ['name' => 'Oct', 'income' => 7500, 'expenses' => 4500],
            ['name' => 'Nov', 'income' => 8000, 'expenses' => 5000],
            ['name' => 'Dec', 'income' => 8500, 'expenses' => 5200],
        ];

        // Appointments Completed Count with percentage
        $completedAppointmentsCount = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->count();
        $completedPercentage = $appointmentsCount > 0
            ? ($completedAppointmentsCount / $appointmentsCount) * 100
            : 0;

        // Appointments Cancelled Count with percentage
        $cancelledAppointmentsCount = Booking::where('salon_id', $salonId)
            ->where('status', 'canceled')
            ->count();
        $cancelledPercentage = $appointmentsCount > 0
            ? ($cancelledAppointmentsCount / $appointmentsCount) * 100
            : 0;

        // Recent Activity: Appointments Today
        $appointmentsToday = Booking::where('salon_id', $salonId)
            ->whereDate('date', now()->toDateString())
            ->get();

        // Last 7 Reviews
        $last7Reviews = Booking::where('salon_id', $salonId)
            ->whereNotNull('review')
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        // Ads Active Count
        $adsActiveCount = PromotionAd::where('salon_id', $salonId)
            ->where('status', 'active')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'earnings' => $earnings,
                'appointments_count' => $appointmentsCount,
                'reviews_count' => $reviewsCount,
                'new_clients_count' => $newClientsCount,
                'unpaid_invoices_count' => $unpaidInvoicesCount,
                'revenue_overview' => $monthlyData,
                'appointments' => [
                    'completed_count' => $completedAppointmentsCount,
                    'completed_percentage' => $completedPercentage,
                    'cancelled_count' => $cancelledAppointmentsCount,
                    'cancelled_percentage' => $cancelledPercentage,
                    'total' => $appointmentsCount,
                ],
                'recent_activity' => [
                    'appointments_today' => $appointmentsToday,
                ],
                'last_7_reviews' => $last7Reviews,
                'ads_active_count' => $adsActiveCount,
            ],
        ]);
    }
}
