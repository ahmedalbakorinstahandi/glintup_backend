<?php

namespace App\Models\Salons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingHour extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'salon_id',
        'day_of_week',
        'opening_time',
        'closing_time',
        'is_closed',
        'break_start',
        'break_end',
    ];

    protected $casts = [
        'is_closed'   => 'boolean',
        'opening_time' => 'datetime:H:i:s',
        'closing_time' => 'datetime:H:i:s',
        'break_start' => 'datetime:H:i:s',
        'break_end'   => 'datetime:H:i:s',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    // public function getFullDayLabelAttribute(): string
    // {
    //     return ucfirst($this->day_of_week);
    // }
}
