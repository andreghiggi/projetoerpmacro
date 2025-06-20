<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaMercadoLivre;
use App\Models\MercadoLivreConfig;
use App\Utils\MercadoLivreUtil;

class MercadoLivreController extends Controller
{

    protected $util;
    public function __construct(MercadoLivreUtil $util)
    {
        $this->util = $util;
    }

    public function getCategorias(Request $request){
        $data = CategoriaMercadoLivre::
        where('nome', 'like', "%$request->pesquisa%")
        ->get();

        return response()->json($data, 200);
    }

    public function getTiposPublicacao(){
        $config = MercadoLivreConfig::where('empresa_id', request()->empresa_id)
        ->first();
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/sites/MLB/listing_types");
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
        return response()->json($retorno, 200);

    }

    public function notification(Request $request){
        //webhook mercado livre
        $config = MercadoLivreConfig::where('user_id', $request->user_id)
        ->first();
        $retorno = $this->util->getNotification($config, $request);
        // file_put_contents('ml'.rand(0,123123).'.txt', $retorno);
    }
}
