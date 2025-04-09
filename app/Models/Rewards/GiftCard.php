<?php

namespace App\Models\Rewards;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiftCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'sender_id',
        'recipient_id',
        'phone_code',
        'phone',
        'type',
        'amount',
        'currency',
        'services',
        'tax',
        'message',
        'is_used',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'tax'           => 'double',
        'services'      => 'array',
        'is_used'       => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id')->withTrashed();
    }

    public function getFullPhoneAttribute(): string
    {
        return $this->phone_code . $this->phone;
    }

    // public function getTypeLabelAttribute(): string
    // {
    //     return ucfirst($this->type);
    // }
}
