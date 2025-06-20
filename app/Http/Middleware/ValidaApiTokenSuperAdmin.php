<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use App\Models\ConfiguracaoSuper;

class ValidaApiTokenSuperAdmin
{

	public function handle($request, Closure $next){
		
		$token = $request->header('Authorization');

		$apiConfig = ConfiguracaoSuper::where('token_api', $token)->first();
		if($apiConfig == null){
			return response()->json("Token não encontrado!", 401);
		}

		return $next($request);
	}
}