<?php

namespace App\Http\Requests\Services\Group;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class ReOrderRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'orders' => 'required|integer|exists:groups,orders,deleted_at,NULL',
        ];
    }
}
