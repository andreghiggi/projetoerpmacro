<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\WoocommerceUtil;
use App\Models\WoocommercePedido;
use App\Models\Cidade;
use App\Models\Transportadora;
use App\Models\NaturezaOperacao;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Nfe;

class WoocommercePedidoController extends Controller
{

    protected $util;
    protected $endpoint = 'orders';
    public function __construct(WoocommerceUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $woocommerceClient = $this->util->getConfig($request->empresa_id);
        $data = $woocommerceClient->get($this->endpoint);

        foreach($data as $pedido){
            $this->util->criaPedido($request->empresa_id, $pedido);
        }

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_nome = $request->get('cliente_nome');

        $data = WoocommercePedido::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data', '<=', $end_date);
        })
        ->when(!empty($cliente_nome), function ($query) use ($cliente_nome) {
            return $query->where('nome', 'LIKE', "%$cliente_nome%");
        })
        ->orderBy('pedido_id', 'desc')
        ->paginate(30);

        return view('woocommerce_pedidos.index', compact('data'));
    }

    public function show($id){
        $item = WoocommercePedido::findOrFail($id);
        foreach($item->itens as $i){
            if($i->produto_id == null){
                session()->flash("flash_warning", "Alguns produtos deste pedido não estão cadastrados no sistema!");
                return redirect()->route('woocommerce-produtos.index');
            }
        }
        return view('woocommerce_pedidos.show', compact('item'));
    }

    public function gerarNfe($id)
    {
        $item = WoocommercePedido::findOrFail($id);

        if(!$item->cliente){
            session()->flash("flash_error", "Cliente não cadastrado no sistema");
            return redirect()->back();
        }
        $cliente = $item->cliente;
        
        $cidades = Cidade::all();
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();

        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        } 
        // $produtos = Produto::where('empresa_id', request()->empresa_id)->get();
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $caixa = __isCaixaAberto();
        $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
        $numeroNfe = Nfe::lastNumero($empresa);

        $item->cliente_id = $cliente->id;

        $isPedidoWoocommerce = 1;

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        return view('nfe.create', compact('item', 'cidades', 'transportadoras', 'naturezas', 'isPedidoWoocommerce', 'numeroNfe',
            'caixa', 'funcionarios'));
    }

}
