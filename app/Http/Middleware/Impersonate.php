<?php

namespace App\Http\Middleware;

use Closure;

class Impersonate
{
    public function handle($request, Closure $next)
    {

        if (session()->has('impersonate') && !session()->has('original_user')) {
            session(['original_user' => auth()->id()]);
        }

        return $next($request);
    }
}
