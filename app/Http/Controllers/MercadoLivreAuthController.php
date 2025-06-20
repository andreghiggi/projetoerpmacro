<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MercadoLivreConfig;
use App\Utils\MercadoLivreUtil;

class MercadoLivreAuthController extends Controller
{

    protected $utilMercadoLivre;
    public function __construct(MercadoLivreUtil $utilMercadoLivre)
    {
        $this->utilMercadoLivre = $utilMercadoLivre;
    }

    public function getCode(Request $request){
        $config = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
        ->first();
        if(!$config){
            session()->flash("flash_error", "Configure as variaveis do mercado livre");
            return redirect()->route('mercado-livre-config.index');
        }
        $uri = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id="
        . $config->client_id ."&redirect_uri="
        . $config->url ."/mercado-livre-auth-code&state=$12345";
        return redirect($uri);
    }

    public function authCode(Request $request){

        $config = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $curl = curl_init();
        $payload = json_encode([
            "grant_type" => "authorization_code",
            "client_id" => $config->client_id,
            "client_secret" => $config->client_secret,
            "accept" => "application/json",
            "content-type" => "application/x-www-form-urlencoded",
            "redirect_uri" => $config->url."/mercado-livre-auth-code",
            "code" => $request->code
        ]);

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/oauth/token");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        $res = curl_exec($curl);
        $retorno = json_decode($res);
        // dd($res);

        if(isset($retorno->access_token)){

            if($config){
                $config->code = $request->code;
                $config->access_token = $retorno->access_token;
                $config->refresh_token = $retorno->refresh_token;
                $config->user_id = $retorno->user_id;
                $config->token_expira = strtotime(date('Y-m-d H:i:s')) + $retorno->expires_in;
                $config->save();

            }
            session()->flash("flash_success", "Token armazenado!");
        }else{
            session()->flash("flash_error", "Algo deu errado: " . $retorno->message);
        }

        return redirect()->route('mercado-livre-config.index');
    }

    public function refreshToken(Request $request){

        $retorno = $this->utilMercadoLivre->refreshToken($request->empresa_id);
        if($retorno == 'token valido!'){
            return redirect()->back();
        }
        dd($retorno);
    }

    public function getUsers(Request $request){
        $config = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/users/me");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ]);

        $res = curl_exec($curl);
        $retorno = json_decode($res);
        dd($res);
    }

    public function authToken(Request $request){

    }
}
