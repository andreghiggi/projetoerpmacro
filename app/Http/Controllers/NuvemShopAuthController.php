<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NuvemShopConfig;

class NuvemShopAuthController extends Controller
{
    public function index(Request $request){
        $config = NuvemShopConfig::where('empresa_id', $request->empresa_id)->first();
        if($config == null){
            session()->flash("flash_warning", 'Defina a configuração!');
            return redirect()->route('nuvem-shop-config.index');
        }

        $auth = new \TiendaNube\Auth($config->client_id, $config->client_secret);
        $url = $auth->login_url_brazil();
        return redirect($url);
    }

    public function code(Request $request){
        $config = NuvemShopConfig::where('empresa_id', $request->empresa_id)->first();
        $code = $request->code;
        $auth = new \TiendaNube\Auth($config->client_id, $config->client_secret);
        $store_info = $auth->request_access_token($code);

        $store_info['email'] = $config->email;

        session(['store_info' => $store_info]);
        session()->flash("flash_success", "Autenticação realizada, access_token: " . $store_info['access_token'] . " store id: " . $store_info['store_id']);
        return redirect()->route('nuvem-shop-pedidos.index');
    }

}
