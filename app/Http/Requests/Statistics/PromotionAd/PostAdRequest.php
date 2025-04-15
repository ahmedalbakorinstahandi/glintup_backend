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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|string|max:110',
            'link_url' => 'required|url|max:255',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:start_date',
            'message' => 'nullable|string|max:1000',
        ];
    }
}