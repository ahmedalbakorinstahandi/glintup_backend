<?php

namespace App\Http\Requests\Salons\SalonStaff;

use App\Http\Requests\BaseFormRequest;

class UpdatePermissionsRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:salon_permissions,id',
        ];
    }
}
