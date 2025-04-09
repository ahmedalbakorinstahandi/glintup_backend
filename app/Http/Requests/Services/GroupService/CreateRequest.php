<?php

namespace App\Http\Requests\Services\GroupService;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {

        $user = User::auth();


        $rules = [
            'group_id' => 'required|exists:groups,id,deleted_at,NULL',
            'service_id' => 'required|exists:services,id,deleted_at,NULL',
        ];

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
