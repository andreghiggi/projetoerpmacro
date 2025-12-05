<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use App\Models\Empresa;
use App\Models\Nfce;
use App\Models\PlanoEmpresa;

class ValidNfce
{

	public function handle($request, Closure $next){
		
		$token = $request->header('Authorization');
		$emitente = $request->emitente;
		$chave = $request->chave;

		$empresa = Empresa::where('token', $token)->first();
		if($empresa == null){
			return response()->json("Empresa não encontrada!", 401);
		}

		if($empresa->status == 0){
			return response()->json("Empresa desativada!", 401);
		}

		$plano = PlanoEmpresa::where('empresa_id', $empresa->id)
		->orderBy('data_expiracao', 'desc')
		->first();

		$totalNfe = Nfce::where('empresa_id', $empresa->id)
		->where(function($q) {
			$q->where('estado', 'aprovado')->orWhere('estado', 'cancelado');
		})
		->whereMonth('data_emissao', date('m'))
		->count('id');

		if($totalNfe >= $plano->plano->maximo_nfces){
			return response()->json("Limite de emissões de NFCe atingido!", 401);
		}

		$request->merge([
			'empresa_id' => $empresa->id
		]);

		return $next($request);
	}
}