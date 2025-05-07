<?php

namespace App\Http\Requests\Salons\SalonMenuRequest;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreateRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'notes' => 'required|string',
            'success_url' => 'required|string',
            'cancel_url' => 'required|string',
        ];
    }
}
