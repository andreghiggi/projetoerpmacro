<?php

namespace App\Utils;

error_reporting(E_ALL);

class SintegraUtil{

	public function getXml($venda, $path){

		if(file_exists(public_path($path).$venda->chave.'.xml')){
			$xml = simplexml_load_file(public_path($path).$venda->chave.'.xml');
			try{
				return $xml;
			}catch(\Exception $e){
				return null;
			}
		}else{
			return null;
		}
	}

	public function getItensNfe($xml){
		$prod = $xml->NFe->infNFe->det;
		return $prod;
	}

	public function getIde($xml){
		$prod = $xml->NFe->infNFe->ide;
		return $prod;
	}

	public function getChave($xml){
		$chave = substr($xml->NFe->infNFe->attributes()->Id, 3, 44);
		return $chave;
	}

	public function getTotal($xml){
		$total = $xml->NFe->infNFe->total->ICMSTot;
		return $total;
	}

	public function getDestinatario($xml){
		$dest = $xml->NFe->infNFe->dest;
		return $dest;
	}
}