<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Produto;
use App\Models\Funcionario;
use App\Models\ItemAdicional;
use App\Models\ItemPizzaPedido;
use App\Models\TamanhoPizza;
use App\Models\ProdutoPizzaValor;
use App\Models\MarketPlaceConfig;

class ComandaController extends Controller
{

    public function comandas(Request $request){

        $data = Pedido::
        where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('em_atendimento', 1)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($data, 200);
    }

    public function find(Request $request){

        $item = Pedido::with(['itens'])
        ->findOrFail($request->comanda_id);

        return response()->json($item, 200);
    }

    public function produtos(Request $request){

        $data = Produto::where('empresa_id', $request->empresa_id)
        ->where('cardapio', 1)
        ->with('categoria')
        ->select('id', 'nome', 'valor_cardapio', 'categoria_id', 'variacoes')
        ->get();

        return response()->json($data, 200);
    }

    public function produto(Request $request){

        $item = Produto::
        with(['adicionais', 'categoria'])
        ->findOrFail($request->produto_id);
        return response()->json($item, 200);
    }

    public function storeItem(Request $request){
        $produto = Produto::findOrFail($request->produto_id);
        $pedido = Pedido::findOrFail($request->pedido_id);
        try{
            $codigo_garcom = $request->codigo_operador;
            $funcionario = null;
            if($codigo_garcom){
                $funcionario = Funcionario::where('empresa_id', $request->empresa_id)
                ->where('codigo', $codigo_garcom)->first();
            }

            $valorUnitario = $produto->valor_unitario;
            if($produto->valor_cardapio > 0){
               $valorUnitario = $produto->valor_cardapio;
            }
            $data = [
                'pedido_id' => $request->pedido_id,
                'produto_id' => $request->produto_id,
                'observacao' => $request->observacao,
                'estado' => 'novo',
                'quantidade' => $request->quantidade,
                'valor_unitario' => $valorUnitario,
                'sub_total' => __convert_value_bd($request->sub_total),
                'tamanho_id' => $request->tamanho_id,
                'funcionario_id' => $funcionario ? $funcionario->id : null,
            ];
            $itemPedido = ItemPedido::create($data);
            foreach($request->adicionais as $a){
                ItemAdicional::create([
                    'item_pedido_id' => $itemPedido->id, 
                    'adicional_id' => $a
                ]);
            }

            foreach($request->sabores as $s){
                ItemPizzaPedido::create([
                    'item_pedido_id' => $itemPedido->id,
                    'produto_id' => $s
                ]);
            }

            $pedido->total = $pedido->itens->sum('sub_total');
            $pedido->save();
            return response()->json($itemPedido, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 403);
        }
    }

    public function removeItem(Request $request){
        $item = ItemPedido::findOrfail($request->item_id);
        try{
            $pedido = $item->pedido;
            $item->adicionais()->delete();
            $item->pizzas()->delete();
            $item->delete();
            $pedido->total = $pedido->itens->sum('sub_total');
            $pedido->save();

            return response()->json("Item removido!", 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 403);
        }
    }

    public function fecharComanda(Request $request){
        $pedido = Pedido::findOrFail($request->comanda_id);
        $pedido->em_atendimento = 0;
        $pedido->save();
        return response()->json($pedido, 200);
    }

    public function openComanda(Request $request){
        $cliente_id = $request->cliente_id;
        $clienteNome = $request->nome;
        $clienteFone = $request->telefone;
        $comanda = $request->comanda;
        $mesa = $request->mesa;

        $item = Pedido::where('status', 1)
        ->where('empresa_id', $request->empresa_id)
        ->where('comanda', $comanda)
        ->first();

        if($item != null){
            return response()->json('Comanda já está aberta', 403);
        }

        $data = [
            'cliente_id' => $cliente_id,
            'cliente_nome' => $clienteNome,
            'cliente_fone' => $clienteFone,
            'comanda' => $comanda,
            'mesa' => $mesa,
            'total' => 0,
            'empresa_id' => $request->empresa_id
        ];

        $pedido = Pedido::create($data);

        return response()->json($pedido, 200);
    }

    public function getTamanhos(Request $request){
        $data = TamanhoPizza::where('empresa_id', $request->empresa_id)
        ->get();

        return response()->json($data, 200);        
    }

    public function getSabores(Request $request){
        $sabores = Produto::orderBy('produtos.nome', 'desc')
        ->select('produtos.*')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->where('produtos.cardapio',1)
        ->where('produtos.id', '!=', $request->sabor_principal)
        ->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
        ->where('categoria_produtos.tipo_pizza', 1)
        ->with(['pizzaValores'])
        ->get();

        foreach($sabores as $p){
            $produtoPizzaValor = ProdutoPizzaValor::where('produto_id', $p->id)
            ->where('tamanho_id', $request->tamanho_id)->first();
            if($produtoPizzaValor){
                $p->valor = $produtoPizzaValor->valor;
            }
            $p->checked = false;
        }

        $tamanho = TamanhoPizza::findOrFail($request->tamanho_id);

        $data = [
            'tamanho' => $tamanho,
            'sabores' => $sabores
        ];
        return response()->json($data, 200);
    }

    public function valorPizza(Request $request){
        $tamanho_id = $request->tamanho_id;
        $empresa_id = $request->empresa_id;
        $sabores = $request->sabores;
        $soma = 0;
        $maiorValor = 0;

        $config = MarketPlaceConfig::where('empresa_id', $empresa_id)->first();
        foreach($sabores as $s){
            $produtoPizzaValor = ProdutoPizzaValor::where('produto_id', $s)
            ->where('tamanho_id', $tamanho_id)->first();
            if($produtoPizzaValor){
                $soma += $produtoPizzaValor->valor;
                if($produtoPizzaValor->valor > $maiorValor){
                    $maiorValor = $produtoPizzaValor->valor;
                }
            }
        }
        $valor = $maiorValor;
        if($config->tipo_divisao_pizza == 'divide'){
            $valor = $soma/sizeof($sabores);
        }

        $valor = number_format($valor, 2);
        return response()->json($valor, 200);   

    }

}
