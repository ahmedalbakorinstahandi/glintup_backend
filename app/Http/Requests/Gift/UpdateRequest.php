<?php

namespace App\Http\Requests\Gift;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
