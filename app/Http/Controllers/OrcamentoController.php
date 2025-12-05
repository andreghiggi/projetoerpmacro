<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\NaturezaOperacao;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\ItemNfce;
use App\Models\Produto;
use App\Models\Caixa;
use App\Models\Transportadora;
use App\Models\FaturaNfce;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use App\Models\Funcionario;

class OrcamentoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:orcamento_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:orcamento_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:orcamento_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:orcamento_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');

        $funcionario_id = $request->funcionario_id;

        $data = Nfe::where('empresa_id', request()->empresa_id)->where('tpNF', 1)->where('orcamento', 1)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($funcionario_id), function ($q) use ($funcionario_id) {
            return $q->where('funcionario_id', $funcionario_id);
        })
        
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $funcionarios = Funcionario::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->get();

        return view('orcamento.index', compact('data', 'funcionarios'));
    }


    public function destroy(string $id)
    {
        $item = Nfe::findOrFail($id);
        try {

            $item->itens()->delete();
            $item->fatura()->delete();
            $item->delete();
            $descricaoLog = $item->cliente->info . " R$ " . __moeda($item->total);
            __createLog(request()->empresa_id, 'Orçamento', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Orçamento removido!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Orçamento', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->back();
    }

    public function imprimir($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);

        $config = Empresa::where('id', $item->empresa_id)->first();
        $config = __objetoParaEmissao($config, $item->local_id);

        $p = view('orcamento.imprimir', compact('config', 'item'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Orçamento de Venda $item->numero_sequencial.pdf", array("Attachment" => false));
    }

    public function show($id)
    {
        $data = Nfe::findOrFail($id);
        __validaObjetoEmpresa($data);

        $config = Empresa::where('id', $data->empresa_id)->first();
        $config = __objetoParaEmissao($config, $data->local_id);

        return view('orcamento.show', compact('config', 'data'));
    }

    public function gerarVenda($id)
    {
        $data = Nfe::findOrFail($id);
        $data->orcamento = 0;
        $data->created_at = date('Y-m-d H:i:s');
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $data->numero_serie = $empresa->numero_serie_nfe;
        $data->save();
        session()->flash("flash_success", "Orçamento transformado em venda!");
        return redirect()->route('nfe.index');

    }

    public function gerarVendaNfce($id)
    {
        $item = Nfe::findOrFail($id);
        $item->orcamento = 0;
        $item->created_at = date('Y-m-d H:i:s');
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $numero_serie = $empresa->numero_serie_nfce;
        $numero_nfce = $empresa->numero_ultima_nfce_producao;
        if ($empresa->ambiente == 2) {
            $numero_nfce = $empresa->numero_ultima_nfce_homologacao;
        }

        $caixa = Caixa::where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $dataNfce = [
            'empresa_id' => $empresa->id,
            'cliente_id' => $item->cliente_id,
            'cliente_nome' => $item->cliente->razao_social,
            'cliente_cpf_cnpj' => $item->cliente->cpf_cnpj,
            'numero_serie' => $numero_serie,
            'numero_sequencial' => $this->setNumeroSequencial(),
            'numero' => $numero_nfce,
            'estado' => 'novo',
            'total' => $item->total,
            'desconto' => $item->desconto,
            'acrescimo' => $item->acrescimo,
            'natureza_id' => $empresa->natureza_id_pdv,
            'observacao' => $item->observacao,
            'caixa_id' => $caixa->id,
            'local_id' => $caixa->local_id,
            'funcionario_id' => $item->funcionario_id,
            'user_id' => get_id_user(),
        ];
        // dd($dataNfce);
        $nfce = Nfce::create($dataNfce);
        foreach($item->itens as $i){
            $dataItem = $i->toArray();
            unset($dataItem['id']);
            unset($dataItem['nfe_id']);

            $dataItem['nfce_id'] = $nfce->id;

            ItemNfce::create($dataItem);
        }

        foreach($item->fatura as $f){
            $dataFatura = $f->toArray();
            unset($dataFatura['id']);
            unset($dataFatura['nfe_id']);
            // dd($dataFatura);
            FaturaNfce::create($dataFatura);
        }
        if(sizeof($item->fatura) == 0){
            FaturaNfce::create([
                'nfce_id' => $nfce->id,
                'tipo_pagamento' => '01',
                'data_vencimento' => date('Y-m-d'),
                'valor' => $nfce->total,
                'observacao' => ''
            ]);
        }

        session()->flash("flash_success", "Orçamento transformado em venda NFCe!");
        return redirect()->route('nfce.index');

    }

    private function setNumeroSequencial(){

        $last = Nfce::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;

        return $numero;
    }

    public function gerarVendaMultipla(Request $request){
        $data = [];
        $orcamentosId = [];
        $itens = [];
        for($i=0; $i<sizeof($request->orcamento_id); $i++){
            $item = Nfe::findOrFail($request->orcamento_id[$i]);
            $data[] = $item;
            $orcamentosId[] = $request->orcamento_id[$i];
            foreach($item->itens as $it){
                $indice = 0;
                for($j=0; $j<sizeof($itens); $j++){
                    if($it->produto_id == $itens[$j]->produto_id){
                        $indice = $j;
                    }
                }
                if($indice == 0){
                    $itens[] = $it;
                }else{
                    $itens[$indice]->quantidade += $it->quantidade;
                }
            }
        }

        foreach($itens as $it){
            $it->sub_total = $it->quantidade * $it->valor_unitario;
        }

        $cliente = $item->cliente;

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
        $item->itens = $itens;

        return view('nfe.create', compact('item', 'cidades', 'transportadoras', 'naturezas', 'orcamentosId', 'numeroNfe',
            'caixa'));
    }

    public function edit($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();
        $cidades = Cidade::all();
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        $caixa = __isCaixaAberto();
        $isOrcamento = 1;
        
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        return view('nfe.edit', compact('item', 'transportadoras', 'cidades', 'naturezas', 'caixa', 'isOrcamento', 'funcionarios'));
    }
}
