<?php

namespace App\Http\Requests\Salons\SocialMediaSite;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => LanguageService::translatableFieldRules('nullable|string|max:255'),
            'icon' => 'nullable|string|max:110',
        ];
    }
}
