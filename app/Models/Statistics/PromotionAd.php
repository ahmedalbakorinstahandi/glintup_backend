<?php

namespace App\Models\Statistics;

use App\Models\Salons\Salon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PromotionAd extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $fillable = [
        'salon_id',
        'title',
        'description',
        'image',
        'valid_from',
        'valid_to',
        'is_active',
        'views',
        'clicks',
    ];

    protected $casts = [
        'salon_id'    => 'integer',
        'title'       => 'string',
        'description' => 'string',
        'image'       => 'string',
        'valid_from'  => 'date',
        'valid_to'    => 'date',
        'is_active'   => 'boolean',
        'views'       => 'integer',
        'clicks'      => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $translatable = ['title', 'description'];

    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    protected function title(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $multi ? $this->getAllTranslations('title') : $value,
        );
    }

    protected function description(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(?string $value) => $multi ? $this->getAllTranslations('description') : $value,
        );
    }
}
