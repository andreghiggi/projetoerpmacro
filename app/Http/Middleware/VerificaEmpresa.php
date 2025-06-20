<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VerificaEmpresa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next){

        if(__isMaster()){
    		return $next($request);
        }
    	if(auth::user()->empresa){
    		return $next($request);
    	}

        if(auth::user()->suporte){
            return $next($request);
        }

    	session()->flash("flash_error", "Configure a empresa para continuar!");
    	return redirect()->route('config.index');
    }
}
