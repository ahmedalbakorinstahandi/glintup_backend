<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Http\Services\Salons\SalonService;
use App\Models\Booking\Booking;
use App\Models\General\Complaint;
use App\Models\Salons\Salon;
use App\Models\Salons\SalonPayment;
use App\Models\Services\Review;
use App\Models\Statistics\PromotionAd;
use App\Models\Users\User;
use App\Services\PermissionHelper;

class DashboardController extends Controller
{
    // statistics dashboard end point
    public function index()
    {
        PermissionHelper::checkAdminPermission('dashboard');

        // daily,weekly,monthly,yearly,custom
        $date = request('date', 'daily');

        if (!in_array($date, ['daily', 'weekly', 'monthly', 'yearly', 'custom'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range',
            ], 400);
        }

        if ($date == 'custom') {
            $from = request('from');
            $to = request('to');

            if (!$from || !$to) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date range',
                ], 400);
            }

            $date = [$from, $to];
        }

        $salons_count = 0;
        $users_count = 0;
        $all_bookings_count = 0;
        $pending_booking_count = 0;
        $confirmed_booking_count = 0;
        $completed_booking_count = 0;
        $canceled_booking_count = 0;

        // Get total revenue from completed bookings
        $completed_bookings = Booking::where('status', 'completed')
            ->when($date === 'daily', function ($query) {
                return $query->whereDate('date', now()->toDateString());
            })
            ->when($date === 'weekly', function ($query) {
                return $query->where('date', '>=', now()->subWeek());
            })
            ->when($date === 'monthly', function ($query) {
                return $query->where('date', '>=', now()->subMonth());
            })
            ->when($date === 'yearly', function ($query) {
                return $query->where('date', '>=', now()->subYear());
            })
            ->when(is_array($date), function ($query) use ($date) {
                return $query->whereBetween('date', $date);
            })
            ->get(); // Changed from total_amount to amount

        $total_revenue = $completed_bookings->sum('total_price');


        // Get total complaints
        $new_complaints_count = Complaint::where('reviewed_by', null)
            ->when($date === 'daily', function ($query) {
                return $query->whereDate('created_at', now()->toDateString());
            })
            ->when($date === 'weekly', function ($query) {
                return $query->where('created_at', '>=', now()->subWeek());
            })
            ->when($date === 'monthly', function ($query) {
                return $query->where('created_at', '>=', now()->subMonth());
            })
            ->when($date === 'yearly', function ($query) {
                return $query->where('created_at', '>=', now()->subYear());
            })
            ->when(is_array($date), function ($query) use ($date) {
                return $query->whereBetween('created_at', $date);
            })
            ->count();


        $ads_count = PromotionAd::where('status', 'approved')
            ->when($date === 'daily', function ($query) {
                return $query->whereDate('created_at', now()->toDateString());
            })
            ->when($date === 'weekly', function ($query) {
                return $query->where('created_at', '>=', now()->subWeek());
            })
            ->when($date === 'monthly', function ($query) {
                return $query->where('created_at', '>=', now()->subMonth());
            })
            ->when($date === 'yearly', function ($query) {
                return $query->where('created_at', '>=', now()->subYear());
            })
            ->when(is_array($date), function ($query) use ($date) {
                return $query->whereBetween('created_at', $date);
            })
            ->count();

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
            case 'custom':
                $salons_count = Salon::where('created_at', '>=', $date[0])->where('created_at', '<=', $date[1])->count();
                $users_count = User::where('created_at', '>=', $date[0])->where('created_at', '<=', $date[1])->where('role', 'customer')->count();
                $all_bookings_count = Booking::where('date', '>=', $date[0])->where('date', '<=', $date[1])->count();
                $pending_booking_count = Booking::where('date', '>=', $date[0])->where('date', '<=', $date[1])->where('status', 'pending')->count();
                $confirmed_booking_count = Booking::where('date', '>=', $date[0])->where('date', '<=', $date[1])->where('status', 'confirmed')->count();
                $completed_booking_count = Booking::where('date', '>=', $date[0])->where('date', '<=', $date[1])->where('status', 'completed')->count();
                $canceled_booking_count = Booking::where('date', '>=', $date[0])->where('date', '<=', $date[1])->where('status', 'canceled')->count();
                $ads_count = PromotionAd::where('created_at', '>=', $date[0])->where('created_at', '<=', $date[1])->count();
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

        // task 2 : complaints 
        $complaints_review_count = Complaint::whereDate('created_at', now()->toDateString())->where('reviewed_by', null)->count();
        $total_complaints_today = Complaint::whereDate('created_at', now()->toDateString())->count();
        $complaints_review_percentage = $total_complaints_today > 0 ? ($complaints_review_count / $total_complaints_today) * 100 : 0;

        //   task 3 : salons 
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
        // $bset_salons = Salon::orderBy('created_at', 'desc')->take(5)->get();

        $salonService = new SalonService();

        $bset_salons = $salonService->index([
            'filter_provider' => 'trending',
            'limit' => 5,
        ])->items();


        // Monthly salons revenue
        $monthlySalonsRevenue = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = SalonPayment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'confirm')
                ->where('is_refund', false)
                ->sum('amount');

            $monthlySalonsRevenue->push([
                'name' => $date->locale('ar')->monthName,
                'total' => $revenue
            ]);
        }


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
        PermissionHelper::checkSalonPermission('dashboard');

        // daily,weekly,monthly,yearly,custom
        $date = request('date', 'daily');

        if (!in_array($date, ['daily', 'weekly', 'monthly', 'yearly', 'custom'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range',
            ], 400);
        }

        if ($date == 'custom') {
            $from = request('from');
            $to = request('to');

            if (!$from || !$to) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date range',
                ], 400);
            }

            $date = [$from, $to];
        }

        $user = User::auth();
        $salonId = $user->salon->id;

        // Build date filter query
        $dateQuery = function ($query) use ($date) {
            if (is_array($date)) {
                $query->whereBetween('created_at', $date);
            } else {
                switch ($date) {
                    case 'daily':
                        $query->whereDate('created_at', today());
                        break;
                    case 'weekly':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'monthly':
                        $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                        break;
                    case 'yearly':
                        $query->whereYear('created_at', now()->year);
                        break;
                }
            }
        };

        // Earnings with date filter
        $earnings = SalonPayment::where('salon_id', $salonId)
            ->where('status', 'confirm')
            ->where('is_refund', false)
            ->tap($dateQuery)
            ->sum('amount');

        // Appointments Count with date filter
        $appointmentsCount = Booking::where('salon_id', $salonId)
            ->tap($dateQuery)
            ->count();

        // Reviews Count with date filter
        $reviewsCount = Review::where('salon_id', $salonId)
            ->tap($dateQuery)
            ->count();

        // New Clients Count with date filter
        $newClientsCount = User::whereHas('bookings', function ($query) use ($salonId) {
            $query->where('salon_id', $salonId);
        })
            ->tap($dateQuery)
            ->count();

        // Invoices not paid with date filter
        $unpaidInvoicesCount = Booking::where('salon_id', $salonId)
            ->where('status', 'pending')
            ->tap($dateQuery)
            ->count();

        // Revenue Overview
        // Get monthly revenue data for the current year
        $monthlyData = collect(range(1, 12))->map(function ($month) use ($salonId) {
            $date = now()->setMonth($month)->startOfMonth();

            // Get income from confirmed payments
            $income = SalonPayment::where('salon_id', $salonId)
                ->where('status', 'confirm')
                ->where('is_refund', false)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->sum('amount');

            // Get expenses (could be expanded to include actual expense tracking)
            $expenses = 0;

            return [
                'name' => $date->format('M'),
                'income' => $income,
                'expenses' => $expenses
            ];
        })->toArray();

        // Appointments Completed Count with percentage and date filter
        $completedAppointmentsCount = Booking::where('salon_id', $salonId)
            ->where('status', 'completed')
            ->tap($dateQuery)
            ->count();
        $completedPercentage = $appointmentsCount > 0
            ? ($completedAppointmentsCount / $appointmentsCount) * 100
            : 0;

        // Appointments Cancelled Count with percentage and date filter
        $cancelledAppointmentsCount = Booking::where('salon_id', $salonId)
            ->where('status', 'canceled')
            ->tap($dateQuery)
            ->count();
        $cancelledPercentage = $appointmentsCount > 0
            ? ($cancelledAppointmentsCount / $appointmentsCount) * 100
            : 0;

        // Recent Activity: Appointments Today
        $appointmentsToday = Booking::where('salon_id', $salonId)
            ->whereDate('date', now()->toDateString())
            ->get();

        // Last 7 Reviews with date filter // load user relationship
        $last7Reviews = Review::where('salon_id', $salonId)
            // ->tap($dateQuery)
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->with('user')
            ->get();

        // Ads Active Count with date filter
        $adsActiveCount = PromotionAd::where('salon_id', $salonId)
            ->where('status', 'active')
            ->tap($dateQuery)
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
