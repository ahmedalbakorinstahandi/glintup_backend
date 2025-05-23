<?php

namespace App\Models\Booking;

use App\Models\Services\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'service_id',
        'price',
        'currency',
        'discount_percentage',
        'start_date_time',
        'end_date_time',
        'duration_minutes',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'price' => 'float',
        'discount_percentage' => 'float',
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }
}
