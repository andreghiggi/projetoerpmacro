<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\Empresa;
use App\Models\PreVenda;
use App\Models\Cidade;
use App\Models\NaturezaOperacao;
use App\Models\UsuarioEmpresa;
use App\Models\UsuarioEmissao;
use App\Models\Caixa;
use App\Models\ItemNfce;
use App\Models\Produto;
use App\Models\FaturaNfce;
use NFePHP\DA\NFe\Danfce;
use App\Services\NFCeService;
use Illuminate\Support\Facades\DB;
use App\Utils\EstoqueUtil;

class ContadorAdminNFCeController extends Controller
{

    protected $estoqueUtil;

    public function __construct(EstoqueUtil $estoqueUtil){

        $this->estoqueUtil = $estoqueUtil;
        
        if (!is_dir(public_path('zips'))) {
            mkdir(public_path('zips'), 0777, true);
        }
    }

    public function nfce(Request $request){
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');

        $data = Nfce::where('empresa_id', $empresaSelecionada)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $contXml = $this->preparaXmls($start_date, $end_date, $empresaSelecionada, $estado);
        return view('contador.nfce', compact('data', 'contXml'));
    }

    private function preparaXmls($start_date, $end_date, $empresaSelecionada, $estado){
        $data = Nfce::where('empresa_id', $empresaSelecionada)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->where('estado', $estado)
        ->get();

        $cont = 0;

        $dir = 'xml_nfce/';
        if($estado == 'cancelado'){
            $dir = 'xml_nfce_cancelada/';
        }
        foreach($data as $item){
            if (file_exists(public_path($dir) . $item->chave . '.xml')) {
                $cont++;
            }
        }
        return $cont;

    }

    public function downloadNFCe($id){
        $item = Nfce::findOrFail($id);
        $dir = 'xml_nfce/';
        if($item->estado == 'cancelado'){
            $dir = 'xml_nfce_cancelada/';
        }
        if (file_exists(public_path($dir) . $item->chave . '.xml')) {
            return response()->download(public_path($dir) . $item->chave . '.xml');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function danfce($id){
        $item = Nfce::findOrFail($id);

        if (file_exists(public_path('xml_nfce/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfce/') . $item->chave . '.xml');

            $danfe = new Danfce($xml, $item);
            $pdf = $danfe->render();
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function downloadZip(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');

        $contador = Empresa::findOrFail(request()->empresa_id);
        $doc = preg_replace('/[^0-9]/', '', $contador->cpf_cnpj);
        $empresaSelecionada = $contador->empresa_selecionada;
        $data = Nfce::where('empresa_id', $empresaSelecionada)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->where('estado', $estado)
        ->get();
        $zip = new \ZipArchive();
        $zip_file = public_path('zips') . '/xml-'.$doc.'.zip';
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $dir = 'xml_nfce/';
        if($estado == 'cancelado'){
            $dir = 'xml_nfce_cancelada/';
        }

        foreach($data as $item){
            if (file_exists(public_path($dir) . $item->chave . '.xml')) {
                $filename = public_path($dir) . $item->chave . '.xml';

                $zip->addFile($filename, $item->chave . '.xml');
            }
        }
        $zip->close();
        if (file_exists($zip_file)){
            return response()->download($zip_file, 'nfce_'.$doc.'.zip');
        }else{
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function xmlTemp($id)
    {
        $item = Nfce::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        $empresa = $item->empresa;
        $empresa = __objetoParaEmissao($empresa, $item->local_id);

        if ($empresa->arquivo == null) {
            session()->flash("flash_error", "Certificado não encontrado para este emitente");
            return redirect()->route('config.index');
        }

        $nfe_service = new NFCeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "CSC" => $empresa->csc,
            "CSCid" => $empresa->csc_id
        ], $empresa);

        $doc = $nfe_service->gerarXml($item);

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];

            return response($xml)
            ->header('Content-Type', 'application/xml');
        } else {
            return response()->json($doc['erros_xml'], 401);
        }
    }

    public function edit($id)
    {
        $item = Nfce::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        $cidades = Cidade::all();
        $naturezas = NaturezaOperacao::where('empresa_id', $item->empresa_id)->get();
        $caixa = $item->caixa;

        return view('contador.nfce_edit', compact('item', 'cidades', 'naturezas', 'caixa'));
    }

    
    public function nfceUpdate(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $item = Nfce::findOrFail($id);
                __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

                $config = Empresa::find($item->empresa_id);

                $tipoPagamento = $request->tipo_pagamento;
                $request->merge([
                    'emissor_nome' => $config->nome,
                    'emissor_cpf_cnpj' => $config->cpf_cnpj,
                    'ambiente' => $config->ambiente,
                    'numero' => $request->numero_nfce,
                    'estado' => 'novo',
                    'total' => ($request->valor_total) - __convert_value_bd($request->desconto) + __convert_value_bd($request->acrescimo),
                    'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                    'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                    'valor_produtos' => __convert_value_bd($request->valor_total) ?? 0,
                    'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                    'tipo_pagamento' => $request->tipo_pagamento[0],
                    'empresa_id' => $item->empresa_id
                ]);

                $item->fill($request->all())->save();

                foreach ($item->itens as $i) {
                    if ($i->produto->gerenciar_estoque) {
                        $this->estoqueUtil->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
                    }
                }

                $item->itens()->delete();
                $item->fatura()->delete();

                for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                    $product = Produto::findOrFail($request->produto_id[$i]);
                    $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;

                    ItemNfce::create([
                        'nfce_id' => $item->id,
                        'produto_id' => (int)$request->produto_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
                        'valor_custo' => __convert_value_bd($product->valor_compra),
                        'sub_total' => __convert_value_bd($request->sub_total[$i]),
                        'perc_icms' => __convert_value_bd($request->perc_icms[$i]),
                        'perc_pis' => __convert_value_bd($request->perc_pis[$i]),
                        'perc_cofins' => __convert_value_bd($request->perc_cofins[$i]),
                        'perc_ipi' => __convert_value_bd($request->perc_ipi[$i]),
                        'cst_csosn' => $request->cst_csosn[$i],
                        'cst_pis' => $request->cst_pis[$i],
                        'cst_cofins' => $request->cst_cofins[$i],
                        'cst_ipi' => $request->cst_ipi[$i],
                        'perc_red_bc' => $request->perc_red_bc[$i] ? __convert_value_bd($request->perc_red_bc[$i]) : 0,
                        'cfop' => $request->cfop[$i],
                        'ncm' => $request->ncm[$i],
                        'codigo_beneficio_fiscal' => $request->codigo_beneficio_fiscal[$i],
                        'variacao_id' => $variacao_id
                    ]);
                    if ($product->gerenciar_estoque) {
                        $this->estoqueUtil->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $item->local_id);
                    }
                }
                for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                    if ($tipoPagamento[$i]) {
                        FaturaNfce::create([
                            'nfce_id' => $item->id,
                            'tipo_pagamento' => $tipoPagamento[$i],
                            'data_vencimento' => $request->data_vencimento[$i],
                            'valor' => __convert_value_bd($request->valor_fatura[$i])
                        ]);
                    }
                }
            });
