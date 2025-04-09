<?php

namespace App\Http\Requests\Services\Service;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => LanguageService::translatableFieldRules('nullable|string|max:255'),
            'description' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
            'icon' => 'nullable|string|max:110',
            'duration_minutes' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'gender' => 'nullable|in:male,female,both',
            'is_active' => 'nullable|boolean',
            'order' => 'nullable|integer',
        ];
    }
}
