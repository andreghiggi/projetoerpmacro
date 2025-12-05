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
use App\Models\ConfigGeral;
use App\Models\TamanhoPizza;
use App\Models\ProdutoPizzaValor;
use App\Models\ConfiguracaoCardapio;
use App\Models\MarketPlaceConfig;
use App\Models\ImpressoraPedidoProduto;

class ComandaController extends Controller
{

    public function comandas(Request $request){

        $abertas = Pedido::
        where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('em_atendimento', 1)
        ->orderBy('comanda', 'asc')
        ->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        foreach($abertas as $item){
            if($item->comanda < 10){
                $item->comanda = "00$item->comanda";
            }elseif($item->comanda >= 10 && $item->comanda < 99){
                $item->comanda = "0$item->comanda";
            }else{
                $item->comanda = "$item->comanda";
            }
            $item->total = $item->total + $item->acrescimo - $item->desconto;
        }

        $fechadas = [];
        if($config && $config->numero_inicial_comanda && $config->numero_final_comanda){
            for($i=$config->numero_inicial_comanda; $i<=$config->numero_final_comanda; $i++){
                $pedido = Pedido::where('empresa_id', request()->empresa_id)
                ->where('status', 1)->where('comanda', $i)->first();

                if($pedido == null){
                    if($i < 10){
                        $com = "00$i";
                    }elseif($i >= 10 && $i < 99){
                        $com = "0$i";
                    }else{
                        $com = "$i";
                    }

                    $fechadas[] = [
                        'comanda' => $com,
                        'mesa' => ($pedido != null && $pedido->_mesa) ? $pedido->_mesa->nome : '',
                        'total' => 0,
                    ];
                }
            }
        }

        $data = [
            'abertas' => $abertas,
            'fechadas' => $fechadas
        ];

        return response()->json($abertas, 200);
    }

    public function buscarComandas(Request $request){

        $abertas = Pedido::
        where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('em_atendimento', 1)
        ->orderBy('comanda', 'asc')
        ->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        foreach($abertas as $item){
            if($item->comanda < 10){
                $item->comanda = "00$item->comanda";
            }elseif($item->comanda >= 10 && $item->comanda < 99){
                $item->comanda = "0$item->comanda";
            }else{
                $item->comanda = "$item->comanda";
            }
            $item->total = $item->total + $item->acrescimo - $item->desconto;
        }

        $fechadas = [];
        if($config && $config->numero_inicial_comanda && $config->numero_final_comanda){
            for($i=$config->numero_inicial_comanda; $i<=$config->numero_final_comanda; $i++){
                $pedido = Pedido::where('empresa_id', request()->empresa_id)
                ->where('status', 1)->where('comanda', $i)->first();

                if($pedido == null){
                    if($i < 10){
                        $com = "00$i";
                    }elseif($i >= 10 && $i < 99){
                        $com = "0$i";
                    }else{
                        $com = "$i";
                    }

                    $fechadas[] = [
                        'comanda' => $com,
                        'mesa' => ($pedido != null && $pedido->_mesa) ? $pedido->_mesa->nome : '',
                        'total' => 0,
                    ];
                }
            }
        }

        $data = [
            'abertas' => $abertas,
            'fechadas' => $fechadas
        ];

        return response()->json($data, 200);
    }

    public function find(Request $request){

        $item = Pedido::with(['itens'])
        ->findOrFail($request->comanda_id);

        $config = ConfiguracaoCardapio::where('empresa_id', $item->empresa_id)
        ->first();

        if($item->comanda < 10){
            $item->comanda = "00$item->comanda";
        }elseif($item->comanda >= 10 && $item->comanda < 99){
            $item->comanda = "0$item->comanda";
        }else{
            $item->comanda = "$item->comanda";
        }

        foreach($item->itens as $i){
            $i->adds = $i->adicionais->pluck('adicional_id')->toArray();
            $i->pizzas = $i->pizzas->pluck('produto_id')->toArray();
        }

        $item->taxa_servico = 0;
        if($config){
            $item->taxa_servico = $config->percentual_taxa_servico;
        }

        return response()->json($item, 200);
    }

    public function produtos(Request $request){

        $data = Produto::where('empresa_id', $request->empresa_id)
        ->where('cardapio', 1)
        ->with(['categoria', 'variacoes'])
        ->select('id', 'nome', 'valor_cardapio', 'categoria_id', 'valor_unitario', 'codigo_barras')
        ->get();

        return response()->json($data, 200);
    }

