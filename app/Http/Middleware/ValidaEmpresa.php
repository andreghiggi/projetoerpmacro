<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class ValidaEmpresa
{
	public function handle($request, Closure $next)
	{	
		$request->merge(['empresa_id' => auth::user()->empresa ? auth::user()->empresa->empresa_id : null]);
		return $next($request);
	}
}
