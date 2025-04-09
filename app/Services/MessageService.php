<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class MessageService
{


    public static function abort($status, $message)
    {
        abort(
            response()->json(
                [
                    'success' => false,
                    'message' => trans($message),
                ],
                $status
            )
        );
    }
}
