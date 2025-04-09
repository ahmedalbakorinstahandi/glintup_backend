<?php

namespace App\Http\Requests\Services\Group;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $user = User::auth();

        $rules = [
            'name' => LanguageService::translatableFieldRules('required|string|max:255'),
        ];

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'nullable|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
