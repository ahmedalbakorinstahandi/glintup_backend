<?php

namespace App\Models\Rewards;

use App\Models\Users\User;
use App\Models\Salons\Salon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyPoint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'salon_id',
        'points',
        'description',
        'taken_at',
        'used_at',
    ];

    protected $casts = [
        'points'      => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    // free service relation morph
    public function freeService()
    {
        return $this->morphOne(FreeService::class, 'freeable');
    }
}
