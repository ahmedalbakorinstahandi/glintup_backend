<?php

namespace App\Models\Booking;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'booking_id',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }
}
