<?php

namespace App\Models\Users;

use App\Models\Booking\Booking;
use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'user_id',
        'amount',
        'currency',
        'reason',
        'status',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'currency'    => 'string',
        'status'      => 'string',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . HelperService::getCurrencySymbol($this->currency);
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }
}
