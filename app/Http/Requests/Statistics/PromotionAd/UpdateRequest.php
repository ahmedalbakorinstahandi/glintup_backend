<?php

namespace App\Http\Requests\Statistics\PromotionAd;

use App\Services\MessageService;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseFormRequest;
use App\Models\Salons\Salon;
use App\Models\Users\User;
use App\Services\LanguageService;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {


        $user = User::auth();

        $salon_id = $this->route('id');

        $salon = Salon::where('id', $salon_id)->first();

        if (!$salon) {
            MessageService::abort(404, 'messages.salon.item_not_found');
        }


        if ($user->isAdmin()) {
            return [
                'title'       => LanguageService::translatableFieldRules('nullable|string|max:255'),
                'button_text' => LanguageService::translatableFieldRules('nullable|string|max:15|min:3'),
                'image'       => 'nullable|string|max:110',
                'valid_from'  => 'nullable|date',
                'valid_to'    => 'nullable|date|after_or_equal:valid_from',
                'is_active'   => 'nullable|boolean',
                'status'      => 'nullable|in:approved,rejected',
            ];
        } elseif ($user->isUserSalon() && $user->salon->id == $salon->id) {
            if ($salon->status == 'draft') {
                return [
                    'title'       => LanguageService::translatableFieldRules('nullable|string|max:255'),
                    'button_text' => LanguageService::translatableFieldRules('nullable|string|max:15|min:3'),
                    'image'       => 'nullable|string|max:110',
                    'valid_from'  => 'nullable|date',
                    'valid_to'    => 'nullable|date|after_or_equal:valid_from',
                    'is_active'   => 'nullable|boolean',
                ];
            } else {
                return [
                    'is_active'   => 'nullable|boolean',
                ];
            }
        }

        return [];
    }
}
