<?php

namespace App\Models\General;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone_code',
        'phone',
        'avatar',
        'user_id',
    ];

    protected $casts = [
        'phone_code' => 'string',
        'phone' => 'string',
        'avatar' => 'string',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getFullPhoneAttribute(): string
    {
        return $this->phone_code . $this->phone;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
