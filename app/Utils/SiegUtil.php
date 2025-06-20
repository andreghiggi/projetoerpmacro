<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\EscritorioContabil;

class SiegUtil {

	public function enviarXml($empresa_id, $fileDir){
		$escritorio = EscritorioContabil::
		where('empresa_id', $empresa_id)
		->first();

		$file = file_get_contents($fileDir);

		if($escritorio != null && $escritorio->token_sieg != null){
			$url = "https://api.sieg.com/aws/api-xml.ashx";

			$curl = curl_init();
			$headers = [];

			$data = $file;
			curl_setopt($curl, CURLOPT_URL, $url . "?apikey=".$escritorio->token_sieg."&email=".$escritorio->email);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$xml = json_decode(curl_exec($curl));
			if(isset($xml->Message)){
				if($xml->Message == 'Importado com sucesso'){
					return $xml->Message;
				}
			}
			return false;
		}
		return false;
	}
}
