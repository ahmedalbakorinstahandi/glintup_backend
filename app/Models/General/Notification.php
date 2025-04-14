<?php

namespace App\Models\General;

use App\Models\Users\User;
use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'notificationable_id',
        'notificationable_type',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'read_at'          => 'datetime',
        'metadata'         => 'array',
        'notificationable_id' => 'integer',
        'notificationable_type' => 'string',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    public $translatable = [
        'title',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    // notificationable polymorphic relationship
    public function notificationable()
    {
        return $this->morphTo()->withTrashed();
    }


    // // title
    protected function title(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                $raw = $this->getRawOriginal('title');

                // Decode if it's a JSON string
                if (is_string($raw)) {
                    $raw = json_decode($raw, true);
                }

                // Return 'cu' if exists
                if (is_array($raw) && isset($raw['cu'])) {
                    return $raw['cu'];
                }

                // Fallback to default translation
                return $value;
            }
        );
    }
    protected function message(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                $raw = $this->getRawOriginal('message');

                // Decode if it's a JSON string
                if (is_string($raw)) {
                    $raw = json_decode($raw, true);
                }

                // Return 'cu' if exists
                if (is_array($raw) && isset($raw['cu'])) {
                    return $raw['cu'];
                }

                // Fallback to default translation
                return $value;
            }
        );
    }
}
