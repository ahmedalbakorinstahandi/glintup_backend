<?php

namespace App\Http\Requests\Salons\SalonSocialMediaSite;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'link'                 => 'nullable|url|max:512',
        ];
    }
}
