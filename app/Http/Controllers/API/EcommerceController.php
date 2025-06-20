<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carrinho;
use App\Models\EcommerceConfig;
use App\Models\PedidoEcommerce;
use App\Models\ProdutoVariacao;
use App\Models\Cliente;
use App\Utils\CorreioUtil;

class EcommerceController extends Controller
{

    protected $util = null;

    public function __construct(CorreioUtil $util){
        $this->util = $util;
    }

    public function calcularFrete(Request $request){
        $carrinho = Carrinho::findOrfail($request->carrinho_id);
        $config = EcommerceConfig::where('empresa_id', $carrinho->empresa_id)->first();
        $cepDestino = preg_replace('/[^0-9]/', '', $request->cep);

        $cepOrigem = str_replace("-", "", $config->cep);

        $somaPeso = $carrinho->somaPeso();
        $dimensoes = $carrinho->somaDimensoes();

        $data = $this->util->getValores($cepOrigem, $cepDestino, $dimensoes['altura'], $dimensoes['largura'], $dimensoes['comprimento'], $somaPeso);

        $total = $carrinho->valor_total;

        // return $data;

        return view('loja.partials.calculo_frete', compact('data', 'config', 'total'));
    }

    public function validaEmail(Request $request){
        $item = Cliente::where('empresa_id', $request->empresa_id)
        ->where('email', $request->email)
        ->first();
        if($item == null){
            return response($request->email, 200);
        }
        return response($item, 402);
    }

    public function consultaPix(Request $request){
        $pedido = PedidoEcommerce::where('transacao_id', $request->transacao_id)
        ->first();

        $config = EcommerceConfig::where('empresa_id', $pedido->empresa_id)->first();

        \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

        if($pedido){
            $payStatus = \MercadoPago\Payment::find_by_id($pedido->transacao_id);
            // $payStatus->status = "approved";
            if($payStatus->status == "approved"){
                $pedido->status_pagamento = "approved";
                $pedido->save();
            }
            return response()->json($payStatus->status, 200);

        }else{
            return response()->json("erro", 404);
        }
    }

    public function variacao(Request $request){
        $item = ProdutoVariacao::findOrfail($request->variacao_id);
        return response($item, 200);
    }
}
