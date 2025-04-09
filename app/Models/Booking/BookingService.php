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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
