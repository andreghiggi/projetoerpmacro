<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\ContratoEmpresa;

class ContratoUtil {

	public function gerarContrato($empresa, $config){
		try{
			$texto = $this->replaceTexto($config->texto, $empresa);
			return ContratoEmpresa::create([
				'empresa_id' => $empresa->id,
				'assinado' => 0,
				'data_assinatura' => null,
				'cpf_cnpj' => $empresa->cpf_cnpj,
				'texto' => $texto
			]);
		}catch(\Exception $e){
		}
	}

	public function replaceTexto($texto, $empresa){
		$texto = str_replace("%nome%", $empresa->nome, $texto);
		$texto = str_replace("%nome_fantasia%", $empresa->nome_fantasia, $texto);
		$texto = str_replace("%cpf_cnpj%", $empresa->cpf_cnpj, $texto);
		$texto = str_replace("%ie%", $empresa->ie, $texto);
		$texto = str_replace("%email%", $empresa->email, $texto);
		$texto = str_replace("%celular%", $empresa->celular, $texto);
		$texto = str_replace("%endereco%", $empresa->endereco, $texto);
		$texto = str_replace("%data%", date("d/m/Y H:i"), $texto);
		$texto = str_replace("%horario%", date("H:i"), $texto);

		return $texto;
	}
}
