<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\PlanoEmpresa;
use App\Models\Empresa;
use App\Models\ConfiguracaoSuper;
use App\Models\FinanceiroBoleto;

class ValidaPlano
{
	public function handle($request, Closure $next)
	{	

		if(__isMaster()){
			return $next($request);
		}

		$empresa_id = auth::user()->empresa ? auth::user()->empresa->empresa_id : null;
		if($empresa_id == null){
			session()->flash("flash_error", "Usuário sem empresa!");
			return redirect()->route('home');
		}

		$emp = Empresa::findOrFail($empresa_id);
		if($emp->status == 0){
			session()->flash("flash_error", "Empresa desativada!");
			return redirect()->route('home');
		}

		$plano = PlanoEmpresa::where('empresa_id', $empresa_id)
		->orderBy('data_expiracao', 'desc')
		->first();

		if($plano == null){
			session()->flash("flash_error", "Empresa sem plano atribuído!");
			return redirect()->route('home');
		}

		if($emp->receber_com_boleto){
			$config = ConfiguracaoSuper::first();
			$diasAtrasoSuspender = $config->dias_atraso_suspender_boleto;

			// $fatura = FinanceiroBoleto::where('empresa_id', $empresa_id)
			// ->whereMonth('created_at', date('m'))
			// ->where('status', 0)
			// ->first();

			$fatura = $this->validaFaturasAnteriores($empresa_id);

			if($fatura == null){
				return $next($request);
			}

			$diferenca = strtotime(date('Y-m-d')) - strtotime($fatura->vencimento);
			$dif = (int)floor($diferenca / (60 * 60 * 24));
			if($dif > $diasAtrasoSuspender){
				session()->flash("flash_financeiro", 1);
				session()->flash("flash_error", "Realize o pagamento da sua fatura!");
				return redirect()->route('home');
			}
		}else{

			if(date('Y-m-d') > $plano->data_expiracao){
				session()->flash("flash_error", "Plano expirado!");
				return redirect()->route('home');
			}
		}

		return $next($request);
	}

	private function validaFaturasAnteriores($empresa_id){
		$mes = 1;
		$fim = (int)date('m');
		for($i=1; $i<=$fim; $i++){
			$fatura = FinanceiroBoleto::where('empresa_id', $empresa_id)
			->whereMonth('vencimento', $mes)
			->where('status', 0)
			->first();

			if($fatura != null) return $fatura;
			$mes++;
		}
	}

}
