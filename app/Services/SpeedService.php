<?php

namespace App\Services;

error_reporting(E_ALL);
ini_set('display_errors', 'On');
class SpeedService{

	protected $config = null;
	public function __construct($config){
		$this->config = $config;
	}

	public function getXml($venda, $path, $attr = null){

		if(file_exists(public_path($path).$venda->chave.'.xml')){
			try{
				$xml = simplexml_load_file(public_path($path).$venda->chave.'.xml');
				return $xml;
			}catch(\Exception $e){
				return null;
			}
		}elseif(file_exists(public_path($path).$venda[$attr].'.xml')){
			try{
				$xml = simplexml_load_file(public_path($path).$venda[$attr].'.xml');
				return $xml;
			}catch(\Exception $e){
				return null;
			}
		}else{
			return null;
		}
	}

	public function getItemNfe($xml){
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

	public function getEmitente($xml){
		$emit = $xml->NFe->infNFe->emit;
		return $emit;
	}
}