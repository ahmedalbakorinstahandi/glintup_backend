<?php

// CreateRequest.php
namespace App\Http\Requests\Salons\SalonSocialMediaSite;

use App\Http\Requests\BaseFormRequest;
use App\Models\Users\User;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {

        $rules = [
            'social_media_site_id' => 'required|exists:social_media_sites,id',
            'link'                 => 'required|url|max:512',
        ];

        $user = User::auth();

        if ($user->isAdmin()) {
            $rules['salon_id'] = 'required|exists:salons,id,deleted_at,NULL';
        }

        return $rules;
    }
}
