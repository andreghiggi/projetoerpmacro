<?php

namespace App\Http\Middleware;

use Closure;

class ImpersonateContador
{
    public function handle($request, Closure $next)
    {

        if (session()->has('impersonate_contador') && !session()->has('original_user')) {
            session(['original_user' => auth()->id()]);
        }

        return $next($request);
    }
}
