<?php

namespace App\Http\Requests\Gift;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'name' => LanguageService::translatableFieldRules('required|string|max:255'),
            'icon' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }
}
