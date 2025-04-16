<?php

namespace App\Models\Booking;

use App\Models\Salons\Salon;
use App\Models\Users\User;
use App\Services\HelperService;
use App\Services\MessageService;
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


    // get if the coupon is valid for current user
    public function getIsValidAttribute(): bool
    {
        $user = User::auth();

        // max_uses
        if ($this->max_uses !== null && $this->couponUsages()->count() >= $this->max_uses) {
            return false;
        }

        if ($user->isCustomer()) {
            // max_uses_per_user
            if ($this->max_uses_per_user !== null && $this->couponUsages()->where('user_id', $user->id)->count() >= $this->max_uses_per_user) {
                return false;
            }

            // min_age
            if ($this->min_age !== null && $user->age < $this->min_age) {
                return false;
            }


            // max_age
            if ($this->max_age !== null && $user->age > $this->max_age) {
                return false;
            }

            // gender
            if ($this->gender !== null && $this->gender !== $user->gender) {
                return false;
            }
        }

        // start_date
        if ($this->start_date !== null && now()->lt($this->start_date)) {
            return false;
        }

        // end_date
        if ($this->end_date !== null && now()->gt($this->end_date)) {
            return false;
        }

        // is_active
        if ($this->is_active !== null && !$this->is_active) {
            return false;
        }



        return true;
    }

    // get discount value for input amount
    public function getAmountAfterDiscount($amount): float
    {
        if ($this->discount_type === 'percentage') {
            return $amount -  ($amount * ($this->discount_value / 100));
        }

        return $amount - $this->discount_value;
    }



    // CouponUsage
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }


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
