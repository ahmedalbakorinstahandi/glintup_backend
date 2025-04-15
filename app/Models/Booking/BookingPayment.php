<?php

namespace App\Models\Booking;

use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'amount',
        'currency',
        'type',
        'method',
        'status',
        'is_refund',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
        'is_refund'   => 'boolean',
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

    // public function getStatusLabelAttribute(): string
    // {
    //     return ucfirst($this->status);
    // }

    // public function getTypeLabelAttribute(): string
    // {
    //     return ucfirst($this->type);
    // }
}
