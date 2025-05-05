<?php

namespace App\Http\Requests\Statistics\PromotionAd;

use App\Http\Requests\BaseFormRequest;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class PostAdRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title'       => LanguageService::translatableFieldRules('required|string|max:255'),
            'button_text' => LanguageService::translatableFieldRules('nullable|string|max:15|min:3'),
            'image' => 'required|string|max:110',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:start_date',
            'success_url' => 'required|url|max:255',
            'cancel_url' => 'required|url|max:255',
        ];
    }
}
