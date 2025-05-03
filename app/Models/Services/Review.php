<?php

namespace App\Models\Services;

use App\Models\Users\User;
use App\Models\Salons\Salon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'salon_id',
        'rating',
        'comment',
        'salon_reply',
        'salon_report',
        'salon_reported_at',
        'reason_for_report',
        'salon_reply_at',
        'is_reviewed',
        'is_visible',
    ];

    protected $casts = [
        'user_id'             => 'integer',
        'salon_id'            => 'integer',
        'rating'              => 'integer',
        'comment'             => 'string',
        'salon_reported_at'   => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
        'salon_reply'         => 'string',
        'salon_reply_at'      => 'datetime',
        'reason_for_report'   => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('⭐', $this->rating);
    }
}
