<?php

namespace App\Http\Middleware;

use App\Models\Users\User;
use App\Services\MessageService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = User::auth();

        if (!$user->isCustomer()) {
            MessageService::abort(503, 'message.permission_error');
        }

        if ($user->is_active == 0) {
            MessageService::abort(503, 'messages.user.is_banned');
        }


        return $next($request);
    }
}
