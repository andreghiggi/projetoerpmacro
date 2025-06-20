<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HashEmpresa
{
    public function handle($request, Closure $next){

		$empresa = Empresa::where('hash', $request->hash)->first();
		if($empresa != null) {
			$request->merge(['empresa_id' => $empresa->id]);
		}

		return $next($request);
	}
}
