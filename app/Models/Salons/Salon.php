<?php

namespace App\Models\Salons;

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

    // ✅ العلاقات
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id')->withTrashed();
    }

    public function services()
    {
        return $this->hasMany(\App\Models\Services\Service::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Services\Review::class);
    }

    public function groupServices()
    {
        return $this->hasMany(\App\Models\Services\GroupService::class);
    }

    // ✅ خصائص مخصصة
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
}
