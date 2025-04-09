<?php

namespace App\Http\Middleware;

use App\Models\Users\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = explode(',', $request->header('Accept-Language', 'en'))[0];
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }


        if (Auth::check()) {
            $user = User::auth();

            if ($user->language != $locale) {
                $user->language = $locale;
                $user->save();
            }
        }

        return $next($request);
    }
}
