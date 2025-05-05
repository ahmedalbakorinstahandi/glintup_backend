<?php

namespace App\Http\Requests\Statistics\PromotionAd;


class SendToReviewRequest
{
    public function rules(): array
    {
        return [
            'success_url' => 'required|url|max:255',
            'cancel_url' => 'required|url|max:255',
        ];
    }
}
