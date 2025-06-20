<?php

namespace App\Http\Middleware;

use App\Models\Cte;
use Closure;
use Response;
use App\Models\PlanoEmpresa;

class ValidaCTe
{

	public function handle($request, Closure $next){
		
		$cte = Cte::findOrFail($request->id);

		$plano = PlanoEmpresa::where('empresa_id', $cte->empresa_id)
		->orderBy('data_expiracao', 'desc')
		->first();

		$totalCte = Cte::where('empresa_id', $cte->empresa_id)
		->where(function($q) {
			$q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
		})
		->whereMonth('created_at', date('m'))
		->count('id');

		if($totalCte >= $plano->plano->maximo_ctes){
			return response()->json("Limite de emissões de CTe atingido!", 401);
		}
		return $next($request);
	}
}