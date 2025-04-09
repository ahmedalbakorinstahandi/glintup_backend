<?php

namespace App\Models\General;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'notificationable_id',
        'notificationable_type',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'read_at'          => 'datetime',
        'metadata'         => 'array',
        'notificationable_id' => 'integer',
        'notificationable_type' => 'string',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    // notificationable polymorphic relationship
    public function notificationable()
    {
        return $this->morphTo()->withTrashed();
    }
}
