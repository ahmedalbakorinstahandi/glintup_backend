<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'booking_id',
        // 'amount',
        'tax',
        'discount',
        // 'status',
    ];

    protected $casts = [
        // 'amount'      => 'double',
        'tax'         => 'double',
        // 'discount'    => 'double',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    // ✅ خصائص مخصصة
    public function getTotalAmountAttribute(): float
    {
        return $this->booking->getTotalPriceAttribute();
    }



    public function getStatusAttribute(): string
    {
        // status paid / partially_paid / unpaid
        $totalPaid = $this->booking->payments->where('status', 'confirm')->sum('amount');
        $totalAmount = $this->booking->getTotalPriceAttribute();

        if ($totalPaid >= $totalAmount) {
            return 'paid';
        }

        if ($totalPaid > 0) {
            return 'partially_paid';
        }

        return 'unpaid';
    }
}
