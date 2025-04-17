<?php

namespace App\Models\Rewards;

use App\Models\Booking\Booking;
use App\Models\Users\User;
use App\Models\Services\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FreeService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_id',
        'source',
        'booking_id',
        'is_used',
        'freeable_id',
        'freeable_type',
    ];

    protected $casts = [
        'user_id'    => 'integer',
        'service_id' => 'integer',
        'source'     => 'string',
        'booking_id' => 'integer',
        'is_used'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'freeable_id' => 'integer',
        'freeable_type' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }
    public function freeable()
    {
        return $this->morphTo();
    }

    // public function getSourceLabelAttribute(): string
    // {
    //     return ucfirst($this->source);
    // }
}
