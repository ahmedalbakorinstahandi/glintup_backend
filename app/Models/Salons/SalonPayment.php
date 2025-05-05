<?php

namespace App\Models\Salons;

use App\Models\Booking\Booking;
use App\Models\Users\User;
use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalonPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'paymentable_id',
        'paymentable_type',
        'user_id',
        'salon_id',
        'amount',
        'currency',
        'type',
        'method',
        'status',
        'is_refund',
        'system_percentage',
    ];

    protected $casts = [
        'code'         => 'string',
        'paymentable_id' => 'integer',
        'paymentable_type' => 'string',
        'user_id'      => 'integer',
        'salon_id'     => 'integer',
        'currency'     => 'string',
        'type'         => 'string',
        'method'       => 'string',
        'status'       => 'string',
        'amount'      => 'decimal:2',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
        'is_refund'   => 'boolean',
        'system_percentage' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    // ✅ خصائص مساعدة
    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2) . HelperService::getCurrencySymbol($this->currency);
    }

    // user
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // morphs
    public function paymentable()
    {
        return $this->morphTo();
    }

    // public function getStatusLabelAttribute(): string
    // {
    //     return ucfirst($this->status);
    // }

    // public function getTypeLabelAttribute(): string
    // {
    //     return ucfirst($this->type);
    // }
}