session()->flash("flash_success", "NFCe alterada com sucesso!");
} catch (\Exception $e) {
    // echo $e->getMessage() . '<br>' . $e->getLine();
    // die;
    session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
}
return redirect()->route('contador-empresa.nfce');

}


public function destroy($id)
{
    $item = Nfce::findOrFail($id);
    __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

    try {
        if($item->estado != 'cancelado'){
            foreach ($item->itens as $i) {
                if ($i->produto->gerenciar_estoque) {
                    $this->estoqueUtil->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
                }
            }
        }

        $preVenda = PreVenda::where('venda_id', $id)->first();
        if($preVenda){
            $preVenda->delete();
        }
        $item->itens()->delete();
        $item->fatura()->delete();
        $item->contaReceber()->delete();
        $item->delete();
        session()->flash("flash_success", "NFCe removida!");
    } catch (\Exception $e) {
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->back();
}

public function create()
{
    $contador = Empresa::findOrFail(request()->empresa_id);
    $empresaSelecionada = $contador->empresa_selecionada;

    $caixa = Caixa::where('empresa_id', $empresaSelecionada)->where('status', 1)->first();

    if($caixa == null){
        session()->flash("flash_error", "Caixa esta fechado!");
        return redirect()->back();
    }

    $sizeProdutos = Produto::where('empresa_id', $empresaSelecionada)->count();
    if ($sizeProdutos == 0) {
        session()->flash("flash_warning", "Primeiro cadastre um produto!");
        return redirect()->back();
    }
    $cidades = Cidade::all();
    $naturezas = NaturezaOperacao::where('empresa_id', $empresaSelecionada)->get();
    if (sizeof($naturezas) == 0) {
        session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
        return redirect()->back();
    }

    $empresa = Empresa::findOrFail($empresaSelecionada);

    $numeroNfce = Nfce::lastNumero($empresa);
    $naturezaPadrao = NaturezaOperacao::where('empresa_id', $empresaSelecionada)
    ->where('padrao', 1)->first();

    $item = UsuarioEmissao::where('usuario_empresas.empresa_id', $empresaSelecionada)
    ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
    ->select('usuario_emissaos.*')
    ->where('usuario_emissaos.usuario_id', get_id_user())
    ->first();

    if($item != null){
        $numeroNfce = $item->numero_ultima_nfce+1;
    }

    $user_id = 0;

    $user = UsuarioEmpresa::
    select('usuario_empresas.usuario_id')
    ->where('empresa_id', $empresaSelecionada)
    ->first();

    if($user != null){
        $user_id = $user->usuario_id;
    }

    return view('contador.nfce_create', compact('cidades', 'naturezas', 'numeroNfce', 'caixa', 'naturezaPadrao', 'empresaSelecionada', 'user_id'));
}

private function setNumeroSequencial($empresa_id){

    $last = Nfce::where('empresa_id', $empresa_id)
    ->orderBy('numero_sequencial', 'desc')
    ->where('numero_sequencial', '>', 0)->first();
    $numero = $last != null ? $last->numero_sequencial : 0;
    $numero++;

    return $numero;
}

public function store(Request $request)
{
    try {
        DB::transaction(function () use ($request) {

            $contador = Empresa::findOrFail(request()->empresa_id);
            $empresaSelecionada = $contador->empresa_selecionada;

            $cliente_id = $request->cliente_id;
            $empresa = Empresa::findOrFail($empresaSelecionada);
            $caixa = Caixa::where('empresa_id', $empresaSelecionada)->where('status', 1)->first();

            $empresa = __objetoParaEmissao($empresa, $caixa->local_id);

            $tipoPagamento = $request->tipo_pagamento;

            $numeroSerieNfce = $empresa->numero_serie_nfce ? $empresa->numero_serie_nfce : 0;
            $user = UsuarioEmpresa::
            select('usuario_empresas.usuario_id')
            ->where('empresa_id', $empresaSelecionada)
            ->first();

            $request->merge([
                'numero_sequencial' => $this->setNumeroSequencial($empresaSelecionada),
                'emissor_nome' => $empresa->nome,
                'emissor_cpf_cnpj' => $empresa->cpf_cnpj,
                'ambiente' => $empresa->ambiente,
                'chave' => '',
                'cliente_id' => $cliente_id,
                'numero_serie' => $numeroSerieNfce,
                'numero' => $request->numero_nfce ? $request->numero_nfce : 0,
                'cliente_nome' => $request->cliente_nome ?? '',
                'cliente_cpf_cnpj' => $request->cliente_cpf_cnpj ?? '',
                'estado' => 'novo',
                'total' => __convert_value_bd($request->valor_total),
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'valor_produtos' => __convert_value_bd($request->valor_total) ?? 0,
                'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                'caixa_id' => $caixa ? $caixa->id : null,
                'local_id' => $caixa->local_id,
                'tipo_pagamento' => $request->tipo_pagamento[0],
                'dinheiro_recebido' => 0,
                'troco' => 0,
                'user_id' => $user ? $user->usuario_id : null,
                'empresa_id' => $empresaSelecionada
            ]);

                // dd($request->all());
            $nfce = Nfce::create($request->all());

            for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                $product = Produto::findOrFail($request->produto_id[$i]);

                $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;

                ItemNfce::create([
                    'nfce_id' => $nfce->id,
                    'produto_id' => (int)$request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade[$i]),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
                    'valor_custo' => __convert_value_bd($product->valor_compra),
                    'sub_total' => __convert_value_bd($request->sub_total[$i]),
                    'perc_icms' => __convert_value_bd($request->perc_icms[$i]),
                    'perc_pis' => __convert_value_bd($request->perc_pis[$i]),
                    'perc_cofins' => __convert_value_bd($request->perc_cofins[$i]),
                    'perc_ipi' => __convert_value_bd($request->perc_ipi[$i]),
                    'cst_csosn' => $request->cst_csosn[$i],
                    'cst_pis' => $request->cst_pis[$i],
                    'cst_cofins' => $request->cst_cofins[$i],
                    'cst_ipi' => $request->cst_ipi[$i],
                    'perc_red_bc' => $request->perc_red_bc[$i] ? __convert_value_bd($request->perc_red_bc[$i]) : 0,
                    'cfop' => $request->cfop[$i],
                    'ncm' => $request->ncm[$i] ?? '',
                    'codigo_beneficio_fiscal' => $request->codigo_beneficio_fiscal[$i],
                    'variacao_id' => $variacao_id
                ]);

                if ($product->gerenciar_estoque) {
                    $this->estoqueUtil->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $caixa->local_id);
                }

                $tipo = 'reducao';
                $codigo_transacao = $nfce->id;
                $tipo_transacao = 'venda_nfce';

                $this->estoqueUtil->movimentacaoProduto($product->id, __convert_value_bd($request->quantidade[$i]), $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao_id);
            }

            for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                if ($tipoPagamento[$i]) {
                    FaturaNfce::create([
                        'nfce_id' => $nfce->id,
                        'tipo_pagamento' => $tipoPagamento[$i],
                        'data_vencimento' => $request->data_vencimento[$i],
                        'valor' => __convert_value_bd($request->valor_fatura[$i])
                    ]);

                    if ($request->gerar_conta_receber) {
                        ContaReceber::create([
                            'empresa_id' => $empresaSelecionada,
                            'nfce_id' => $nfce->id,
                            'cliente_id' => $cliente_id,
                            'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                            'tipo_pagamento' => $request->tipo_pagamento[$i],
                            'data_vencimento' => $request->data_vencimento[$i],
                            'local_id' => $caixa->local_id,
                            'descricao' => "Parcela " . $i+1 . " de " . sizeof($tipoPagamento)

                        ]);
                    }
                }
            }
        });
session()->flash("flash_success", "NFCe cadastrada!");
} catch (\Exception $e) {
    session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    return redirect()->back();

}
return redirect()->route('contador-empresa.nfce');

}
}
