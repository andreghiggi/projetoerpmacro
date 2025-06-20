<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\Empresa;
use App\Models\PlanoConta;

class ContaEmpresaUtil {

	public function atualizaSaldo($itemConta){

		$conta = $itemConta->conta;
		$saldoDaConta = $conta->saldo;

		if($itemConta->tipo == 'entrada'){
			$saldoDaConta += $itemConta->valor;
		}else{
			$saldoDaConta -= $itemConta->valor;
		}

		$itemConta->saldo_atual = $saldoDaConta;
		$itemConta->save();
		$conta->saldo = $saldoDaConta;
		$conta->save();
	}
}