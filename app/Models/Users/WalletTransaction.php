<?php

namespace App\Models\Users;

use App\Services\HelperService;
use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class WalletTransaction extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'description',
        'status',
        'type',
        'is_refund',
        'transactionable_id',
        'transactionable_type',
        'direction',
        'metadata',
    ];

    protected $translatable = [
        'description',
    ];

    protected $casts = [
        'amount'              => 'decimal:2',
        'currency'            => 'string',
        'user_id'             => 'integer',
        'description'         => 'string',
        'type'                => 'string',
        'is_refund'           => 'boolean',
        'transactionable_id'  => 'integer',
        'transactionable_type' => 'string',
        'direction'           => 'string',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'metadata'           => 'json',
    ];


    protected function description(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $multi ? $this->getAllTranslations('description') : $value,
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
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

    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }
}
