<?php

namespace App\Models\Services;

use App\Models\Salons\Salon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Group extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $fillable = [
        'name',
        'salon_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $translatable = ['name'];

    // ✅ العلاقات
    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function groupServices()
    {
        return $this->hasMany(GroupService::class);
    }

    // ✅ accessors
    protected function name(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $multi ? $this->getAllTranslations('name') : $value,
        );
    }
}
