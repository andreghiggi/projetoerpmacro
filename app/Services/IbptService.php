<?php
namespace App\Services;

use NFePHP\Ibpt\Ibpt;

class IbptService{

	protected $token = ""; 
	protected $cnpj = ""; 
	public function __construct($token, $cnpj){
		$this->token = $token;
		$this->cnpj = $cnpj;
	}

	public function consulta($data){
		$ibpt = new Ibpt($this->cnpj, $this->token);
		$resp = $ibpt->productTaxes(
			$data['uf'],
			$data['ncm'],
			$data['extarif'],
			$data['descricao'],
			$data['unidadeMedida'],
			$data['valor'],
			$data['gtin'],
			$data['codigoInterno']
		);

		return $resp;
	}

}