<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\PlanoEmpresa;
use App\Models\Nfe;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ValidaNFe
{
	public function handle($request, Closure $next){

        $nfe = Nfe::findOrFail($request->id);

		$plano = PlanoEmpresa::where('empresa_id', $nfe->empresa_id)
		->orderBy('data_expiracao', 'desc')
		->first();

		$totalNfe = Nfe::where('empresa_id', $nfe->empresa_id)
		->where(function($q) {
			$q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
		})
		->whereMonth('data_emissao', date('m'))
		->count('id');

		if($totalNfe >= $plano->plano->maximo_nfes){
			return response()->json("Limite de emissões de NFe atingido!", 401);
		}

		return $next($request);
	}

}
