<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use App\Models\Empresa;
use App\Models\Mdfe;
use App\Models\PlanoEmpresa;

class ValidaMDFe
{

	public function handle($request, Closure $next){

		$mdfe = Mdfe::findOrFail($request->id);

		$plano = PlanoEmpresa::where('empresa_id', $mdfe->empresa_id)
		->orderBy('data_expiracao', 'desc')
		->first();

		$totalMDFe = Mdfe::where('empresa_id', $mdfe->empresa_id)
		->where(function($q) {
			$q->where('estado_emissao', 'aprovado')->orWhere('estado_emissao', 'cancelado');
		})
		->whereMonth('created_at', date('m'))
		->count('id');

		if($totalMDFe >= $plano->plano->maximo_mdfes){
			return response()->json("Limite de emissões de MDFe atingido!", 401);
		}
		return $next($request);
	}
}