<?php

namespace App\Models\Booking;

use App\Models\Salons\Salon;
use App\Services\HelperService;
use Faker\Extension\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'salon_id',
        'discount_type',
        'discount_value',
        'max_uses',
        'max_uses_per_user',
        'start_date',
        'end_date',
        'min_age',
        'max_age',
        'gender',
        'is_active',
    ];

    protected $casts = [
        'discount_value'      => 'double',
        'max_uses'            => 'integer',
        'max_uses_per_user'   => 'integer',
        'min_age'             => 'integer',
        'max_age'             => 'integer',
        'is_active'           => 'boolean',
        'start_date'          => 'datetime',
        'end_date'            => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date !== null && now()->gt($this->end_date);
    }

    public function getDiscountLabelAttribute(): string
    {
        return $this->discount_type === 'percentage'
            ? "{$this->discount_value}%"
            : number_format($this->discount_value, 2) . ' ' . HelperService::getCurrencySymbol('AED');
    }
}
