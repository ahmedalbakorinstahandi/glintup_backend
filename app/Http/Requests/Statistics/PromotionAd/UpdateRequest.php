<?php

namespace App\Http\Requests\Statistics\PromotionAd;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title'       => LanguageService::translatableFieldRules('nullable|string|max:255'),
            'button_text' => LanguageService::translatableFieldRules('nullable|string|max:1000'),
            'image'       => 'nullable|string|max:110',
            'valid_from'  => 'nullable|date',
            'valid_to'    => 'nullable|date|after_or_equal:valid_from',
            'is_active'   => 'nullable|boolean',
            'views'       => 'nullable|integer|min:0',
            'clicks'      => 'nullable|integer|min:0',
        ];
    }
}
