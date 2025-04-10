<?php

namespace App\Http\Requests\Statistics\PromotionAd;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title'       => LanguageService::translatableFieldRules('required|string|max:255'),
            'description' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
            'image'       => 'required|string|max:110',
            'valid_from'  => 'required|date',
            'valid_to'    => 'required|date|after_or_equal:valid_from',
            'is_active'   => 'required|boolean',
        ];
    }
}
