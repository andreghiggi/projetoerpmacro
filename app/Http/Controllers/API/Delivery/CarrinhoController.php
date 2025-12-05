<?php

namespace App\Http\Controllers\API\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemCarrinhoDelivery;
use App\Models\CarrinhoDelivery;
use App\Models\EnderecoDelivery;
use App\Models\MarketPlaceConfig;

class CarrinhoController extends Controller
{
    public function removeItem(Request $request){
        $item = ItemCarrinhoDelivery::findOrFail($request->item_id);
        $item->adicionais()->delete();
        $item->sabores()->delete();
        $item->delete();

        $carrinho = $item->carrinho;
        $carrinho->valor_total = $carrinho->itens->sum('sub_total') + $carrinho->valor_frete - $carrinho->valor_desconto;
        $carrinho->save();
        return response()->json($carrinho, 200);
    }

    public function carrinhoCount(Request $request){
        $item = ItemCarrinhoDelivery::join('carrinho_deliveries', 'carrinho_deliveries.id', 'item_carrinho_deliveries.carrinho_id')
        ->where('carrinho_deliveries.session_cart_delivery', $request->session_cart_delivery)->count();
        return response()->json($item, 200);
    }

    public function atualizaQuantidade(Request $request){
        $item = ItemCarrinhoDelivery::findOrFail($request->item_id);
        $item->quantidade = $request->quantidade;
        $item->sub_total = $item->quantidade * $item->valor_unitario;
        $item->save();

        $carrinho = CarrinhoDelivery::findOrFail($item->carrinho_id);
        $carrinho->valor_total = $carrinho->itens->sum('sub_total') + $carrinho->valor_frete;

        $carrinho->save();
        $item->total_carrinho = $carrinho->valor_total;
        return response()->json($item, 200);
    }

    public function validaEstoque(Request $request){
        $item = ItemCarrinhoDelivery::findOrFail($request->item_id);
        if($item->servico){
            return response()->json("estoque ok", 200);
        }
        $quantidade = $request->quantidade;
        $produto = $item->produto;
        if($produto->gerenciar_estoque){
            if(!$produto->estoque || $produto->estoque->quantidade < $quantidade){
                return response()->json("Estoque insuficiente!", 401);
            }
        }
        return response()->json("estoque ok", 200);
    }

    public function carrinhoModal(Request $request){
        $carrinho = CarrinhoDelivery::where('session_cart_delivery', $request->session_cart)->first();
        $config = MarketPlaceConfig::where('empresa_id', $carrinho->empresa_id)->first();
        return view('food.partials.carrinho_modal', compact('config', 'carrinho'));
    }

    public function atualizaCarrinho(Request $request){
        $carrinho = CarrinhoDelivery::findOrFail($request->carrinho_id);
        $carrinho->observacao = $request->observacoes;
        $carrinho->tipo_pagamento = $request->forma_pagamento;
        $carrinho->cupom = $request->cupom;
        $carrinho->fone = $request->whatsapp;
        $config = MarketPlaceConfig::where('empresa_id', $carrinho->empresa_id)->first();

        $cliente = $carrinho->cliente;
        // return $cliente->id;

        if($cliente){
            $cliente->razao_social = $request->nome;
            $cliente->cpf_cnpj = $request->cpf;
            $cliente->save();
        }

        $endereco = null;
        if($request->endereco_id == null){

            if($request->bairro_id){

                $end = EnderecoDelivery::where('cliente_id', $cliente->id)
                ->where('rua', $request->endereco_rua)
                ->where('numero', $request->endereco_numero)
                ->first();
                if($end == null){

                    $dataEndereco = [
                        'rua' => $request->endereco_rua ?? '',
                        'numero'=> $request->endereco_numero ?? '',
                        'bairro_id'=> $request->bairro_id,
                        'referencia'=> $request->endereco_referencia ?? '',
                        'tipo' => $request->tipo,
                        'cep' => $request->endereco_cep ?? '',
                        'latitude' => '',
                        'longitude' => '',
                        'cliente_id' => $cliente->id,
                        'cidade_id' => $config->cidade_id,
                        'padrao' => sizeof($cliente->enderecos) == 0 ? 1 : 0
                    ];
                    $endereco = EnderecoDelivery::create($dataEndereco);
                    $carrinho->endereco_id = $endereco->id;
                    $carrinho->save();
                }
            }
        }else{

            $endereco = EnderecoDelivery::findOrFail($request->endereco_id);
            if($endereco){

                $endereco->rua = $request->endereco_rua;
                $endereco->numero = $request->endereco_numero;
                $endereco->bairro_id = $request->bairro_id;
                $endereco->referencia = $request->endereco_referencia;
                $endereco->tipo = $request->tipo;
                $endereco->cep = $request->endereco_cep;
                $endereco->save();
            }

        }

        if($endereco){
            // $carrinho->endereco = $endereco;
            $carrinho->valor_frete = $endereco->bairro->valor_entrega;
            if(($carrinho->itens->sum('sub_total') - $carrinho->desconto) >= $config->valor_entrega_gratis){
                $carrinho->valor_frete = 0;
            }
            $carrinho->valor_total = $carrinho->itens->sum('sub_total') + $carrinho->valor_frete - $carrinho->desconto;
            $carrinho->save();

        }
        $trocoPara = str_replace("R$ ", "", $request->forma_pagamento_informacao);

        $carrinho->troco_para = __convert_value_bd($trocoPara);
        $carrinho->save();

        $carrinho = CarrinhoDelivery::
        with(['endereco'])
        ->findOrFail($request->carrinho_id);
        return response()->json($carrinho, 200);

    }

    public function getEnderecos(Request $request){
        $carrinho = CarrinhoDelivery::findOrFail($request->carrinho_id);
        $cliente = $carrinho->cliente;

        if($cliente){
            return response()->json($cliente->enderecosDelivery, 200);
        }
        return response()->json([], 200);
    }

    public function comprovanteCarrinho(Request $request){
        $carrinho = CarrinhoDelivery::findOrFail($request->carrinho_id);
        $config = MarketPlaceConfig::where('empresa_id', $carrinho->empresa_id)->first();

        return view('food.partials.comprovante', compact('carrinho', 'config'));
    }

    public function findEndereco(Request $request){
        $endereco = EnderecoDelivery::findOrFail($request->endereco_id);
        return response()->json($endereco, 200);
    }

}
