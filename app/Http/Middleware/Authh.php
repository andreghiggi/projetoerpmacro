<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use Illuminate\Support\Facades\Auth;

class Authh
{
	public function handle($request, Closure $next){
		if(!auth::user()){
			return redirect('/login');
		}
		
		if(auth::user()->email == env("MAILMASTER")){
			auth::user()->master = 1;
		}
		return $next($request);
	}
}