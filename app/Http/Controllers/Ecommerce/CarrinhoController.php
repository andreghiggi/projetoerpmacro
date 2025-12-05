<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EcommerceConfig;
use App\Models\Produto;
use App\Models\Carrinho;
use App\Models\Cliente;
use App\Models\ItemCarrinho;
use App\Models\ProdutoVariacao;
use App\Models\CategoriaProduto;
use App\Models\Estoque;
use Illuminate\Support\Str;
use App\Utils\CorreioUtil;

class CarrinhoController extends Controller
{

    public function __construct(CorreioUtil $util){
        session_start();
        $this->util = $util;
    }

    public function index(Request $request){
        $item = $this->_getCarrinho();
        $config = EcommerceConfig::findOrfail($request->loja_id);
        if(isset($_SESSION["session_cart"])){
            $item = Carrinho::where('session_cart', $_SESSION["session_cart"])
            ->first();
        }

        $categorias = CategoriaProduto::where('ecommerce', 1)->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();
        $clienteLogado = $this->_getClienteLogado();

        $cliente = null;
        $enderecos = [];
        $dataFrete = null;
        if($item == null){
            return redirect()->route('loja.index', 'link='.$config->loja_id);
        }
        if($clienteLogado){
            $cliente = Cliente::findOrFail($clienteLogado);

            $somaPeso = $item->somaPeso();
            $dimensoes = $item->somaDimensoes();
            $cepOrigem = str_replace("-", "", $config->cep);

            $total = $item->valor_total;
            foreach($cliente->enderecosEcommerce as $endereco){
                $cepDestino = str_replace("-", "", $endereco->cep);
                $data = $this->util->getValores($cepOrigem, $cepDestino, $dimensoes['altura'], $dimensoes['largura'], $dimensoes['comprimento'], $somaPeso);
                $dataFrete .= view('loja.partials.calculo_frete_enderecos', compact('data', 'config', 'endereco', 'total'));
            }
        }

        return view('loja.carrinho', compact('item', 'config', 'categorias', 'cliente', 'enderecos', 'dataFrete'));
    }

    private function _getCarrinho(){
        $data = [];
        if(isset($_SESSION["session_cart"])){
            $data = Carrinho::where('session_cart', $_SESSION["session_cart"])
            ->first();
        }
        return $data;
    }

    public function adicionar(Request $request){

        if(!isset($_SESSION["session_cart"])){
            $session_cart = Str::random(30);
            $_SESSION['session_cart'] = $session_cart;
        }else{
            $session_cart = $_SESSION['session_cart'];
        }

        $config = EcommerceConfig::findOrfail($request->loja_id);
        $produto = Produto::findOrfail($request->produto_id);

        $quantidade = __convert_value_bd($request->quantidade);

        // dd($request->all());
        if($produto->gerenciar_estoque){
            if(!$produto->estoque){
                session()->flash("flash_error", 'Produto sem estoque');
                return redirect()->back();
            }

            if(sizeof($produto->variacoes) == 0){
                if($quantidade > $produto->estoque->quantidade){
                    session()->flash("flash_error", 'Estoque insuficinete, quantidade máxima de ' . number_format($produto->estoque->quantidade, 0) . ' ' . $produto->unidade .  ' para este item!');
                    return redirect()->back();
                }
            }else{
                $estoque = Estoque::where('produto_id', $produto->id)->where('produto_variacao_id', $request->variacao_id)
                ->first();
                if($quantidade > $estoque->quantidade){
                    session()->flash("flash_error", 'Estoque insuficinete, quantidade máxima de ' . number_format($estoque->quantidade, 0) . ' ' . $produto->unidade .  ' para este item!');
                    return redirect()->back();
                }
            }
        }

        $carrinho = Carrinho::where('session_cart', $session_cart)
        ->first();


        $produtoVariacao = null;
        if(isset($request->variacao_id)){
            $produtoVariacao = ProdutoVariacao::findOrfail($request->variacao_id);
            $produto->valor_ecommerce = $produtoVariacao->valor;
        }else{
            if($produto->valor_ecommerce == null){
                $produto->valor_ecommerce = $produto->valor_unitario;
            }
        }

        if($carrinho == null){
            //novo carrinho
            $clienteLogado = $this->_getClienteLogado();

            $carrinho = Carrinho::create([
                'cliente_id' => $clienteLogado ? $clienteLogado : null,
                'empresa_id' => $config->empresa_id,
                'estado' => 'pendente',
                'valor_total' => $produto->valor_ecommerce * $quantidade,
                'endereco_id' => null,
                'valor_frete' => 0,
                'session_cart' => $session_cart
            ]);

            ItemCarrinho::create([
                'carrinho_id' => $carrinho->id,
                'produto_id' => $request->produto_id,
                'variacao_id' => isset($request->variacao_id) ? $request->variacao_id : null,
                'quantidade' => $quantidade,
                'valor_unitario' => $produto->valor_ecommerce,
                'sub_total' => $produto->valor_ecommerce * $quantidade
            ]);
            session()->flash("flash_success", "Produto adicionado ao carrinho!");
        }else{

            ItemCarrinho::create([
                'carrinho_id' => $carrinho->id,
                'produto_id' => $request->produto_id,
                'variacao_id' => isset($request->variacao_id) ? $request->variacao_id : null,
                'quantidade' => $quantidade,
                'valor_unitario' => $produto->valor_ecommerce,
                'sub_total' => $produto->valor_ecommerce * $quantidade
            ]);

            session()->flash("flash_success", "Produto adicionado ao carrinho!");
        }
        $this->_atualizaValorCarrinho($carrinho->id);
        
        return redirect()->route('loja.carrinho', 'link='.$config->loja_id);
    }

