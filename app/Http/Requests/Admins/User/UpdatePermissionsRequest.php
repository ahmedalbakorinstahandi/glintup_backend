<?php

namespace App\Http\Requests\Admins\User;

use App\Http\Requests\BaseFormRequest;

class UpdatePermissionsRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:admin_permissions,id,deleted_at,NULL',
        ];
    }
}
