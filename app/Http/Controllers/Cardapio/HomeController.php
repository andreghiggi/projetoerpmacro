<?php

namespace App\Http\Controllers\Cardapio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConfiguracaoCardapio;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\Nfce;
use App\Models\Mesa;
use App\Models\ConfigGeral;
use App\Models\ItemPedido;
use App\Models\CarrinhoCardapio;
use App\Models\TamanhoPizza;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __construct(){
        session_start();
    }

    private function _validaHash($config){
        $categorias = CategoriaProduto::where('cardapio', 1)
        ->where('empresa_id', $config->empresa_id)
        ->orderBy('nome', 'asc')
        ->where('hash_delivery', null)
        ->where('status', 1)
        ->get();

        foreach($categorias as $c){
            $c->hash_delivery = Str::random(50);
            $c->save();
        }

        $produtos = Produto::where('empresa_id', $config->empresa_id)
        ->where('status', 1)
        ->where('cardapio', 1)
        ->where('hash_delivery', null)
        ->get();

        foreach($produtos as $p){
            $p->hash_delivery = Str::random(50);
            $p->save();
        }

    }

    private function getCategorias($empresa_id){
        $categorias = [];
        $categoriasServico = [];
        $categorias = CategoriaProduto::where('cardapio', 1)
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->where('empresa_id', $empresa_id)->get();

        return $categorias;
    }

    public function index(Request $request){

        $config = ConfiguracaoCardapio::findOrfail($request->config_id);
        // dd($config);
        $this->_validaHash($config);

        $categorias = $this->getCategorias($config->empresa_id);

        $produtosEmDestaque = $this->produtosEmDestaque($config->empresa_id);
        $carrinho = $this->_getCarrinho();
        $link = $request->link;
        $mesa = Mesa::where('hash', $link)->first();

        return view('qr_code_cardapio.index', compact('config', 'categorias', 'produtosEmDestaque', 'carrinho', 'link', 'mesa'));
    }

    public function pesquisa(Request $request){

        $config = ConfiguracaoCardapio::findOrfail($request->config_id);
        // dd($config);
        $this->_validaHash($config);

        $categorias = $this->getCategorias($config->empresa_id);
        $pesquisa = $request->pesquisa;

        $produtos = Produto::where('produtos.empresa_id', $config->empresa_id)
        ->select('produtos.*')
        ->where('produtos.status', 1)
        ->where('produtos.cardapio', 1)
        ->when(!empty($pesquisa), function ($query) use ($pesquisa) {
            return $query->where('produtos.nome', 'like', "%$pesquisa%");
        })
        ->get();
        $carrinho = $this->_getCarrinho();
        $link = $request->link;
        return view('qr_code_cardapio.pesquisa', compact('config', 'categorias', 'carrinho', 'link', 'produtos'));
    }

    private function produtosEmDestaque($empresa_id){

        $data = Produto::where('empresa_id', $empresa_id)
        ->where('destaque_cardapio', 1)
        ->where('status', 1)
        ->where('cardapio', 1)->get();

        $produtos = [];
        foreach($data as $item){
            if($item->gerenciar_estoque){

                if($item->estoque && $item->estoque->quantidade > 0){
                    array_push($produtos, $item);
                }
            }else{
                array_push($produtos, $item);
            }
        }
        return $produtos;
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

    public function ofertas(Request $request){

        $config = ConfiguracaoCardapio::findOrfail($request->config_id);
        $categorias = $this->getCategorias($config->empresa_id);

        $produtos = Produto::where('empresa_id', $config->empresa_id)
        ->where('status', 1)
        ->where('oferta_cardapio', 1)
        ->where('cardapio', 1)->get();

        $tamanho = TamanhoPizza::where('empresa_id', $config->empresa_id)
        ->orderBy('maximo_sabores', 'desc')->first();
        $maximo_sabores_pizza = 0;
        if($tamanho != null){
            $maximo_sabores_pizza = $tamanho->maximo_sabores;
        }

        $tamanhosPizza = TamanhoPizza::where('empresa_id', $config->empresa_id)
        ->where('status', 1)
        ->with('produtos')
        ->get();
        $link = $request->link;
        $carrinho = $this->_getCarrinho();

        return view('qr_code_cardapio.ofertas', compact(
            'config', 'categorias', 'produtos', 'maximo_sabores_pizza', 'tamanhosPizza', 'link', 'carrinho'));
    }

    public function conta(Request $request){
        $carrinho = $this->_getCarrinho();
        $config = ConfiguracaoCardapio::findOrfail($request->config_id);

        if(!$carrinho){
            session()->flash("flash_error", "Nenhum item adicionado!");
            return redirect()->back();
        }
        // $pedido = Pedido::where('empresa_id', $carrinho->empresa_id)
        // ->where('status', 1)->where('session_cart_cardapio', $carrinho->session_cart_cardapio)
        // ->where('session_cart_user', $_SESSION["session_cart_user"])
        // ->first();

        $pedido = Pedido::where('empresa_id', $carrinho->empresa_id)
        ->where('status', 1)->where('session_cart_cardapio', $carrinho->session_cart_cardapio)
        ->first();

        if($pedido == null){
            $_SESSION["session_cart_cardapio"] = null;
            $_SESSION["session_nome_cardapio"] = null;
            $_SESSION["session_telefone_cardapio"] = null;
            return redirect()->route('cardapio.index', ['link='.$request->link]);

        }

        $totalClientes = ItemPedido::where('pedido_id', $pedido->id)
        ->select('nome_cardapio')->distinct('nome_cardapio')->count();

        $itens = [];
        $subtotal = 0;

        foreach($pedido->itens as $i){
            if($i->nome_cardapio == $_SESSION["session_nome_cardapio"]){
                $itens[] = $i;
                $subtotal += $i->sub_total;
            }
        }

        $pedido->itens = $itens;

        $link = $request->link;
        $categorias = $this->getCategorias($config->empresa_id);

        $configGeral = ConfigGeral::where('empresa_id', $config->empresa_id)->first();
        $tiposPagamento = Nfce::tiposPagamento();
        // dd($tiposPagamento);
        if($configGeral != null){
            $configGeral->tipos_pagamento_pdv = $configGeral != null && $config->tipos_pagamento_pdv ? json_decode($configGeral->tipos_pagamento_pdv) : [];
            $temp = [];
            if(sizeof($configGeral->tipos_pagamento_pdv) > 0){
                foreach($tiposPagamento as $key => $t){
                    if(in_array($t, $configGeral->tipos_pagamento_pdv)){
                        $temp[$key] = $t;
                    }
                }
                $tiposPagamento = $temp;
            }
        }

        return view('qr_code_cardapio.conta', compact('pedido', 'link', 'config', 'categorias', 'carrinho', 'tiposPagamento', 'subtotal'));
    }

    public function produtosDaCategoria(Request $request, $hash){

        $config = ConfiguracaoCardapio::findOrfail($request->config_id);
        $categorias = $this->getCategorias($config->empresa_id);

        $categoria = CategoriaProduto::where('hash_delivery', $hash)
        ->first();

        if($categoria == null){
            abort(404);
        }
        $data = Produto::where('empresa_id', $config->empresa_id)
        ->where('categoria_id', $categoria->id)
        ->where('status', 1)
        ->where('cardapio', 1)->get();

        $produtos = [];
        foreach($data as $item){
            if($item->gerenciar_estoque){
                if($item->estoque && $item->estoque->quantidade > 0){
                    array_push($produtos, $item);
                }
            }else{
                array_push($produtos, $item);
            }
        }

        $carrinho = $this->_getCarrinho();

        $tamanho = TamanhoPizza::where('empresa_id', $config->empresa_id)
        ->orderBy('maximo_sabores', 'desc')->first();
        $maximo_sabores_pizza = 0;
        if($tamanho != null){
            $maximo_sabores_pizza = $tamanho->maximo_sabores;
        }

        $tamanhosPizza = TamanhoPizza::where('empresa_id', $config->empresa_id)
        ->where('status', 1)
        ->with('produtos')
        ->get();
        $link = $request->link;

        return view('qr_code_cardapio.produtos_categoria', compact(
            'config', 'categorias', 'categoria', 'produtos', 'carrinho', 'maximo_sabores_pizza', 'tamanhosPizza', 'link'));
    }

    public function produtoDetalhe(Request $request, $hash){

        $config = ConfiguracaoCardapio::findOrfail($request->config_id);

        $produto = Produto::where('empresa_id', $config->empresa_id)
        ->where('hash_delivery', $hash)
        ->where('status', 1)->first();

        $this->_validaHash($config);
        $categorias = $this->getCategorias($config->empresa_id);

        $produtosEmDestaque = $this->produtosEmDestaque($config->empresa_id);

        $carrinho = $this->_getCarrinho();
        $link = $request->link;

        return view('qr_code_cardapio.index', compact('config', 'categorias', 'produtosEmDestaque', 'carrinho', 'produto',
            'link'));

    }

}