    public function produto(Request $request){

        $item = Produto::
        with(['adicionais', 'categoria'])
        ->findOrFail($request->produto_id);
        return response()->json($item, 200);
    }

    private function validaItemImpressao($produto_id){
        $imprime = ImpressoraPedidoProduto::where('produto_id', $produto_id)->first();
        return $imprime != null ? 0 : 1;
    }

    public function destroy($id){
        $item = Pedido::findOrFail($id);

        if($item->_mesa){
            $mesa = $item->_mesa;
            $mesa->ocupada = 0;
            $mesa->save();
        }
        try {
            foreach($item->itens as $it){
                $it->adicionais()->delete();
                $it->pizzas()->delete();
                $it->delete();
            }
            $item->notificacoes()->delete();
            $item->delete();

            return response()->json("ok", 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 403);
        }
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
            $impresso = $this->validaItemImpressao($request->produto_id);

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
                'impresso' => $impresso
            ];
            $itemPedido = ItemPedido::create($data);
            foreach($request->adicionais as $a){
                ItemAdicional::create([
                    'item_pedido_id' => $itemPedido->id, 
                    'adicional_id' => $a
                ]);
            }

            if($request->sabores && sizeof($request->sabores) >= 1){
                ItemPizzaPedido::create([
                    'item_pedido_id' => $itemPedido->id,
                    'produto_id' => $request->produto_id
                ]);
            }
            foreach($request->sabores as $s){
                ItemPizzaPedido::create([
                    'item_pedido_id' => $itemPedido->id,
                    'produto_id' => $s
                ]);
            }

            $config = ConfiguracaoCardapio::where('empresa_id', $pedido->empresa_id)
            ->first();

            $pedido->total = $pedido->itens->sum('sub_total');

            if($config && $config->percentual_taxa_servico){
                $pedido->acrescimo = $pedido->total * ($config->percentual_taxa_servico/100);
            }

            $pedido->save();
            return response()->json($pedido, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 403);
        }
    }

    public function storeItensClear(Request $request){

        $pedido = Pedido::findOrFail($request->pedido_id);

        foreach($pedido->itens as $item){
            $item->adicionais()->delete();
            $item->pizzas()->delete();
            $item->delete();
        }

        try{

            $soma = 0;
            foreach($request->itens as $item){

                $codigo_garcom = $item['codigo_operador'];

                $produto = Produto::findOrFail($item['produto_id']);
                $funcionario = null;
                if($codigo_garcom){
                    $funcionario = Funcionario::where('empresa_id', $request->empresa_id)
                    ->where('codigo', $codigo_garcom)->first();
                }

                $valorUnitario = $produto->valor_unitario;
                if($produto->valor_cardapio > 0){
                    $valorUnitario = $produto->valor_cardapio;
                }
                $impresso = $this->validaItemImpressao($produto->id);

                $data = [
                    'pedido_id' => $pedido->id,
                    'produto_id' => $item['produto_id'],
                    'observacao' => $item['observacao'],
                    'estado' => 'novo',
                    'quantidade' => $item['quantidade'],
                    'valor_unitario' => $valorUnitario,
                    'sub_total' => __convert_value_bd($item['sub_total']),
                    'tamanho_id' => $item['tamanho_id'],
                    'funcionario_id' => $funcionario ? $funcionario->id : null,
                    'impresso' => $impresso
                ];

                $soma += __convert_value_bd($item['sub_total']);
                $itemPedido = ItemPedido::create($data);
                foreach($item['adicionais'] as $a){
                    ItemAdicional::create([
                        'item_pedido_id' => $itemPedido->id, 
                        'adicional_id' => $a
                    ]);
                }

                if($item['sabores'] && sizeof($item['sabores']) >= 1){
                    ItemPizzaPedido::create([
                        'item_pedido_id' => $itemPedido->id,
                        'produto_id' => $request->produto_id
                    ]);
                }
                foreach($item['sabores'] as $s){
                    ItemPizzaPedido::create([
                        'item_pedido_id' => $itemPedido->id,
                        'produto_id' => $s
                    ]);
                }
            }

            $pedido->total = $soma;
            $pedido->save();
            return response()->json($pedido, 200);
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
            'empresa_id' => $request->empresa_id,
            'local_pedido' => 'App'
        ];

        $pedido = Pedido::create($data);

        return response()->json($pedido, 200);
    }

    public function getTamanhos(Request $request){
        $data = TamanhoPizza::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
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
