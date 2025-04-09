<?php

namespace App\Models\Rewards;

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
        'is_used',
    ];

    protected $casts = [
        'is_used'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    // public function getSourceLabelAttribute(): string
    // {
    //     return ucfirst($this->source);
    // }
}
