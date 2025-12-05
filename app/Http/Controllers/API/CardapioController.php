<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaProduto;
use App\Models\CarrosselCardapio;
use App\Models\ConfiguracaoCardapio;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\ItemPizzaPedido;
use App\Models\ItemAdicional;
use App\Models\TamanhoPizza;
use App\Models\NotificaoCardapio;
use App\Models\ImpressoraPedidoProduto;
use App\Models\Nfe;

class CardapioController extends Controller
{
    public function setConfig(Request $request){
        $item = ConfiguracaoCardapio::where('api_token', $request->token)
        ->first();

        if($item == null){
            return response()->json("Configuração não encontrada!", 401);
        }
        return response()->json($item, 200);
    }

    public function categorias(Request $request){
        $data = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->where('categoria_id', null)
        ->where('cardapio', 1)->get();
        return response()->json($data, 200);
    }

    public function destaques(Request $request){
        $data = CarrosselCardapio::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->get();

        return response()->json($data, 200);
    }

    public function config(Request $request){
        $item = ConfiguracaoCardapio::where('empresa_id', $request->empresa_id)

        ->first();
        return response()->json($item, 200);
    }

    public function categoria(Request $request, $id){
        $item = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->where('id', $id)
        ->with('produtosCardapio')
        ->first();
        return response()->json($item, 200);
    }

    public function produto(Request $request, $id){
        $item = Produto::where('empresa_id', $request->empresa_id)
        ->where('id', $id)
        ->with(['adicionais', 'ingredientes'])
        ->first();
        return response()->json($item, 200);
    }

    public function ingredientes(Request $request){
        $produtos = [];
        foreach($request->data as $c){
            $produto = Produto::findOrFail($c['id']);
            array_push($produtos, $produto);
        }

        $ingredientes = [];
        $ingredientesTemp = [];
        foreach($produtos as $p){
            foreach($p->ingredientes as $i){
                if(!in_array($i->ingrediente, $ingredientesTemp)){
                    array_push($ingredientesTemp, $i->ingrediente);
                    array_push($ingredientes, $i);
                }
            }
        }
        return response()->json($ingredientes, 200);
    }

    private function validaItemImpressao($produto_id){

        $imprime = ImpressoraPedidoProduto::where('produto_id', $produto_id)->first();
        return $imprime != null ? 0 : 1;
    }

    public function storePedido(Request $request){

        $item = Pedido::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('em_atendimento', 1)
        ->where('mesa', $request->mesa)
        ->first();

        if($item == null){
            return response()->json("nada encontrado", 404);
        }

        //salvando itens

        foreach($request->carrinho as $cartItem){
            $cartItem = (object)$cartItem;

            $observacao = $cartItem->observacao;
            if($cartItem->remover_ingredientes != ''){
                $observacao .= ' - remover: '. $cartItem->remover_ingredientes;
            }
            $estado = 'novo';
            $prod = Produto::findOrFail($cartItem->produto_id);
            if($prod->tempo_preparo){
                $estado = 'pendente';
            }
            $impresso = $this->validaItemImpressao($cartItem->produto_id);
            $itemPedido = ItemPedido::create([
                'pedido_id' => $item->id,
                'produto_id' => $cartItem->produto_id,
                'observacao' => $observacao,
                'quantidade' => $cartItem->quantidade,
                'valor_unitario' => $cartItem->valor_unitario > 0 ? $cartItem->valor_unitario : $cartItem->produto_valor,
                'sub_total' => $cartItem->sub_total,
                'ponto_carne' => $cartItem->ponto_carne,
                'tamanho_id' => $cartItem->tamanho_id,
                'estado' => $estado,
                'impresso' => $impresso
            ]);

            foreach($cartItem->sabores as $s){
                $s = (object)$s;
                ItemPizzaPedido::create([
                    'item_pedido_id' => $itemPedido->id,
                    'produto_id' => $s->id
                ]);
            }

            foreach($cartItem->adicionais as $a){
                $a = (object)$a;
                if($a){

                    $dataItemAdicional = [
                        'item_pedido_id' => $itemPedido->id,
                        'adicional_id' => $a->adicional_id,
                    ];
                    ItemAdicional::create($dataItemAdicional);
                }
            }
        }

        $item->sumTotal();
        return response()->json($item, 200);
    }

    public function storeMesa(Request $request){

        $item = Pedido::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('em_atendimento', 1)
        ->where('mesa', $request->mesa)
        ->first();

        // dd($request->all());

        if($item != null){
            return response()->json("esta mesa já se encontra aberta", 401);
        }

        $item = Pedido::create([
            'empresa_id' => $request->empresa_id,
            'cliente_nome' => $request->nome,
            'cliente_fone' => $request->telefone,
            'mesa' => $request->mesa,
            'total' => 0,
            'comanda' => 'M'.$request->mesa,
            'local_pedido' => 'App'
        ]);
        return response()->json($item, 200);
    }

    public function conta(Request $request){
        $item = Pedido::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('em_atendimento', 1)
        ->where('mesa', $request->mesa)
        ->with('itens')
        ->first();

        foreach($item->itens as $it){
            if($it->produto->tempo_preparo > 0){
                $it->tempo_preparo = $it->tempoPreparoRestante();
            }
        }
        
        return response()->json($item, 200);
    }

    public function chamarGarcom(Request $request){
        $item = Pedido::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('mesa', $request->mesa)
        ->first();

        if($item == null){
            return response()->json("nada encontrado", 404);
        }

        $notificacao = NotificaoCardapio::create([
            'empresa_id' => $request->empresa_id,
            'mesa' => $request->mesa,
            'tipo' => 'garcom'
        ]);

        return response()->json($notificacao, 200);

    }

    public function tiposDePagamento(){
        $data = Nfe::tiposPagamento();
        $tipos = [];
        foreach($data as $key => $t){
            array_push($tipos, [
                'codigo' => $key,
                'nome' => $t
            ]);
        }
        return response()->json($tipos, 200);
    }

    public function finalizarConta(Request $request){
        $item = Pedido::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('em_atendimento', 1)
        ->where('mesa', $request->mesa)
        ->first();

        if($item == null){
            return response()->json("nada encontrado", 404);
        }

        $notificacao = NotificaoCardapio::create([
            'empresa_id' => $request->empresa_id,
            'mesa' => $request->mesa,
            'pedido_id' => $item->id,
            'tipo' => 'fechar_mesa',
            'observacao' => $request->observacao,
            'avaliacao' => $request->avaliacao,
            'tipo_pagamento' => $request->tipo_pagamento,
        ]);

        $item->em_atendimento = 0;
        $item->save();

        return response()->json($notificacao, 200);
    }

    public function emAtendimento(Request $request){
        $item = Pedido::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('em_atendimento', 1)
        ->where('mesa', $request->mesa)
        ->first();

        if($item == null){
            return response()->json("nada encontrado", 404);
        }

        return response()->json($item, 200);
    }

    public function pesquisa(Request $request){
        $data = Produto::where('empresa_id', $request->empresa_id)
        ->where('nome', 'like', "%$request->pesquisa%")
        ->where('cardapio', 1)
        ->get();

        return response()->json($data, 200);
    }

    public function tamanhosPizza(Request $request){
        $data = TamanhoPizza::where('empresa_id', $request->empresa_id)
        ->get();

        return response()->json($data, 200);
    }

}
