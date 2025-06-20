<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\CashBackCliente;

class ValidaCashBack
{
	public function handle($request, Closure $next)
	{	
		
		if(__isMaster()){
			return $next($request);
		}

		$empresa_id = auth::user()->empresa ? auth::user()->empresa->empresa_id : null;
		try{
			$data = CashBackCliente::where('empresa_id', $empresa_id)
			->where('status', 1)
			->get();

			$dataHoje = date('Y-m-d');
			foreach($data as $item){

				if(strtotime($dataHoje) > strtotime($item->data_expiracao)){
					$item->status = 0;
					$item->save();

					$cliente = $item->cliente;
					$cliente->valor_cashback = $cliente->valor_cashback - $item->valor_credito;
					$cliente->save();
				}
			}
		}catch(\Exception $e){
		}
		return $next($request);
	}
}
