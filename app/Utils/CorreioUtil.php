<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\ConfiguracaoSuper;

class CorreioUtil {

	protected $__url = 'https://api.correios.com.br';
	protected $__user = '';
	protected $__pass = '';
	protected $__postagem = '';

	public function updateToken(){
		try{
			$curl = curl_init();
			$headers = [
				'Content-Type:application/json',
				'Authorization: Basic '. base64_encode($this->__user.":".$this->__pass)
			];

			$data = [ 
				'numero' => $this->__postagem
			];

			curl_setopt($curl, CURLOPT_URL, $this->__url . "/token/v1/autentica/cartaopostagem");
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($curl, CURLOPT_HEADER, false);
			$result = curl_exec($curl);
			curl_close($curl);

			$obj = json_decode($result);
			if(isset($obj->token)){
			// dd($obj);
				$configSystem = ConfiguracaoSuper::first();

				$configSystem->token_correios = $obj->token;
				$configSystem->token_expira_correios = $obj->expiraEm;
				$configSystem->dr_correios = $obj->cartaoPostagem->dr;
				$configSystem->contrato_correios = $obj->cartaoPostagem->contrato;
				$configSystem->save();
			}

			return $result;
		}catch(\Exception $e){

		}
	}

	public function codigosSericos(){
		return [
			'03298' => 'PAC',
			'03220' => 'SEDEX',
			'03158' => 'SEDEX 10',
		];
	}

	public function getValores($cepOrigem, $cepDestino, $altura, $largura, $comprimento, $peso){
		try{
			$configSystem = ConfiguracaoSuper::first();
			if($configSystem != null){
				$this->__pass = $configSystem->codigo_acesso_correios;
				$this->__user = $configSystem->usuario_correios;
				$this->__postagem = $configSystem->cartao_postagem_correios;
			}
			$this->updateToken();
			$configSystem = ConfiguracaoSuper::first();

			$curl = curl_init();
			$headers = [
				'Content-Type:application/json',
				'Authorization: Bearer '. $configSystem->token_correios
			];

			$codigosSericos = $this->codigosSericos();
			$retorno = [];
			foreach($codigosSericos as $key => $servico){
			// preço
				$dataPreco = [
					'idLote' => '1',
					'parametrosProduto' => [
						[
							'cepOrigem' => $cepOrigem,
							'cepDestino' => $cepDestino,
							'nuContrato' => $configSystem->contrato_correios,
							'nuDR' => $configSystem->dr_correios,
							'nuRequisicao' => '1',
							'tpObjeto' => '2',
							'diameto' => '0',
							'altura' => $altura,
							'largura' => $largura,
							'comprimento' => $comprimento,
							'psObjeto' => $peso,
							'coProduto' => $key,
							'dtEvento' => date('d-m-Y')
						]
					]
				];

				curl_setopt($curl, CURLOPT_URL, $this->__url . "/preco/v1/nacional");
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataPreco));
				curl_setopt($curl, CURLOPT_HEADER, false);
				$result = curl_exec($curl);
				curl_close($curl);

				$obj = json_decode($result);	

				if(isset($obj->msgs)){
					return [];
				}

				if(!isset($obj[0]->txErro)){

					$arr = [
						'tipo' => $servico,
						'valor' => $obj[0]->pcFinal
					];

			// prazo

					$dataPrazo = [
						'idLote' => '1',
						'parametrosPrazo' => [
							[
								'cepOrigem' => $cepOrigem,
								'cepDestino' => $cepDestino,
								'nuRequisicao' => '1',
								'coProduto' => $key,
								'dtEvento' => date('d-m-Y')
							]
						]
					];

					curl_setopt($curl, CURLOPT_URL, $this->__url . "/prazo/v1/nacional");
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataPrazo));
					curl_setopt($curl, CURLOPT_HEADER, false);
					$result = curl_exec($curl);
					curl_close($curl);

					$obj = json_decode($result);

				// dd($obj);

					$arr['prazo_entrega'] = $obj[0]->prazoEntrega;
					$arr['data_maxima'] = \Carbon\Carbon::parse($obj[0]->dataMaxima)->format('d/m/Y');
					$arr['mensagem_prazo'] = isset($obj[0]->msgPrazo) ? $obj[0]->msgPrazo : '';

					array_push($retorno, $arr);
				}
			}

			return $retorno;
		}catch(\Exception $e){
			// echo $e->getMessage();
			// die;
			return [];
		}
	}
}