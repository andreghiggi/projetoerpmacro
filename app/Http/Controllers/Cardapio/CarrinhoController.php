<?php

namespace App\Http\Controllers\Cardapio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConfiguracaoCardapio;
use App\Models\CarrinhoCardapio;
use App\Models\Mesa;
use App\Models\ItemCarrinhoCardapio;
use App\Models\NotificaoCardapio;
use App\Models\ItemPizzaCarrinhoCardapio;
use App\Models\ItemCarrinhoAdicionalCardapio;
use App\Models\Produto;
use App\Models\ItemAdicional;
use App\Models\ItemPizzaPedido;
use App\Models\ItemPedido;
use App\Models\CategoriaProduto;
use App\Models\ImpressoraPedidoProduto;
use App\Models\Pedido;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CarrinhoController extends Controller
{
    public function __construct(){
        session_start();
    }

    private function _getCarrinho(){
        $data = [];
        if(isset($_SESSION["session_cart_cardapio"])){
            $data = CarrinhoCardapio::where('session_cart_cardapio', $_SESSION["session_cart_cardapio"])
            ->where('session_cart_user', $_SESSION["session_cart_user"])
            ->first();
        }
        return $data;
    }

    public function index(Request $request){

        $carrinho = $this->_getCarrinho();
        $config = ConfiguracaoCardapio::findOrfail($request->config_id);

        $mesa = Mesa::where('empresa_id', $config->empresa_id)->where('hash', $request->link)
        ->first();

        // if($mesa->ocupada == 1){
        //     $pedido = Pedido::where('empresa_id', $mesa->empresa_id)
        //     ->where('status', 1)->orderBy('id', 'desc')
        //     ->first();

        //     $_SESSION['session_cart_cardapio'] = $pedido->session_cart_cardapio;
        //     // dd($pedido);
        //     $carrinho->session_cart_cardapio = $pedido->session_cart_cardapio;
        //     $carrinho->save();
        //     // session()->flash("flash_error", "Esta mesa já esta ocupada!");
        //     // return -1;
        // }

        if($mesa == null){
            session()->flash("flash_error", "Mesa não encontrada!");
            return redirect()->back();
        }

        $session_cart_user = null;
        if(isset($_SESSION["session_cart_user"])){
            $session_cart_user = $_SESSION["session_cart_user"];
        }

        $session_nome_cardapio = null;
        if(isset($_SESSION["session_nome_cardapio"])){
            $session_nome_cardapio = $_SESSION["session_nome_cardapio"];
        }

        $item = null;
        if(isset($_SESSION["session_cart_cardapio"])){
            $item = CarrinhoCardapio::where('session_cart_cardapio', $_SESSION["session_cart_cardapio"])
            ->first();
        }

        $categorias = CategoriaProduto::where('cardapio', 1)
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();

        $notSearch = true;
        $pedido = null;

        if($carrinho){
            $pedido = Pedido::where('empresa_id', $carrinho->empresa_id)
            ->where('status', 1)->where('session_cart_cardapio', $carrinho->session_cart_cardapio)
            ->join('item_pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
            ->where('item_pedidos.nome_cardapio', $session_nome_cardapio)
            ->first();
        }

        $travaLimite = 0;
        if($config->limite_pessoas_qr_code && $carrinho){
            $contPedidos = Pedido::where('empresa_id', $carrinho->empresa_id)
            ->where('status', 1)->where('session_cart_cardapio', $carrinho->session_cart_cardapio)
            ->count();

            if($contPedidos >= $config->limite_pessoas_qr_code){
                $travaLimite = 1;
            }
        }

        // echo $pedido;
        // die;

        $notInfoHeader = 1;
        $link = $request->link;

        return view('qr_code_cardapio.carrinho', compact('config', 'categorias', 'carrinho', 'notSearch', 'notInfoHeader', 'link', 
            'pedido', 'travaLimite', 'mesa'));
    }


    public function adicionar(Request $request){
        // dd($request->all());

        $config = ConfiguracaoCardapio::findOrfail($request->config_id);
        $quantidade = (float)__convert_value_bd($request->quantidade);
        if($request->produto_id){
            $produto_id = $request->produto_id;
        }else{
            $produto_id = $request->pizza_id[0];
        }

        $produto = Produto::findOrFail($produto_id);

        if($produto->gerenciar_estoque){

            if(!$produto->estoque || $produto->estoque->quantidade < $quantidade){
                session()->flash("flash_error", "Estoque insuficiente!");
                return redirect()->back();
            }
        }

        try{
            $carrinho = DB::transaction(function () use ($request, $config) {

                if(!isset($_SESSION["session_cart_cardapio"])){
                    $session_cart_cardapio = Str::random(30);
                    $_SESSION['session_cart_cardapio'] = $session_cart_cardapio;
                }else{
                    $session_cart_cardapio = $_SESSION['session_cart_cardapio'];
                }

                if(!isset($_SESSION["session_cart_user"])){
                    $session_cart_user = Str::random(30);
                    $_SESSION['session_cart_user'] = $session_cart_user;
                }else{
                    $session_cart_user = $_SESSION['session_cart_user'];
                }

                if($request->produto_id){
                    $produto_id = $request->produto_id;
                }else{
                    $produto_id = $request->pizza_id[0];
                }

                $adicionarSaborUnico = 0;
                if(isset($request->tamanho_id)){
                    if(!isset($request->pizza_id)){
                        $adicionarSaborUnico = 1;
                    }
                }

                $carrinho = CarrinhoCardapio::where('session_cart_cardapio', $session_cart_cardapio)
                ->where('session_cart_user', $session_cart_user)
                ->first();

                // dd($carrinho);

                $quantidade = (float)__convert_value_bd($request->quantidade);

                $itemCarrinho = null;

                if(!isset($_SESSION["session_cart_user"])){
                    $session_cart_user = Str::random(30);
                    $_SESSION['session_cart_user'] = $session_cart_user;
                }else{
                    $session_cart_user = $_SESSION['session_cart_user'];
                }

                if($carrinho == null){
                    //novo carrinho

                    $carrinho = CarrinhoCardapio::create([
                        'empresa_id' => $config->empresa_id,
                        'estado' => 'pendente',
                        'valor_total' => $request->sub_total,
                        'endereco_id' => null,
                        'valor_frete' => 0,
                        'session_cart_cardapio' => $session_cart_cardapio,
                        'session_cart_user' => $session_cart_user,
                    ]);
                    $itemCarrinho = ItemCarrinhoCardapio::create([
                        'carrinho_id' => $carrinho->id,
                        'produto_id' => $produto_id,
                        'quantidade' => $quantidade,
                        'valor_unitario' => $request->sub_total/$quantidade,
                        'sub_total' => $request->sub_total,
                        'observacao' => $request->observacao ?? '',
                        'tamanho_id' => isset($request->tamanho_id) ? $request->tamanho_id : null
                    ]);
                    session()->flash("flash_success", "Produto adicionado ao carrinho!");

                }else{

                    $itemCarrinho = ItemCarrinhoCardapio::create([
                        'carrinho_id' => $carrinho->id,
                        'produto_id' => $produto_id,
                        'quantidade' => $quantidade,
                        'valor_unitario' => $request->sub_total/$quantidade,
                        'sub_total' => $request->sub_total,
                        'observacao' => $request->observacao ?? '',
                        'tamanho_id' => isset($request->tamanho_id) ? $request->tamanho_id : null
                    ]);

                    session()->flash("flash_success", "Produto adicionado ao carrinho!");
                }

                if($request->adicional){
                    for($i=0; $i<sizeof($request->adicional); $i++){

                        ItemCarrinhoAdicionalCardapio::create([
                            'item_carrinho_id' => $itemCarrinho->id, 
                            'adicional_id' => $request->adicional[$i]
                        ]);
                    }
                }
                if(isset($request->pizza_id)){

                    for($i=0; $i<sizeof($request->pizza_id); $i++){
                        ItemPizzaCarrinhoCardapio::create([
                            'item_carrinho_id' => $itemCarrinho->id, 
                            'produto_id' => $request->pizza_id[$i]
                        ]);
                    }
                }

                if($adicionarSaborUnico){
                    ItemPizzaCarrinhoCardapio::create([
                        'item_carrinho_id' => $itemCarrinho->id, 
                        'produto_id' => $produto_id
                    ]);
                }
                return $carrinho;
            });
}catch(\Exception $e){
            // echo $e->getMessage();
            // die;
    session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
    return redirect()->back();

}
$this->_atualizaValorCarrinho($carrinho->id);

return redirect()->route('cardapio.carrinho', 'link='.$request->link);
}

private function _atualizaValorCarrinho($carrinho_id){
    $item = CarrinhoCardapio::findOrfail($carrinho_id);
    $item->valor_total = $item->itens->sum('sub_total') + $item->valor_frete;

    $item->save();
}

public function enviarPedido(Request $request){
    $config = ConfiguracaoCardapio::findOrfail($request->config_id);
    $mesa = Mesa::where('empresa_id', $config->empresa_id)->where('hash', $request->link)
    ->first();

    // dd($mesa);

    if(isset($request->telefone)){

        $verifica = Pedido::where('empresa_id', $mesa->empresa_id)
        ->where('cliente_fone', $request->telefone)
        ->where('status', 1)
        ->first();

        if($verifica != null){
            session()->flash("flash_error", "Você já fez pedido em outra mesa!");
            return redirect()->back();
        }

        $_SESSION['session_telefone_cardapio'] = $request->telefone;
    }

    if(isset($request->nome)){
        $_SESSION['session_nome_cardapio'] = $request->nome;
    }

    // verifica ja esta em outra mesa

    if(!isset($_SESSION["session_cart_user"])){
        $session_cart_user = Str::random(30);
        $_SESSION['session_cart_user'] = $session_cart_user;
    }else{
        $session_cart_user = $_SESSION['session_cart_user'];
    }

    $carrinho = $this->_getCarrinho();

    if($mesa->ocupada == 1){
        $pedido = Pedido::where('empresa_id', $mesa->empresa_id)
        ->where('status', 1)->orderBy('id', 'desc')
        ->where('mesa_id', $mesa->id)
        ->first();

        $_SESSION['session_cart_cardapio'] = $pedido->session_cart_cardapio;
        // dd($carrinho);
        if($carrinho){
            $carrinho->session_cart_cardapio = $pedido->session_cart_cardapio;
            $carrinho->save();
        }
            // session()->flash("flash_error", "Esta mesa já esta ocupada!");
            // return -1;
    }

    $pedido = DB::transaction(function () use ($request, $config, $mesa, $session_cart_user, $carrinho) {

        $pedido = Pedido::where('empresa_id', $carrinho->empresa_id)
        ->where('status', 1)->where('session_cart_cardapio', $carrinho->session_cart_cardapio)
        ->first();

        if($pedido == null){
            $_SESSION['session_nome_cardapio'] = $request->nome;
            $_SESSION['session_telefone_cardapio'] = $request->telefone;

            $pedido = Pedido::create([
                'empresa_id' => $config->empresa_id,
                'cliente_nome' => $request->nome,
                'cliente_fone' => $request->telefone,
                'mesa_id' => $mesa->id,
                'confirma_mesa' => $config->confirma_mesa,
                'total' => $carrinho->valor_total,
                'session_cart_cardapio' => $carrinho->session_cart_cardapio,
                'session_cart_user' => $session_cart_user,
            ]);
            $carrinho->cliente_nome = $request->nome;
            $carrinho->save();

            NotificaoCardapio::create([
                'empresa_id' => $config->empresa_id,
                'mesa' => $pedido->_mesa->nome,
                'pedido_id' => $pedido->id,
                'tipo' => 'fechar_mesa',
                'observacao' => 'Abrindo mesa',
                'avaliacao' => '',
                'tipo_pagamento' => '',
            ]);

            $mesa->ocupada = 1;
            $mesa->save();

        }else{
            $pedido->total += $carrinho->valor_total;
            $pedido->save();
        }

        foreach($carrinho->itens as $i){
            $impresso = $this->validaItemImpressao($i->produto_id);

            $itemPedido = ItemPedido::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $i->produto_id,
                'observacao' => $i->observacao,
                'estado' => 'novo',
                'quantidade' => $i->quantidade,
                'valor_unitario' => $i->valor_unitario,
                'sub_total' => $i->sub_total, 
                'tamanho_id' => $i->tamanho_id,
                'impresso' => $impresso,
                'nome_cardapio' => $_SESSION['session_nome_cardapio'],
                'telefone_cardapio' => $_SESSION['session_telefone_cardapio'],
            ]);

            foreach($i->adicionais as $a){
                $itemAdicional = ItemAdicional::create([
                    'item_pedido_id' => $itemPedido->id,
                    'adicional_id' => $a->adicional_id
                ]);
            }

            foreach($i->pizzas as $pizza){
                ItemPizzaPedido::create([
                    'item_pedido_id' => $itemPedido->id,
                    'produto_id' => $pizza->produto_id
                ]);
            }
        }

        foreach($carrinho->itens as $it){
            $it->adicionais()->delete();
            $it->sabores()->delete();
            $it->delete();
        }
        $carrinho->valor_total = 0;
        $carrinho->save();
        return $pedido;
    });

    if(isset($pedido->id)){
        session()->flash("flash_success", "Pedido realizado!");
    }
    return redirect()->route('cardapio.index', ['link='.$request->link]);

}

