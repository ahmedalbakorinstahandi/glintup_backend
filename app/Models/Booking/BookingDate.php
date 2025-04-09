<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingDate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'date',
        'time',
        'created_by',
        'status',
    ];

    protected $casts = [
        'date'       => 'date',
        'time'       => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    public function getFullDatetimeAttribute(): string
    {
        return "{$this->date->format('Y-m-d')} {$this->time->format('H:i')}";
    }

    // public function getStatusLabelAttribute(): string
    // {
    //     return ucfirst($this->status);
    // }

    // public function getCreatedByLabelAttribute(): string
    // {
    //     return ucfirst($this->created_by);
    // }
}