    private function _getClienteLogado(){
        if(isset($_SESSION['cliente_ecommerce'])){
            return $_SESSION['cliente_ecommerce'];
        }
        return null;
    }

    public function removeItem(Request $request, $id){
        $item = ItemCarrinho::findOrfail($id);
        try {
            $item->delete();

            $this->_atualizaValorCarrinho($item->carrinho_id);
            session()->flash("flash_success", "Item removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->back();
    }

    private function _atualizaValorCarrinho($carrinho_id){
        $item = Carrinho::findOrfail($carrinho_id);
        $item->valor_total = $item->itens->sum('sub_total') + $item->valor_frete;
        $item->save();
    }

    public function atualizaQuantidade(Request $request, $id){
        $item = ItemCarrinho::findOrfail($id);
        try {

            $item->quantidade = (float)$request->quantidade;
            if($item->produto->gerenciar_estoque){



                if(sizeof($item->produto->variacoes) == 0){

                    if($item->quantidade > $item->produto->estoque->quantidade){
                        session()->flash("flash_error", 'Estoque insuficinete, quantidade máxima de ' . number_format($item->produto->estoque->quantidade, 0) . ' ' . $item->produto->unidade .  ' para este item!');
                        return redirect()->back();
                    }
                }else{
                    $estoque = Estoque::where('produto_id', $item->produto->id)->where('produto_variacao_id', $request->produto_variacao_id)
                    ->first();
                    // dd($estoque);
                    if($item->quantidade > $estoque->quantidade){
                        session()->flash("flash_error", 'Estoque insuficinete, quantidade máxima de ' . number_format($estoque->quantidade, 0) . ' ' . $item->produto->unidade .  ' para este item!');
                        return redirect()->back();
                    }
                }
            }
            $item->sub_total = $item->valor_unitario * (float)$request->quantidade;
            $item->save();
            $this->_atualizaValorCarrinho($item->carrinho_id);
            session()->flash("flash_success", "Quantidade atualizada com sucesso!");
        } catch (\Exception $e) {

            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->back();
    }

    public function setarFrete(Request $request){
        // dd($request->all());
        $item = $this->_getCarrinho();
        $item->valor_frete = $request->valor_frete;
        $item->endereco_id = $request->endereco_id;
        $item->tipo_frete = $request->tipo_frete;
        $item->cep = $request->cep;
        $item->save();

        $this->_atualizaValorCarrinho($item->id);
        $config = EcommerceConfig::findOrfail($request->loja_id);
        $clienteLogado = $this->_getClienteLogado();
        if($clienteLogado == null){
            return redirect()->route('loja.cadastro', 'link='.$config->loja_id);
        }

        return redirect()->route('loja.pagamento', 'link='.$config->loja_id);

    }
    
}
