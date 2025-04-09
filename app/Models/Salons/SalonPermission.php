<?php

namespace App\Models\Salons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SalonPermission extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $fillable = [
        'name',
        'key',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $translatable = ['name'];

    protected function name(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $multi ? $this->getAllTranslations('name') : $value,
        );
    }

    public function userSalonPermissions()
    {
        return $this->hasMany(UserSalonPermission::class);
    }
}
