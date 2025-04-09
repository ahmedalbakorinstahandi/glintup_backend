<?php

namespace App\Models\Users;

use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wallet_id',
        'amount',
        'currency',
        'description',
        'transaction_type',
        'direction',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'currency'         => 'string',
        'transaction_type' => 'string',
        'direction'        => 'string',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
     ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class)->withTrashed();
    }

    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->direction === 'in' ? '+' : '-';
        return $sign . number_format($this->amount, 2) . HelperService::getCurrencySymbol($this->currency);
    }

    public function getShortTypeAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->transaction_type));
    }
}
