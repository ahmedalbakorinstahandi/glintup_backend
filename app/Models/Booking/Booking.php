<?php

namespace App\Models\Booking;

use App\Models\Salons\Salon;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'user_id',
        'salon_id',
        'date',
        'time',
        'status',
        'payment_status',
        'notes',
        'salon_notes',
    ];

    protected $casts = [
        'date'           => 'datetime',
        'time'           => 'datetime:H:i:s',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    // ✅ العلاقات
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    //BookingDate
    public function bookingDates()
    {
        return $this->hasMany(BookingDate::class);
    }
    //

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    // end time : time + getTotalServiceTimeInMinutes()
    public function getEndTimeAttribute()
    {
        return $this->time?->addMinutes($this->getTotalServiceTimeInMinutes());
    }


    public function getTotalServiceTimeInMinutes()
    {
        $totalMinutes = $this->bookingServices->sum(function ($service) {
            return $service->duration_minutes;
        });

        return max($totalMinutes, 15);
    }


    public function payments()
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class)->withTrashed();
    }

    public function refund()
    {
        return $this->hasOne(\App\Models\Users\Refund::class)->withTrashed();
    }
}
