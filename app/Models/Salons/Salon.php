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
