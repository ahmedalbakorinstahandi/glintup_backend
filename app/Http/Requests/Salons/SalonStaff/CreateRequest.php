<?php

namespace App\Http\Requests\Salons\SalonStaff;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone' => ['required', 'phone:AUTO'],
            'gender'     => 'required|in:male,female',
            'birth_date' => 'required|date',
            'password'   => 'required|string|min:6',
            'position'   => 'required|string|max:255',
            'is_active'  => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:salon_permissions,id,deleted_at,NULL',
        ];

        $user = User::auth();

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
