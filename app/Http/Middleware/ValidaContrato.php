<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa;
use App\Models\ContratoConfig;
use App\Models\ContratoEmpresa;
use App\Utils\ContratoUtil;

class ValidaContrato
{

	protected $except = [
		'assinar-contrato',
	];
	protected $contratoUtil;

	public function __construct(ContratoUtil $contratoUtil){
		$this->contratoUtil = $contratoUtil;
	}

	public function handle($request, Closure $next)
	{	
		if(__isMaster()){
			return $next($request);
		}
		$config = ContratoConfig::first();

		if($config == null || $config->status == 0){
			return $next($request);
		}

		$empresa_id = auth::user()->empresa ? auth::user()->empresa->empresa_id : null;
		if($empresa_id == null){
			session()->flash("flash_error", "Usuário sem empresa!");
			return redirect()->route('home');
		}

		$emp = Empresa::findOrFail($empresa_id);

		$contrato = ContratoEmpresa::where('empresa_id', $empresa_id)
		->first();

		if($contrato == null){
			$contrato = $this->contratoUtil->gerarContrato($emp, $config);
		}

		if($contrato == null){
			return $next($request);
		}

		if($contrato->assinado){
			return $next($request);
		}

		$dif = strtotime(date("Y-m-d H:i:s")) - strtotime($contrato->created_at);
		// $dif = floor($dif / (60 * 60 * 24 * 30));
		$dif = floor($dif / (60 * 60 * 24));
		if($dif >= $config->limite_dias_assinar){
			session()->flash("flash_warning", "Assine o contrato para continuar o uso do sistema!");
			return redirect()->route('assinar-contrato.index');
		}
		//cria contrato
		return $next($request);
	}
}
