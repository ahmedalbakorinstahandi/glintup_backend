<?php

namespace App\Http\Requests\Services\Service;

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
            'description' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
            'icon' => 'required|string|max:110',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'gender' => 'required|in:male,female,both',
            'is_active' => 'required|boolean',
            'currency' => 'required|string|max:3',
            'is_home_service' => 'nullable|boolean',
            'is_beautician' => 'nullable|boolean',
        ];

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
