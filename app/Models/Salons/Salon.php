<?php

namespace App\Models\Salons;

use App\Models\General\Image;
use App\Models\Services\Group;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_active'   => 'boolean',
        'latitude'    => 'double',
        'longitude'   => 'double',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id')->withTrashed();
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
        return $this->hasMany(\App\Models\Services\Service::class);
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


    // get active or not from working hours and holidays
    public function isOpen(): bool
    {
        $currentDay = strtolower(now()->format('l')); // Get current day in lowercase
        $currentTime = now()->format('H:i:s'); // Get current time in 24-hour format
        $currentDate = now()->toDateString(); // Get current date

        // Check if today is a holiday
        $holiday = $this->holidays()
            ->where('holiday_date', $currentDate)
            ->first();

        if ($holiday) {
            if ($holiday->is_full_day) {
                return false; // Salon is closed for the full day
            }

            // Check if current time is within holiday hours
            if ($holiday->start_time && $holiday->end_time) {
                if ($currentTime >= $holiday->start_time && $currentTime <= $holiday->end_time) {
                    return false; // Salon is closed during holiday hours
                }
            }
        }

        // Check working hours
        $workingHour = $this->workingHours()
            ->where('day_of_week', $currentDay)
            ->first();

        if (!$workingHour || $workingHour->is_closed) {
            return false; // Salon is closed for the day
        }

        // Check if current time is within opening and closing hours
        if ($currentTime >= $workingHour->opening_time && $currentTime <= $workingHour->closing_time) {
            // Check if current time is not within break hours
            if ($workingHour->break_start && $workingHour->break_end) {
                if ($currentTime >= $workingHour->break_start && $currentTime <= $workingHour->break_end) {
                    return false; // Salon is on break
                }
            }
            return true; // Salon is open
        }

        return false; // Salon is closed
    }

    // Define relationship with holidays
    public function holidays()
    {
        return $this->hasMany(\App\Models\Salons\SalonHoliday::class, 'salon_id');
    }


    public function isAvailableAt(string $dateTime): bool
    {
        $date = \Carbon\Carbon::parse($dateTime);
        $dayOfWeek = strtolower($date->format('l')); // Get day of the week in lowercase
        $time = $date->format('H:i:s'); // Extract time in 24-hour format
        $currentDate = $date->toDateString(); // Extract date

        // Check if the given date is a holiday
        $holiday = $this->holidays()
            ->where('holiday_date', $currentDate)
            ->first();

        if ($holiday) {
            if ($holiday->is_full_day) {
                return false; // Salon is closed for the full day
            }

            // Check if the given time is within holiday hours
            if ($holiday->start_time && $holiday->end_time) {
                if ($time >= $holiday->start_time && $time <= $holiday->end_time) {
                    return false; // Salon is closed during holiday hours
                }
            }
        }

        // Check working hours for the given day
        $workingHour = $this->workingHours()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$workingHour || $workingHour->is_closed) {
            return false; // Salon is closed for the day
        }

        // Check if the given time is within opening and closing hours
        if ($time >= $workingHour->opening_time && $time <= $workingHour->closing_time) {
            // Check if the given time is not within break hours
            if ($workingHour->break_start && $workingHour->break_end) {
                if ($time >= $workingHour->break_start && $time <= $workingHour->break_end) {
                    return false; // Salon is on break
                }
            }
            return true; // Salon is open
        }

        return false; // Salon is closed
    }


    public function getWorkingStatus(string $locale = 'en'): string
    {
        $currentDay = strtolower(now()->format('l')); // Get current day in lowercase
        $currentTime = now()->format('H:i:s'); // Get current time in 24-hour format
        $currentDate = now()->toDateString(); // Get current date

        $status = [];
        $holiday = $this->holidays()->where('holiday_date', $currentDate)->first();

        if ($holiday) {
            if ($holiday->is_full_day) {
                return $locale === 'ar' ? 'مغلق اليوم بسبب مناسبة' : 'Closed today due to an event';
            }

            if ($holiday->start_time && $holiday->end_time) {
                if ($currentTime >= $holiday->start_time && $currentTime <= $holiday->end_time) {
                    return $locale === 'ar' ? 'مغلق الآن بسبب مناسبة' : 'Currently closed due to an event';
                }
                $status[] = $locale === 'ar'
                    ? "مناسبة من " . date('h:i A', strtotime($holiday->start_time)) . " إلى " . date('h:i A', strtotime($holiday->end_time))
                    : "Event from " . date('h:i A', strtotime($holiday->start_time)) . " to " . date('h:i A', strtotime($holiday->end_time));
            }
        }

        $workingHour = $this->workingHours()->where('day_of_week', $currentDay)->first();

        if (!$workingHour || $workingHour->is_closed) {
            return $locale === 'ar' ? 'مغلق اليوم' : 'Closed today';
        }

        if ($currentTime >= $workingHour->opening_time && $currentTime <= $workingHour->closing_time) {
            if ($workingHour->break_start && $workingHour->break_end) {
                if ($currentTime >= $workingHour->break_start && $currentTime <= $workingHour->break_end) {
                    return $locale === 'ar'
                        ? "مغلق الآن بسبب استراحة من " . date('h:i A', strtotime($workingHour->break_start)) . " إلى " . date('h:i A', strtotime($workingHour->break_end))
                        : "Currently closed for a break from " . date('h:i A', strtotime($workingHour->break_start)) . " to " . date('h:i A', strtotime($workingHour->break_end));
                }
                $status[] = $locale === 'ar'
                    ? "استراحة من " . date('h:i A', strtotime($workingHour->break_start)) . " إلى " . date('h:i A', strtotime($workingHour->break_end))
                    : "Break from " . date('h:i A', strtotime($workingHour->break_start)) . " to " . date('h:i A', strtotime($workingHour->break_end));
            }
            $status[] = $locale === 'ar'
                ? "مفتوح من " . date('h:i A', strtotime($workingHour->opening_time)) . " إلى " . date('h:i A', strtotime($workingHour->closing_time))
                : "Open from " . date('h:i A', strtotime($workingHour->opening_time)) . " to " . date('h:i A', strtotime($workingHour->closing_time));
        } else {
            return $locale === 'ar'
                ? "مغلق الآن، ساعات العمل من " . date('h:i A', strtotime($workingHour->opening_time)) . " إلى " . date('h:i A', strtotime($workingHour->closing_time))
                : "Currently closed, working hours are from " . date('h:i A', strtotime($workingHour->opening_time)) . " to " . date('h:i A', strtotime($workingHour->closing_time));
        }

        return implode(' | ', $status);
    }

    public function workingHours()
    {
        return $this->hasMany(\App\Models\Salons\WorkingHour::class, 'salon_id');
    }



    public function reviews()
    {
        return $this->hasMany(\App\Models\Services\Review::class);
    }

    public function groupServices()
    {
        return $this->hasMany(\App\Models\Services\GroupService::class);
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


        // ToDO :: fix this later
        // $distance = haversineGreatCircleDistance(
        //     $this->latitude,
        //     $this->longitude,
        //     $userLocation['lat'],
        //     $userLocation['lng']
        // );

        // return round($distance, 2);

        return rand(1, 100) + 0.0;
    }
}
