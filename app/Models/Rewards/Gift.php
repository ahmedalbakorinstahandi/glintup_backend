<?php

namespace App\Models\Rewards;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Gift extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $fillable = [
        'name',
        'icon',
        'type',
        'amount',
        'currency',
        'order',
    ];

    protected $casts = [
        'amount'     => 'double',
        'order'      => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $translatable = ['name'];

    protected $appends = ['icon_url'];

    protected function name(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $multi ? $this->getAllTranslations('name') : $value,
        );
    }

    public function getIconUrlAttribute(): string
    {
        return asset('storage/' . $this->icon);
    }

    // public function getTypeLabelAttribute(): string
    // {
    //     return ucfirst($this->type);
    // }
}
