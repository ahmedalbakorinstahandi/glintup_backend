<?php

namespace App\Models\Salons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonHoliday extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'salon_id',
        'holiday_date',
        'reason',
        'is_full_day',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'start_time'   => 'datetime:H:i:s',
        'end_time'     => 'datetime:H:i:s',
        'is_full_day'  => 'boolean',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function getIsPartialAttribute(): bool
    {
        return !$this->is_full_day;
    }
}
