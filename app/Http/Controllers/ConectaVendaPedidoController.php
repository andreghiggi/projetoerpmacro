<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\ConectaVendaConfig;
use App\Models\ConectaVendaPedido;
use App\Models\Empresa;
use App\Models\NaturezaOperacao;
use App\Models\Nfe;
use App\Models\Transportadora;
use App\Utils\ConectaVendaUtil;
use App\Utils\EstoqueUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConectaVendaPedidoController extends Controller
{

    protected $util;
    public function __construct(ConectaVendaUtil $util, EstoqueUtil $estoqueUtil)
    {
        $this->util = $util;
        $this->estoqueUtil = $estoqueUtil;
    }

    public function index(Request $request)
    {
        $config = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();
        if(!$config){
            return redirect()->route('conecta-venda-config.index');
        }

        $start_date = $request->get('start_date');

        $id_publico = $request->get('id_publico');
        $data = ConectaVendaPedido::where('empresa_id', $request->empresa_id)
            ->orderBy('id', 'desc')
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_criacao', '>=', $start_date);
            })
            ->when(!empty($id_publico), function ($query) use ($id_publico) {
                return $query->where('id', 'LIKE', "%$id_publico%");
            })
            ->paginate(30);
        return view('conecta_venda_pedidos.index', compact('data'));
    }

    public function show($id)
    {
        $pedido = ConectaVendaPedido::with('produtos.variacoes')->findOrFail($id);

        return view('conecta_venda_pedidos.show', compact('pedido'));
    }

    public function finishOrder($id, Request $request)
    {
        $item = ConectaVendaPedido::with('itens.produto.variacoes')->findOrFail($id);
        $config = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();
        if (in_array($item->situacao, ['finalizado', 'Cancelado','Finalizado','cancelado'])) {
            return redirect()->back()->with(session()->flash('flash_error', 'Pedido já finalizado ou cancelado!'));
        }

        $cpfCnpj = !empty(trim($item->cpf)) ? $item->cpf : $item->cnpj;
        $item->cpfCnpj = $cpfCnpj;
        $customer = Cliente::where('cpf_cnpj', $cpfCnpj)->first();

        if (!$customer) {
            $customer = $this->cadastrarClienteConecta($item, $config->empresa_id);
        }

        $item->cliente_id = $customer->id;
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

        $isPedidoConectaVenda = 1;
        return view('nfe.create', compact('item', 'cidades', 'transportadoras', 'naturezas', 'isPedidoConectaVenda', 'numeroNfe',
            'caixa'));
    }

    public function destroy($id, Request $request)
    {
        try {
            DB::beginTransaction();
        $config = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();
        $item = ConectaVendaPedido::with('itens.produto.variacoes')->findOrFail($id);

        if($item->situacao == "Cancelado" || $item->situacao == "cancelado"
            || $item->situacao == "finalizado" || $item->situacao == "Finalizado"
        ) {
            return redirect()->back()->with(session()->flash('flash_error', 'Pedido já Se Encontra Finalizado ou Cancelado!'));
        }

        $response = $this->util->updateOrderStatus($config, $item->id, "cancelado");

        if($response['ok'] != true){
            return redirect()->back()->with(session()->flash('flash_error', 'Algo deu errado, o pedido não pode ser cancelado!'));
        }

        $this->util->returnStock($item, $config);
        $item->situacao = "Cancelado";
        $item->save();
        DB::commit();
        return redirect()->back()->with(session()->flash('flash_success', 'Pedido Cancelado!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(session()->flash('flash_error', $e->getMessage()));

        }
    }

    protected function cadastrarClienteConecta($pedido, $empresa)
    {
        $cidade = Cidade::select('id')->where('nome', $pedido->cidade)->first();
        $cliente = Cliente::create([
            'empresa_id' => $empresa,
            'razao_social' => $pedido->comprador,
            'nome_fantasia' => $pedido->comprador ?? '',
            'cpf_cnpj' => $pedido->cpfCnpj,
            'ie' => $pedido->inscricao_estadual ?? '',
            'contribuinte' => 0,
            'consumidor_final' => 1,
            'email' => $pedido->email ?? '',
            'telefone' => $pedido->telefone ?? '',
            'cidade_id' => $cidade->id,
            'rua' => $pedido->endereco,
            'cep' => $pedido->cep,
            'numero' => $pedido->numero,
            'bairro' => $pedido->bairro,
            'complemento' => $pedido->complemento ?? ''
        ]);
        return $cliente;
    }

}
