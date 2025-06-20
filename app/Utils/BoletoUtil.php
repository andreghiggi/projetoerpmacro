<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\Empresa;
use App\Models\PlanoConta;

class BoletoUtil {

	public function gerarBoleto($beneficiario, $pagador, $dadosBoleto, $contaBoleto){

		$objeto = $this->preparaObjeto($beneficiario, $pagador, $dadosBoleto, $contaBoleto);
		try{
			$boleto = $this->geraBoleto($objeto, $contaBoleto);
			$empresa = Empresa::findOrFail($dadosBoleto['empresa_id']);

			$pdf = new \Eduardokum\LaravelBoleto\Boleto\Render\Pdf();
			$pdf->addBoleto($boleto);
			$fileName = date('d_m_Y_H:i:s') . "_". Str::random(10) . '.pdf';
			$pdf->gerarBoleto($pdf::OUTPUT_SAVE, public_path('boletos_pdf/'.$fileName));
			// dd($boleto);
			
			$linhaDigitavel = $boleto->getLinhaDigitavel();
			
			return [ 
				'fileName' => $fileName,
				'linhaDigitavel' => $linhaDigitavel
			];
		}catch(\Exception $e){

			return [
				'erro' => true,
				'mensagem' => $e->getMessage()
			];
		}
	}

	private function preparaObjeto($beneficiario, $pagador, $dadosBoleto, $contaBoleto){
		$logo = null;
		if($dadosBoleto['usar_logo']){
			$empresa = Empresa::findOrFail($dadosBoleto['empresa_id']);
			if($empresa->logo != null){
				if(file_exists(public_path('/uploads/logos/'. $empresa->logo))){
					$logo = public_path('/uploads/logos/'. $empresa->logo);
				}
			}
		}

		$data = [
			'logo' => $logo,
			'dataVencimento' => new \Carbon\Carbon($dadosBoleto['vencimento']),
			'valor' => $dadosBoleto['valor'],
			'multa' => $dadosBoleto['multa'],
			'juros' => $dadosBoleto['juros'],
			'numero' => $dadosBoleto['numero'],
			'numeroDocumento'=> $dadosBoleto['numero_documento'],
			'instrucoes' => [$dadosBoleto['instrucoes']],
			'aceite' => 'S',
			'especieDoc' => 'DM',
			'pagador' => $pagador,
			'beneficiario' => $beneficiario,
			'carteira' => $dadosBoleto['carteira'],
			'convenio' => $dadosBoleto['convenio'],
		];

		if($contaBoleto->banco == 'Sicoob' || $contaBoleto->banco == 'Bradesco' || $contaBoleto->banco == 'Sicredi' || $contaBoleto->banco == 'Itau' || $contaBoleto->banco == 'Banco do nordeste' || $contaBoleto->banco == 'Banco btg'){
			$data['agencia'] = $contaBoleto->agencia;
			$data['conta'] = $contaBoleto->conta;
		}

		if($contaBoleto->banco == 'Santander' || $contaBoleto->banco == 'C6'){
			$data['codigoCliente'] = $dadosBoleto['cliente_id'];
		}

		if($contaBoleto->banco == 'Sicredi'){
			$data['posto'] = $dadosBoleto['posto'];
		}

		if($contaBoleto->banco == 'Banco inter'){
			$data['operacao'] = rand(1111111111,9999999999);
			$data['nossoNumero'] = rand(1111111111,9999999999);
		}
		return $data;
	}

	private function geraBoleto($objeto, $contaBoleto){
		
		if($contaBoleto->banco == 'Banco do brasil'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Bb($objeto);
		}
		if($contaBoleto->banco == 'Sicoob'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob($objeto);
		}
		if($contaBoleto->banco == 'Bradesco'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Bradesco($objeto);
		}
		if($contaBoleto->banco == 'Santander'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Santander($objeto);
		}

		if($contaBoleto->banco == 'Sicredi'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi($objeto);
		}

		if($contaBoleto->banco == 'Itau'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Itau($objeto);
		}

		if($contaBoleto->banco == 'Banco inter'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Inter($objeto);
		}

		if($contaBoleto->banco == 'Banco do nordeste'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Bnb($objeto);
		}

		if($contaBoleto->banco == 'C6'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\C6($objeto);
		}

		if($contaBoleto->banco == 'Banco btg'){
			return new \Eduardokum\LaravelBoleto\Boleto\Banco\Btg($objeto);
		}
	}

	public function gerarBoletoParaRemessa($beneficiario, $pagador, $dadosBoleto, $contaBoleto){
		$objeto = $this->preparaObjeto($beneficiario, $pagador, $dadosBoleto, $contaBoleto);
		$boleto = $this->geraBoleto($objeto, $contaBoleto);
		return $boleto;
	}

	public function geraRemessa($boletos, $tipo, $banco, $data){
		$remessa = null;
		if($banco == 'Banco do brasil'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Bb($data);
			}else{
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Bb($data);
			}
		}

		if($banco == 'Sicoob'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Bancoob($data);
			}else{
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Bancoob($data);
			}
		}

		if($banco == 'Bradesco'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Bradesco($data);
			}else{
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Bradesco($data);
			}
		}

		if($banco == 'Santander'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Santander($data);
			}else{
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Santander($data);
			}
		}

		if($banco == 'Sicredi'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Sicredi($data);
			}else{
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Sicredi($data);
			}
		}

		if($banco == 'Itau'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Itau($data);
			}else{
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Itau($data);
			}
		}

		if($banco == 'Banco inter'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Inter($data);
			}else{

				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Inter($data);
			}
		}

		if($banco == 'Banco do nordeste'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Bnb($data);
			}else{

				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Bnb($data);
			}
		}

		if($banco == 'C6'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\C6($data);
			}else{

				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\C6($data);
			}
		}

		if($banco == 'Banco btg'){
			if($tipo == 'Cnab400'){
				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Btg($data);
			}else{

				$remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Btg($data);
			}
		}

		$remessa->addBoletos($boletos);
		$fileName = date('d_m_Y_H:i:s') . "_". Str::random(10) . '.txt';
		$remessa->save(public_path('remessas_boleto/'.$fileName));
		return $fileName;
	}

};