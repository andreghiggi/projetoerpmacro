<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\WoocommerceUtil;
use App\Models\WoocommerceConfig;
use App\Models\WoocommercePedido;

class WoocommercePedidoController extends Controller
{
    protected $util;
    protected $endpoint = 'orders';
    public function __construct(WoocommerceUtil $util)
    {
        $this->util = $util;
    }

    public function storePedido(Request $request){
        $payload = $request->getContent();
        $pedido = json_decode($payload);

        $config = WoocommerceConfig::where('consumer_key', $request->consumer_key)
        ->first();
        // $woocommerceClient = $this->util->getConfig($config->empresa_id);

        $this->util->criaPedido($config->empresa_id, $pedido);

        return response()->json("ok", 200);
    }

    public function updatePedido(Request $request){
        $payload = $request->getContent();
        $pedido = json_decode($payload);
        $config = WoocommerceConfig::where('consumer_key', $request->consumer_key)
        ->first();
        // $woocommerceClient = $this->util->getConfig($config->empresa_id);

        $item = WoocommercePedido::where('empresa_id', $config->empresa_id)
        ->where('pedido_id', $pedido->id)->first();
        if($item){
            $item->status = $pedido->status;
            $item->save();
        }else{
            $this->util->criaPedido($config->empresa_id, $pedido);
        }
        return response()->json("ok", 200);
    }
}
