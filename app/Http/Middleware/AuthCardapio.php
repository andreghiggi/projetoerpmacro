<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ConfiguracaoCardapio;

class AuthCardapio
{
    
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        $config = ConfiguracaoCardapio::
        where('api_token', $token)
        ->first();
        
        if($config == null){
            return response()->json($token, 403);
        }

        $request->merge(['empresa_id' => $config->empresa_id]);
        return $next($request);
    }
}
