<?php

namespace App\Models\Salons;

use App\Http\Services\Services\GroupService;
use App\Models\Booking\Booking;
use App\Models\General\Image;
use App\Models\General\Setting;
use App\Models\Rewards\GiftCard;
use App\Models\Rewards\LoyaltyPoint;
use App\Models\Services\Group;
use App\Models\Services\Review;
use App\Models\Services\Service;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Salon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'icon',
        'phone_code',
        'phone',
        'email',
        'description',
        'location',
        'is_approved',
        'is_active',
        'type',
        'latitude',
        'longitude',
        'country',
        'city',
        'merchant_legal_name',
        'merchant_commercial_name',
        'address',
        'city_street_name',
        'contact_name',
        'contact_number',
        'contact_email',
        'business_contact_name',
        'business_contact_email',
        'business_contact_number',
        'types',
        'block_message',
        'bio',
        'tags',
        'loyalty_service_id',
        'vat_number',
        'service_location',
        'bank_name',
        'bank_account_number',
        'bank_account_holder_name',
        'bank_account_iban',
        'services_list',
        'trade_license',
        'vat_certificate',
        'bank_account_certificate',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_active'   => 'boolean',
        'latitude'    => 'double',
        'longitude'   => 'double',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
        // 'service_location' => 'enum:in_house,in_center,in_house_and_center',
        'bank_name' => 'string',
        'bank_account_number' => 'string',
        'bank_account_holder_name' => 'string',
        'bank_account_iban' => 'string',
        'services_list' => 'string',
        'trade_license' => 'string',
        'vat_certificate' => 'string',
        'bank_account_certificate' => 'string',
        'vat_number' => 'string',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id')->withTrashed();
    }

    // salon staff
    public function staff()
    {
        return $this->hasMany(SalonStaff::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    //loyalty_service_id
    public function loyaltyService()
    {
        return $this->belongsTo(Service::class, 'loyalty_service_id')->withTrashed();
    }


    // get the service with the highest discount
    public function getServiceWithHighestDiscountPercentage()
    {
        return $this->services()->where('discount_percentage', '>', 0)->orderBy('discount_percentage', 'desc')->first()->discount_percentage ?? null;
    }


    public function myLoyaltyPoints()
    {
        return $this->hasOne(LoyaltyPoint::class, 'salon_id')
            ->where('user_id', User::auth()->id);
    }

    // get my gift cards in this salon not token and not used
    public function myGiftCards()
    {
        $user = User::auth();

        $giftCards = GiftCard::where('salon_id', $this->id)
            ->where('recipient_id', $user->id)
            ->where('is_used', false)
            ->whereNull('received_at')
            ->get();

        return $giftCards;
    }

    // SalonMenuRequest
    public function menuRequests()
    {
        return $this->hasMany(SalonMenuRequest::class, 'salon_id');
    }

    // can user review
    public function canUserReview()
    {
        if (!Auth::check()) {
            return false;
        }

        $user = User::auth();

        if (!$user->isCustomer()) {
            return false;
        }

        // Check if the user already has a review for this salon
        $existingReview = $this->reviews()
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            return false;
        }

        // Check if the user has at least one completed booking for this salon
        $booking = $user->bookings()
            ->where('salon_id', $this->id)
            ->where('status', 'completed')
            ->first();

        return $booking ? true : false;
    }


    //isHomeServiceSalon
    public function isHomeServiceSalon()
    {
        return $this->type == 'home_service' || $this->type == 'beautician';
    }


    // Define relationship with loyalty points
    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class, 'salon_id');
    }


    // is_makeup_artist
    public function isMakeupArtist()
    {
        return $this->types == 'beautician';
    }

    public function mostBookedServices()
    {
        // service resource
        return $this->services()
            ->withCount(['bookings as bookings_count' => function ($query) {
                $query->select(DB::raw('count(distinct booking_id)'));
            }])
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();
    }

    //مميز هل هو اعلى حجوزات خلال اخر 3 ايام  اي من اعلى 5 صالونات بالحجوزات خلال اخر 3 ايام
    public function isMostBooked()
    {
        $threeDaysAgo = now()->subDays(3);

        $mostBookedSalons = Booking::where('created_at', '>=', $threeDaysAgo)
            ->select('salon_id')
            ->groupBy('salon_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->pluck('salon_id');

        return $mostBookedSalons->contains($this->id);
    }


    //أحدث التقييمات 
    public function latestReviews()
    {
        return $this->hasMany(Review::class)
            ->latest()
            ->limit(5);
    }


    public function socialMediaSites()
    {
        return $this->hasManyThrough(
            SocialMediaSite::class,        // الجدول النهائي
            SalonSocialMediaSite::class,   // الجدول الوسيط
            'salon_id',         // المفتاح الأجنبي في الجدول الوسيط الذي يربطه بالصالون
            'id',               // المفتاح الأساسي في جدول SocialMidiaSite
            'id',               // المفتاح الأساسي في جدول Salon
            'social_media_site_id' // المفتاح الأجنبي في الجدول الوسيط الذي يشير إلى SocialMidiaSite
        )->withTrashed();
    }


    // salon images morph many
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // public function groups()
    // {
    //     return $this->hasMany(Group::class, 'salon_id');
    // }

    public function groupsIncludingGlobal()
    {
        return Group::where('salon_id', $this->id)
            ->orWhereNull('salon_id')
            ->get();
    }


    //SalonCustomer
    public function customers()
    {
        return $this->hasMany(User::class, 'salon_id');
    }


    // get active or not from working hours and holidays
    public function isOpen(): bool
    {
        $now = now();
        $currentDay = strtolower($now->format('l'));
        $currentDate = $now->toDateString();

        $holiday = $this->holidays()
            ->where('holiday_date', $currentDate)
            ->first();

        if ($holiday) {
            if ($holiday->is_full_day) {
                return false;
            }

            if ($holiday->start_time && $holiday->end_time) {
                $holidayStart = $now->copy()->setTimeFromTimeString($holiday->start_time);
                $holidayEnd = $now->copy()->setTimeFromTimeString($holiday->end_time);
                if ($now->between($holidayStart, $holidayEnd)) {
                    return false;
                }
            }
        }

        $workingHour = $this->workingHours()
            ->where('day_of_week', $currentDay)
            ->first();

        if (!$workingHour || $workingHour->is_closed) {
            return false;
        }

        $openingTime = $now->copy()->setTimeFromTimeString($workingHour->opening_time);
        $closingTime = $now->copy()->setTimeFromTimeString($workingHour->closing_time);

        if ($now->between($openingTime, $closingTime)) {
            if ($workingHour->break_start && $workingHour->break_end) {
                $breakStart = $now->copy()->setTimeFromTimeString($workingHour->break_start);
                $breakEnd = $now->copy()->setTimeFromTimeString($workingHour->break_end);
                if ($now->between($breakStart, $breakEnd)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }


    // Define relationship with holidays
    public function holidays()
    {
        return $this->hasMany(\App\Models\Salons\SalonHoliday::class, 'salon_id');
    }


    public function isAvailableAt(string $dateTime): bool
    {
        $date = \Carbon\Carbon::parse($dateTime);
        $dayOfWeek = strtolower($date->format('l'));
        $currentDate = $date->toDateString();

        $holiday = $this->holidays()
            ->where('holiday_date', $currentDate)
            ->first();

        if ($holiday) {
            if ($holiday->is_full_day) {
                return false;
            }

            if ($holiday->start_time && $holiday->end_time) {
                $holidayStart = $date->copy()->setTimeFromTimeString($holiday->start_time);
                $holidayEnd = $date->copy()->setTimeFromTimeString($holiday->end_time);
                if ($date->between($holidayStart, $holidayEnd)) {
                    return false;
                }
            }
        }

        $workingHour = $this->workingHours()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$workingHour || $workingHour->is_closed) {
            return false;
        }

        $openingTime = $date->copy()->setTimeFromTimeString($workingHour->opening_time);
        $closingTime = $date->copy()->setTimeFromTimeString($workingHour->closing_time);

        if ($date->between($openingTime, $closingTime)) {
            if ($workingHour->break_start && $workingHour->break_end) {
                $breakStart = $date->copy()->setTimeFromTimeString($workingHour->break_start);
                $breakEnd = $date->copy()->setTimeFromTimeString($workingHour->break_end);
                if ($date->between($breakStart, $breakEnd)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }



    public function getWorkingStatus(string $locale = 'en'): string
    {
        $now = now();
        $currentDay = strtolower($now->format('l')); // sunday, monday, etc.
        $currentDate = $now->toDateString(); // YYYY-MM-DD

        $holiday = $this->holidays()->where('holiday_date', $currentDate)->first();

        if ($holiday) {
            if ($holiday->is_full_day) {
                return $locale === 'ar' ? 'مغلق اليوم بسبب مناسبة' : 'Closed today due to an event';
            }

            if ($holiday->start_time && $holiday->end_time) {
                $holidayStart = $now->copy()->setTimeFromTimeString($holiday->start_time);
                $holidayEnd = $now->copy()->setTimeFromTimeString($holiday->end_time);

                if ($now->between($holidayStart, $holidayEnd)) {
                    return $locale === 'ar' ? 'مغلق الآن بسبب مناسبة' : 'Currently closed due to an event';
                }
            }
        }

        $workingHour = $this->workingHours()->where('day_of_week', $currentDay)->first();

        if (!$workingHour || $workingHour->is_closed) {
            return $locale === 'ar' ? 'مغلق اليوم' : 'Closed today';
        }

        // إعداد أوقات العمل
        $openingTime = $now->copy()->setTimeFromTimeString($workingHour->opening_time);
        $closingTime = $now->copy()->setTimeFromTimeString($workingHour->closing_time);

        if ($openingTime >= $closingTime) {
            return $locale === 'ar' ? 'مغلق اليوم (خطأ في الإعدادات)' : 'Closed today (invalid schedule)';
        }

        if ($now->between($openingTime, $closingTime)) {
            // التحقق من فترة الاستراحة
            if ($workingHour->break_start && $workingHour->break_end) {
                $breakStart = $now->copy()->setTimeFromTimeString($workingHour->break_start);
                $breakEnd = $now->copy()->setTimeFromTimeString($workingHour->break_end);

                if ($now->between($breakStart, $breakEnd)) {
                    return $locale === 'ar'
                        ? "مغلق الآن بسبب استراحة من " . $breakStart->format('h:i A') . " إلى " . $breakEnd->format('h:i A')
                        : "Currently closed for a break from " . $breakStart->format('h:i A') . " to " . $breakEnd->format('h:i A');
                }
            }

            return $locale === 'ar' ? 'مفتوح الآن' : 'Currently open';
        }

        return $locale === 'ar'
            ? "مغلق الآن، ساعات العمل من " . $openingTime->format('h:i A') . " إلى " . $closingTime->format('h:i A')
            : "Currently closed, working hours are from " . $openingTime->format('h:i A') . " to " . $closingTime->format('h:i A');
    }


    public function workingHours()
    {
        return $this->hasMany(\App\Models\Salons\WorkingHour::class, 'salon_id');
    }



    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function groupServices()
    {
        return $this->hasMany(GroupService::class);
    }

    public function getIconUrlAttribute(): string
    {
        return asset('storage/' . $this->icon);
    }

    public function getFullPhoneAttribute(): string
    {
        return $this->phone_code . $this->phone;
    }

    public function getLocationCoordinatesAttribute(): ?array
    {
        if (!$this->latitude || !$this->longitude) return null;
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
        ];
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('rating') ?? 0.0;
    }


    // get persntage of evry rating level
    public function getRatingPercentageAttribute(): array
    {
        $totalReviews = $this->reviews()->count();
        $ratingCounts = array_fill(1, 5, 0); // Initialize counts for ratings 1 to 5

        if ($totalReviews > 0) {
            $ratings = $this->reviews()->select('rating')->get();

            foreach ($ratings as $rating) {
                if (isset($ratingCounts[$rating->rating])) {
                    $ratingCounts[$rating->rating]++;
                }
            }
        }

        $percentage = [];
        foreach ($ratingCounts as $key => $value) {
            $percentage[$key] = [
                'rating' => $key,
                'percentage' => $totalReviews == 0 ? null : round(($value / $totalReviews) * 100, 2),
            ];
        }

        return $percentage;
    }


    public function getWhatsappLinkAttribute(): string
    {
        return 'https://api.whatsapp.com/send?phone='  . str_replace('+', '', $this->full_phone);
    }


    public function getDistance($user): ?float
    {
        if (!$user->latitude || !$user->longitude) return null;
        if (!$this->latitude || !$this->longitude) return null;


        $distance = $this->haversineGreatCircleDistance(
            $this->latitude,
            $this->longitude,
            $user->latitude,
            $user->longitude
        );

        return round($distance, 2);

        // return rand(1, 100) + 0.0;
    }

    public function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $earthRadius = 6371000; // Radius of the earth in meters
        $dLat = deg2rad($latitudeTo - $latitudeFrom);
        $dLon = deg2rad($longitudeTo - $longitudeFrom);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }



    // service_location display text

    //     makeup_artist_home_service_text_ar
    // makeup_artist_center_service_text_ar

    // makeup_artist_center_and_home_service_text_ar

    // makeup_artist_home_service_text_en
    // makeup_artist_center_service_text_en

    // makeup_artist_center_and_home_service_text_en
    public function getServiceLocationTextAttribute()
    {
        $text_key = '';
        if ($this->type == 'beautician' && $this->service_location != null) {
            if ($this->service_location == 'in_house') {
                $text_key = 'makeup_artist_home_service_text';
            } else if ($this->service_location == 'in_center') {
                $text_key = 'makeup_artist_center_service_text';
            } else if ($this->service_location == 'in_house_and_center') {
                $text_key = 'makeup_artist_center_and_home_service_text';
            }

            $locales = config('translatable.locales');


            $texts = [];
            foreach ($locales as $locale) {
                $texts[$locale] = Setting::where('key', $text_key . '_' . $locale)->first()->value;
            }

            return $texts;
        }

        return null;
    }


    public function getTradeLicenseUrlAttribute(): string
    {
        return asset('storage/' . $this->trade_license);
    }

    public function getVatCertificateUrlAttribute(): string
    {
        return asset('storage/' . $this->vat_certificate);
    }

    public function getBankAccountCertificateUrlAttribute(): string
    {
        return asset('storage/' . $this->bank_account_certificate);
    }


    public function getServicesListUrlAttribute(): string
    {
        return asset('storage/' . $this->services_list);
    }
}
