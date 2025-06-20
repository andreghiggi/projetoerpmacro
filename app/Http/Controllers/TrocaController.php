<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troca;
use App\Models\ItemTroca;
use App\Models\Nfce;
use App\Models\Funcionario;
use App\Models\CategoriaProduto;
use App\Models\Caixa;
use App\Models\Empresa;
use App\Models\ConfigGeral;
use App\Utils\EstoqueUtil;
use NFePHP\DA\NFe\CupomNaoFiscal;

class TrocaController extends Controller
{
    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;

        $this->middleware('permission:troca_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:troca_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:troca_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');

        $data = Troca::where('trocas.empresa_id', $request->empresa_id)
        ->select('trocas.*')
        ->join('nfces', 'nfces.id', '=', 'trocas.nfce_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('trocas.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('trocas.created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('nfces.cliente_id', $cliente_id);
        })
        ->orderBy('trocas.created_at', 'desc')
        ->paginate(env("PAGINACAO"));
        return view('trocas.index', compact('data'));
    }

    public function create(Request $request){
        $codigo = $request->codigo;
        $numero_nfce = $request->numero_nfce;

        $item = Nfce::where('numero_sequencial', $codigo)->where('empresa_id', $request->empresa_id)
        ->first();
        if($item == null){
            $item = Nfce::where('numero', $numero_nfce)->where('empresa_id', $request->empresa_id)
            ->first();
        }

        if($item == null){
            session()->flash("flash_error", "Nenhuma venda encontrada!");
            return redirect()->back();
        }

        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        __validaObjetoEmpresa($item);

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $cliente = $item->cliente;
        $funcionario = $item->funcionario;
        $caixa = __isCaixaAberto();
        $abertura = Caixa::where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $isVendaSuspensa = 0;
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)->get();
        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        $tiposPagamento = Nfce::tiposPagamento();
        // dd($tiposPagamento);
        if($config != null){
            $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
            $temp = [];
            if(sizeof($config->tipos_pagamento_pdv) > 0){
                foreach($tiposPagamento as $key => $t){
                    if(in_array($t, $config->tipos_pagamento_pdv)){
                        $temp[$key] = $t;
                    }
                }
                $tiposPagamento = $temp;
            }
        }
        $tiposPagamento['00'] = 'Vale Crédito';

        $msgTroca = "";
        if(sizeof($item->trocas) > 0){
            $msgTroca = "Essa venda já possui troca!";
        }

        return view('trocas.create', compact('item', 'funcionarios', 'cliente', 'funcionario', 'caixa', 'abertura', 
            'isVendaSuspensa', 'categorias', 'tiposPagamento', 'msgTroca'));
    }

    public function show($id)
    {
        $item = Troca::findOrFail($id);
        return view('trocas.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = Troca::findOrFail($id);
        try {
            $descricaoLog = "#$item->numero_sequencial - R$ " . __moeda($item->valor_troca);

            foreach($item->itens as $i){
                if ($i->produto->gerenciar_estoque) {
                    $this->util->incrementaEstoque($i->produto->id, $i->quantidade, null, $item->nfce->local_id);
                }
            }
            $item->itens()->delete();
            $item->delete();

            __createLog(request()->empresa_id, 'PDV Troca', 'excluir', $descricaoLog);

            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'PDV Troca', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function imprimir($id){

        $item = Troca::findOrFail($id);

        __validaObjetoEmpresa($item);
        $config = Empresa::where('id', $item->empresa_id)
        ->first();

        $cupom = new CupomNaoFiscal($item->nfce, $config, 0, $item);

        $pdf = $cupom->render();
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

}
