<?php

namespace App\Utils;

class IfoodUtil{

	public function getUserCode($config){
		$url = "https://merchant-api.ifood.com.br/authentication/v1.0/oauth/userCode";

		$curl = curl_init();

		$headers = [];
		curl_setopt($curl, CURLOPT_URL, $url . "?clientId=".$config->clientId);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($curl, CURLOPT_HEADER, false);
		$result = json_decode(curl_exec($curl));
		curl_close($curl);

		if(isset($result->authorizationCodeVerifier)){
			$authorizationCodeVerifier = $result->authorizationCodeVerifier;
			$verificationUrlComplete = $result->verificationUrlComplete;
			$userCode = $result->userCode;

			if($userCode){
				$item = $config;
				$item->userCode = $userCode;
				$item->authorizationCodeVerifier = $authorizationCodeVerifier;
				$item->verificationUrlComplete = $verificationUrlComplete;
				$item->save();
				return $userCode;
			}
			return "";
		}else{
			echo "Algo errado, retorno iFood: ";
			print_r($result);
			die;
		}
	}

	public function oAuthToken($config){
		$url = "https://merchant-api.ifood.com.br/authentication/v1.0/oauth/token";

		$ch = curl_init();
		$grantType = $config->grantType;

		if($config->accessToken != ""){
			$grantType = 'refresh_token';
		}

		$clientId = $config->clientId;
		$clientSecret = $config->clientSecret;
		$authorizationCode = $config->authorizationCode;
		$authorizationCodeVerifier = $config->authorizationCodeVerifier;

		$params = "?grantType=$grantType&clientId=$clientId&clientSecret=$clientSecret&authorizationCode=$authorizationCode&authorizationCodeVerifier=$authorizationCodeVerifier";

		if($config->accessToken != ""){
			$params .= "&refreshToken=" . $config->refreshToken;
		}
		$headers = [];
		curl_setopt($ch, CURLOPT_URL, $url . $params);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		if(!isset($result->error)){

			$accessToken = $result->accessToken;
			$refreshToken = $result->refreshToken;
			$item = $config;
			$item->accessToken = $result->accessToken;
			$item->refreshToken = $result->refreshToken;

			$item->save();
			return ['success' => 1, 'token' => $accessToken];
		}else{
			$item = $config;
			$item->save();
			return ['success' => 0, 'message' => $result->error->message];

		}

	}

	public function newToken($config){
		$url = "https://merchant-api.ifood.com.br/authentication/v1.0/oauth/token";

		$ch = curl_init();
		$grantType = $config->grantType;

		$clientId = $config->clientId;
		$clientSecret = $config->clientSecret;
		$authorizationCode = $config->authorizationCode;
		$authorizationCodeVerifier = $config->authorizationCodeVerifier;

		$params = "?grantType=$grantType&clientId=$clientId&clientSecret=$clientSecret&authorizationCode=$authorizationCode&authorizationCodeVerifier=$authorizationCodeVerifier";

		$headers = [];
		curl_setopt($ch, CURLOPT_URL, $url . $params);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		if(!isset($result->error)){

			$accessToken = $result->accessToken;
			$refreshToken = $result->refreshToken;
			$item = $config;
			$item->accessToken = $result->accessToken;
			$item->refreshToken = $result->refreshToken;

			$item->save();
			return ['success' => 1, 'token' => $accessToken];
		}else{
			// echo $result->error->message;
			// die;
			$item = $config;
			$item->save();
			return ['success' => 0, 'message' => $result->error->message];

		}

	}

	public function statusMerchant($config){
		$url = "https://merchant-api.ifood.com.br/merchant/v1.0/merchants/".$config->merchantId."/status";

		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken,
		];

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);

		return $result;
	}

	public function getInterruptions($config){
		$url = "https://merchant-api.ifood.com.br/merchant/v1.0/merchants/".$config->merchantId."/interruptions";

		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken,
		];

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);

		return $result;

	}

	public function getCatalogs($config){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/catalogs";
		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken
		];

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		return $result;

	}

	public function getCategories($config){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/catalogs/".
		$config->catalogId."/categories";

		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken
		];

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		return $result;

	}

	public function storeCategory($config, $data){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/catalogs/".
		$config->catalogId."/categories";

		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken,
			'Content-Type: application/json'
		];
		$payload = json_encode($data);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		return $result;

	}

	public function updateCategory($config, $data, $id){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/catalogs/".
		$config->catalogId."/categories/".$id;
		// dd($url);
		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken,
			'Content-Type: application/json'
		];
		$payload = json_encode($data);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		return $result;

	}

	public function destroyCategory($config, $id){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/catalogs/".
		$config->catalogId."/categories/".$id;

		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken,
		];

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		return $result;

	}

	public function storeProduct($config, $data){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/products";

		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken,
			'Content-Type: application/json'
		];
		$payload = json_encode($data);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);

		return $result;

	}

	public function addStockProduct($config, $data){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/inventory";

		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken,
			'Content-Type: application/json'
		];
		$payload = json_encode($data);
		// print_r($payload);
		// die;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);

		return $result;

	}

	public function associationProductCategory($config, $categoryId, $productId, $data){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/categories/$categoryId/products/$productId";
		// dd($url);
		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken,
			'Content-Type: application/json'
		];
		$payload = json_encode($data);

		// print_r($payload);
		// die;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);

		return $result;

	}

	public function getStock($config, $id){
		$url = "https://merchant-api.ifood.com.br/catalog/v1.0/merchants/".$config->merchantUUID."/inventory/$id";
		$ch = curl_init();
		$headers = [
			"Authorization: Bearer " . $config->accessToken
		];

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		return $result;

	}

}