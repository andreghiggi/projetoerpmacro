<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class ValidaSuporte
{
	public function handle($request, Closure $next)
	{	
		
		if(__isSuporte()){
			return $next($request);
		}

		return redirect()->route('home');
	}
}
