<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use App\Models\ApiConfig;

class ValidaApiToken
{

	public function handle($request, Closure $next){
		
		$token = $request->header('Authorization');

		$apiConfig = ApiConfig::where('token', $token)->first();
		if($apiConfig == null){
			return response()->json("Token não encontrado!", 401);
		}

		if($apiConfig->status == 0){
			return response()->json("Token desativado!", 401);
		}

		$request->merge([
			'empresa_id' => $apiConfig->empresa->id,
			'token' => $token
		]);

		return $next($request);
	}
}