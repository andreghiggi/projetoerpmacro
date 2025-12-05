<?php

namespace App\Http\Controllers\API\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarrinhoDelivery;
use App\Models\EnderecoDelivery;
use App\Models\MarketPlaceConfig;
use App\Models\Produto;
use App\Models\CupomDesconto;
use App\Models\CupomDescontoCliente;
use App\Models\ProdutoPizzaValor;
use App\Models\Cliente;
use App\Models\TamanhoPizza;
use App\Models\PedidoDelivery;
use App\Models\ItemPedidoDelivery;
use App\Models\ItemAdicionalDelivery;
use App\Models\ItemPizzaPedidoDelivery;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class HelperController extends Controller
{
    public function cupom(Request $request){
        $item = CupomDesconto::where('codigo', $request->cupom)
        ->where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->first();

        $carrinho = CarrinhoDelivery::findOrFail($request->carrinho_id);
        // return response()->json($carrinho, 404);

        if($item == null){

            $carrinho->valor_desconto = 0;
            $carrinho->cupom = '';
            $carrinho->valor_frete = 0;
            $carrinho->valor_total = $carrinho->itens->sum('sub_total') + $carrinho->valor_frete;
            $carrinho->save();
            return response()->json(['erro' => 'Cupom não encontrado'], 403);
        }
        $total = $carrinho->valor_total;

        if($carrinho->valor_desconto > 0){
            return response()->json(['erro' => "Desconto já foi aplicado"], 403);
        }

        if($total < $item->valor_minimo_pedido){
            return response()->json(['erro' => "Valor minímo para este cupom R$ " . __moeda($item->valor_minimo_pedido)], 422);
        }

        $cliente = $carrinho->cliente;
        if($cliente == null){
            return response()->json(['erro' => "Cliente não encontrado"], 403);
        }

        $cupomUsuado = CupomDescontoCliente::where('empresa_id', $request->empresa_id)
        ->where('cupom_id', $item->id)
        ->where('cliente_id', $cliente->id)
        ->first();
        if($cupomUsuado != null){
            return response()->json(['erro' => "Cupom já utilizado no pedido #$cupomUsuado->pedido_id"], 403);
        }

        $desconto = 0;
        if($item->tipo_desconto == 'valor'){
            $desconto = $item->valor;
        }else{
            $desconto = $total * ($item->valor/100);
        }

        $carrinho->valor_desconto = $desconto;
        $carrinho->cupom = $request->cupom;
        $carrinho->valor_total -= $desconto;
        $carrinho->save();

        return response()->json($desconto, 200);
    }

    public function validaFone(Request $request){
        $fone = $request->fone;
        $empresa_id = $request->empresa_id;
        $carrinho_id = $request->carrinho_id;

        $item = Cliente::where('empresa_id', $empresa_id)
        ->where('telefone', $fone)
        ->first();
        if($item != null){

            $carrinho = CarrinhoDelivery::findOrFail($carrinho_id);
            $carrinho->fone = $fone;
            $carrinho->save();
            return response()->json($item, 200);
        }
        return response()->json(null, 404);
    }

    public function clienteStore(Request $request){
        try{
            $carrinho_id = $request->carrinho_id;
            $fone = $request->fone;
            // return response()->json($fone, 403);

            $item = Cliente::create([
                'empresa_id' => $request->empresa_id,
                'razao_social' => $request->nome,
                'telefone' => $fone,
                'uid' => Str::random(30),
            ]);

            $carrinho = CarrinhoDelivery::findOrFail($carrinho_id);
            $carrinho->cliente_id = $item->id;
            $carrinho->fone = $fone;
            $carrinho->save();
            return response()->json($item, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 403);
        }
    }

    public function setEndereco(Request $request){
        $carrinho = CarrinhoDelivery::findOrFail($request->carrinho_id);
        if($request->endereco_id != 'balcao'){
            $endereco = EnderecoDelivery::findOrFail($request->endereco_id);

            $carrinho->endereco_id = $request->endereco_id;
            $carrinho->valor_frete = $endereco->bairro->valor_entrega;
            $carrinho->valor_total = $carrinho->itens->sum('sub_total') + $carrinho->valor_frete - $carrinho->valor_desconto;
            $carrinho->save();
        }else{
            $carrinho->endereco_id = null;
            $carrinho->valor_frete = 0;
            $carrinho->valor_total = $carrinho->itens->sum('sub_total') + $carrinho->valor_frete - $carrinho->valor_desconto;
            $carrinho->save();
        }

        return response()->json($carrinho, 200);

    }

    public function hashPizzas(Request $request){
        $sabores = $request->sabores;
        $produtos = [];
        foreach($sabores as $s){
            $p = Produto::findOrFail($s['id']);
            array_push($produtos, $p);
        }
        return view('food.partials.input_pizzas', compact('produtos'));
        // return response()->json($produtos, 200);
    }

    public function valorPizza(Request $request){
        $sabores = $request->sabores;
        $tamanho_id = $request->tamanho_id;
        $tamanho = TamanhoPizza::findOrFail($tamanho_id);
        $config = MarketPlaceConfig::where('empresa_id', $tamanho->empresa_id)->first();
        if($config == null){
            return response()->json("Configuração não encontrada!", 404);
        }
        $produtos = [];
        $maiorValor = 0;
        $soma = 0;
        foreach($sabores as $s){
            $produtoPizzaValor = ProdutoPizzaValor::where('produto_id', $s['id'])
            ->where('tamanho_id', $tamanho_id)->first();
            $soma += $produtoPizzaValor->valor;
            if($produtoPizzaValor->valor > $maiorValor){
                $maiorValor = $produtoPizzaValor->valor;
            }
        }
        if($config->tipo_divisao_pizza == 'divide'){
            return response()->json(($soma/sizeof($sabores)), 200);

        }else{
            return response()->json($maiorValor, 200);
        }
    }

    public function storePix(Request $request){
        sleep(4);
        return response()->json($request->all(), 200);

        $pedido = $this->createPedido($request);
        return response()->json($pedido, 200);

    }

    private function getLastNumero($empresa_id){
        $last = PedidoDelivery::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    private function createPedido($request){

        try{
            $pedido = DB::transaction(function () use ($request) {
                $carrinho = CarrinhoDelivery::findOrFail($request->carrinho_id);
                $cliente = Cliente::findOrFail($carrinho->cliente_id);
                $config = MarketPlaceConfig::where('empresa_id', $carrinho->empresa_id)->first();

                $cupom = CupomDesconto::where('codigo', $carrinho->cupom)->first();
                $pedido = PedidoDelivery::create([
                    'empresa_id' => $config->empresa_id,
                    'cliente_id' => $cliente->id,
                    'valor_total' => $carrinho->valor_total,
                    'troco_para' => $troco != $carrinho->valor_total ? $troco : null,
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'observacao' => $request->observacao ?? '',
                    'telefone' => $cliente->telefone,
                    'estado' => 'novo',
                    'endereco_id' => $carrinho->endereco_id,
                    'cupom_id' => $cupom ? $cupom->id : null,
                    'desconto' => $carrinho->valor_desconto,
                    'valor_entrega' => $carrinho->valor_frete,
                    'horario_cricao' => date('H:i'),
                    'numero_sequencial' => $this->getLastNumero($config->empresa_id)
                ]);

                if($cupom){
                    CupomDescontoCliente::create([
                        'cliente_id' => $cliente->id,
                        'empresa_id' => $pedido->empresa_id,
                        'cupom_id' => $cupom->id,
                        'pedido_id' => $pedido->id
                    ]);
                }

                foreach($carrinho->itens as $i){
                    $itemPedido = ItemPedidoDelivery::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $i->produto_id,
                        'quantidade' => $i->quantidade,
                        'observacao' => $i->observacao,
                        'tamanho_id' => $i->tamanho_id,
                        'valor_unitario' => $i->valor_unitario, 
                        'sub_total' => $i->sub_total
                    ]);
                    foreach($i->adicionais as $a){
                        ItemAdicionalDelivery::create([
                            'item_pedido_id' => $itemPedido->id,
                            'adicional_id' => $a->adicional_id
                        ]);
                    }

                    foreach($i->pizzas as $pizza){
                        ItemPizzaPedidoDelivery::create([
                            'item_pedido_id' => $itemPedido->id,
                            'produto_id' => $pizza->produto_id
                        ]);
                    }

                }
                return $pedido;
            });
            return [
                'sucesso' => 1,
                'pedido' => $pedido
            ];
        }catch(\Exception $e){
            return [
                'sucesso' => 0,
                'erro' => $e->getMessage()
            ];
        }
    }

    public function consultaPix(Request $request){
        $pedido = PedidoDelivery::where('transacao_id', $request->transacao_id)
        ->first();

        $config = MarketPlaceConfig::where('empresa_id', $pedido->empresa_id)->first();

        \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

        if($pedido){
            $payStatus = \MercadoPago\Payment::find_by_id($pedido->transacao_id);
            // $payStatus->status = "approved";
            if($payStatus->status == "approved"){
                $pedido->status_pagamento = "approved";
                $pedido->estado = "aprovado";
                $pedido->save();
            }
            return response()->json($payStatus->status, 200);

        }else{
            return response()->json("erro", 404);
        }
    }

    public function consultaPedido(Request $request){
        $pedido = PedidoDelivery::where('id', $request->pedido_id)
        ->first();
        return response()->json($pedido->estado, 200);
    }

}
