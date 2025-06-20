<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VerificaMaster
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

        if(__isSuporte()){
            $uri = $_SERVER['REQUEST_URI'];
            if(in_array($uri, $this->notAcceptSuporte())){
                session()->flash("flash_error", "Aceso restrito");
                return redirect()->route('home');
            }
            return $next($request);
        }

        session()->flash("flash_error", "Aceso restrito");
        return redirect()->route('home');
    }

    private function notAcceptSuporte(){
        return [
            '/planos',
            '/gerenciar-planos',
            '/planos-pendentes',
            '/financeiro-plano'
        ];
    }
}
