<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\ConfiguracaoSuper;
use App\Models\Empresa;
use App\Models\Localizacao;

class WhatsAppUtil
{

	public function sendMessage($numero, $mensagem, $empresa_id, $file = null){
		$nodeurl = 'https://api.criarwhats.com/send';

		$config = ConfiguracaoSuper::first();
		if($config == null){
			return false;
		}

		if($config->token_whatsapp == null){
			return false;
		}
		
		$data = [
			'receiver'  => $numero,
			'msgtext'   => $mensagem,
			'token'     => $config->token_whatsapp,
		];

		if($file != null){
			$data['mediaurl'] = $file;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_URL, $nodeurl);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	public function sendMessageWithToken($numero, $mensagem, $empresa_id, $token){
		$nodeurl = 'https://api.criarwhats.com/send';

		$data = [
			'receiver'  => $numero,
			'msgtext'   => $mensagem,
			'token'     => $token,
		];

		// dd($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_URL, $nodeurl);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	public function sendMessageWithLocal($numero, $mensagem, $local_id){
		$nodeurl = 'https://api.criarwhats.com/send';
		$local = Localizacao::findOrFail($local_id);
		$token = $local->token_whatsapp;

		if($token == null){
			$config = ConfiguracaoSuper::first();
			if($config == null){
				return false;
			}
		}

		$data = [
			'receiver'  => $numero,
			'msgtext'   => $mensagem,
			'token'     => $token,
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_URL, $nodeurl);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

}