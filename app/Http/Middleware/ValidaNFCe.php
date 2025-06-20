<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\PlanoEmpresa;
use App\Models\Nfce;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ValidaNFCe
{
	public function handle($request, Closure $next){

        $nfe = Nfce::findOrFail($request->id);
		
		$plano = PlanoEmpresa::where('empresa_id', $nfe->empresa_id)
		->orderBy('data_expiracao', 'desc')
		->first();

		$totalNfce = Nfce::where('empresa_id', $nfe->empresa_id)
		->where(function($q) {
			$q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
		})
		->whereMonth('created_at', date('m'))
		->count('id');

		if($totalNfce >= $plano->plano->maximo_nfces){
			return response()->json("Limite de emissões de NFCe atingido!", 401);
		}

		return $next($request);

	}

}
