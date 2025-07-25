<?php

namespace App\Models\Services;

use App\Models\Salons\Salon;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use PDO;

class Group extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $fillable = [
        'name',
        'salon_id',
        'key',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'key'       => 'string',
    ];

    protected $translatable = ['name'];

    // ✅ العلاقات
    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    // public function groupServices()
    // {
    //     return $this->hasMany(GroupService::class);
    // }


    public function groupServices()
    {

        if (Auth::check()) {
            $user = User::auth();

            $salon_id = 0;
            if ($user->isUserSalon()) {
                $salon_id = $user->salon->id;
            } else {
                $salon_id = request()->salon_id;
            }
        } else {
            $salon_id = request()->salon_id;
        }




        return $this->hasMany(GroupService::class)->where('salon_id', $salon_id);


        // return $this->hasMany(GroupService::class);
    }


    public function getGroupServices()
    {
        if (Auth::check()) {
            $user = User::auth();

            $salon_id = 0;
            if ($user->isUserSalon()) {
                $salon_id = $user->salon->id;
            } else {
                $salon_id = request()->salon_id;
            }
        } else {
            $salon_id = request()->salon_id ?? null;
        }



        if ($this->key == 'new') {
            $query = GroupService::where('salon_id', $salon_id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->with('service');
        } else {
            $query = GroupService::where('group_id', $this->id)
                ->where('salon_id', $salon_id)
                ->with('service');
        }

        return $query->get();
    }

    // ✅ accessors
    protected function name(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $multi ? $this->getAllTranslations('name') : $value,
        );
    }


    // can salon edit
    public function canSalonEdit()
    {
        $user = User::auth();

        if ($user && $user->isUserSalon()) {
            if ($user->salon->id == $this->salon_id) {
                return true;
            }
        } elseif ($user && $user->isAdmin()) {
            return true;
        }
        return false;
    }
}