private function validaItemImpressao($produto_id){
    $imprime = ImpressoraPedidoProduto::where('produto_id', $produto_id)->first();
    return $imprime != null ? 0 : 1;
}

public function pedirFechar(Request $request){

    $config = ConfiguracaoCardapio::findOrfail($request->config_id);
    $carrinho = $this->_getCarrinho();

    $pedido = Pedido::where('empresa_id', $carrinho->empresa_id)
    ->where('status', 1)->where('session_cart_cardapio', $carrinho->session_cart_cardapio)
    ->first();

    $pedido->em_atendimento = 0;
    $pedido->save();

    NotificaoCardapio::create([
        'empresa_id' => $config->empresa_id,
        'mesa' => $pedido->_mesa->nome,
        'pedido_id' => $pedido->id,
        'tipo' => 'fechar_mesa',
        'observacao' => $request->observacao,
        'avaliacao' => $request->avaliacao,
        'tipo_pagamento' => $request->tipo_pagamento,
    ]);

    $_SESSION["session_cart_cardapio"] = null;
    $_SESSION["session_nome_cardapio"] = null;
    $_SESSION["session_telefone_cardapio"] = null;

    session()->flash("flash_success", "Mesa finalizada!");
    return redirect()->route('cardapio.index', ['link='.$request->link]);

}

}
