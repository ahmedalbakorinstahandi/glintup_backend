<?php

namespace App\Models\Statistics;

use App\Models\General\Setting;
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
        'button_text',
        'image',
        'valid_from',
        'valid_to',
        'is_active',
        'status',
        'views',
        'clicks',
    ];

    protected $casts = [
        'salon_id'    => 'integer',
        'title'       => 'string',
        'button_text' => 'string',
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

    protected $translatable = ['title', 'button_text'];

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

    protected function buttonText(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(?string $value) => $multi ? $this->getAllTranslations('button_text') : $value,
        );
    }

    // amount

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $startDate = new \DateTime($this->valid_from);
                $endDate = new \DateTime($this->valid_to);
                $interval = $startDate->diff($endDate);

                $ad_price_day = Setting::where('key', 'adver_cost_per_day')->first()->value;

                $hours = $interval->h;
                $minutes = $interval->i;
                $amount = $interval->days * $ad_price_day + ($hours / 24) * $ad_price_day + ($minutes / 1440) * $ad_price_day;

                return $amount;
            }
        );
    }

}
