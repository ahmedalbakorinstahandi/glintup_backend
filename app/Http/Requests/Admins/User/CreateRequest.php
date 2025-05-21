<?php

namespace App\Http\Requests\Admins\User;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'phone' => ['required', 'phone:AUTO'],
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'is_active'   => 'nullable|boolean',

            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:admin_permissions,id,deleted_at,NULL',
        ];
    }
}
