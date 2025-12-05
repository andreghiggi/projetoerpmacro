<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\Cliente;
use App\Models\Fornecedor;

class SpedUtil {

	public function updateOrCreateC190($data, $std){
		// if(sizeof($data) == 0){
		// 	$data[] = $std;
		// 	return $data;
		// }
		$dup = false;
		for($i=0; $i<sizeof($data); $i++){
			if($data[$i]->CST_ICMS == $std->CST_ICMS && $data[$i]->CFOP == $std->CFOP){
				$dup = true;
				$data[$i]->VL_ICMS += $std->VL_ICMS;
				$data[$i]->VL_OPR += $std->VL_OPR;
				$data[$i]->VL_BC_ICMS += $std->VL_BC_ICMS;

			}
		}
		if($dup == false){
			array_push($data, $std);
		}
		return $data;

	}

	public function getCliente($doc, $empresa_id){
		$cliente = Cliente::where('cpf_cnpj', $doc)
		->where('empresa_id', $empresa_id)->first();

		if($cliente == null){
			$doc = $this->__setMask($doc);
			$cliente = Cliente::where('cpf_cnpj', $doc)
			->where('empresa_id', $empresa_id)->first();
		}
		return $cliente;
	}

	public function getFornecedor($doc, $empresa_id){
		$fornecedor = Fornecedor::where('cpf_cnpj', $doc)
		->where('empresa_id', $empresa_id)->first();

		if($fornecedor == null){
			$doc = $this->__setMask($doc);
			$fornecedor = Fornecedor::where('cpf_cnpj', $doc)
			->where('empresa_id', $empresa_id)->first();
		}
		return $fornecedor;
	}

	private function __setMask($doc){
		$doc = preg_replace('/[^0-9]/', '', $doc);
		$mask = '##.###.###/####-##';
		if (strlen($doc) == 11) {
			$mask = '###.###.###-##';
		}
		return $this->__mask($doc, $mask);
	}

	private function __mask($val, $mask){
		$maskared = '';
		$k = 0;
		for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
			if ($mask[$i] == '#') {
				if (isset($val[$k])) {
					$maskared .= $val[$k++];
				}
			} else {
				if (isset($mask[$i])) {
					$maskared .= $mask[$i];
				}
			}
		}

		return $maskared;
	}

	public function trataCfop($cfop, $tipo, $xml_importado){
		if($tipo == 'venda' || $tipo == 'pdv') return $cfop;
		if($xml_importado == 0) return $cfop;

		$digito = substr($cfop, 0, 1);
		if($digito == 5) return "1" . substr($cfop, 1, 3);
		return "2" . substr($cfop, 1, 3);
	}

	public function cfopIsentoTributacao($cfop){
		$cfops = [
			'1400', '1401', '1403', '1406', '1407', '1408', '1409', '1410', '1411', '1414', '1415',
			'2400', '2401', '2403', '2406', '2407', '2408', '2409', '2410', '2411', '2414', '2415',
		];

		if(in_array($cfop, $cfops)) return 1;
		return 0;
	}
}