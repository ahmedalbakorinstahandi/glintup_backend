<?php

namespace App\Http\Requests\Salons\SocialMediaSite;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => LanguageService::translatableFieldRules('required|string|max:255'),
            'icon' => 'required|string|max:110',
        ];
    }
}
