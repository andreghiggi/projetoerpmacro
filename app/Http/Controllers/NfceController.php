<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\FaturaNfce;
use App\Models\ItemNfce;
use App\Models\PreVenda;
use App\Models\UsuarioEmissao;
use App\Models\NaturezaOperacao;
use Illuminate\Http\Request;
use NFePHP\DA\NFe\Danfce;
use App\Models\Nfce;
use App\Models\Localizacao;
use App\Models\ProdutoLocalizacao;
use App\Models\ConfigGeral;
use App\Models\Inutilizacao;
use App\Models\Produto;
use App\Models\ContaReceber;
use Illuminate\Support\Facades\DB;
use Spatie\FlareClient\View;
use App\Services\NFCeService;
use App\Utils\EstoqueUtil;
use File;
use App\Models\Contigencia;

class NfceController extends Controller
{
    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;

        $this->middleware('permission:nfce_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:nfce_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:nfce_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:nfce_delete', ['only' => ['destroy']]);
    }

    private function setNumeroSequencial(){
        $docs = Nfce::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->get();

        $last = Nfce::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;

        foreach($docs as $d){
            $d->numero_sequencial = $numero;
            $d->save();
            $numero++;
        }
    }

    private function getContigencia($empresa_id){
        $active = Contigencia::
        where('empresa_id', $empresa_id)
        ->where('status', 1)
        ->where('documento', 'NFCe')
        ->first();
        return $active;
    }

    private function corrigeNumeros($empresa_id){

        $config = ConfigGeral::where('empresa_id', $empresa_id)->first();
        if($config != null && $config->corrigir_numeracao_fiscal == 0){
            return;
        }
        
        $item = UsuarioEmissao::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
        ->select('usuario_emissaos.*')
        ->where('usuario_emissaos.usuario_id', get_id_user())
        ->first();

        if($item != null){
            return;
        }

        $locais = Localizacao::where('empresa_id', $empresa_id)->where('status', 1)->get();
        foreach($locais as $key => $local){
            $empresa = Empresa::findOrFail($empresa_id);
            $caixa = __isCaixaAberto();
            if($caixa){
                $empresa = __objetoParaEmissao($empresa, $local->id);
            }


            if($empresa->ambiente == 1){
                $numero = $empresa->numero_ultima_nfce_producao;
            }else{
                $numero = $empresa->numero_ultima_nfce_homologacao;
            }

            if($numero){
                Nfce::where(function($q) {
                    $q->where('estado', 'novo')->orWhere('estado', 'rejeitado');
                })
                ->where('empresa_id', $empresa_id)
                ->where('local_id', $local->id)
            // ->where('caixa_id', $caixa->id)
                ->update(['numero' => $numero+1]);
            }
        }
    }

    public function index(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $this->corrigeNumeros($request->empresa_id);
        
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');
        $numero_nfce = $request->get('numero_nfce');

        $this->setNumeroSequencial();

        $data = Nfce::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($numero_nfce, function ($query) use ($numero_nfce) {
            return $query->where('numero', $numero_nfce);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());
        $contigencia = $this->getContigencia(request()->empresa_id);

        return View('nfce.index', compact('data', 'contigencia'));
    }

    public function create()
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $sizeProdutos = Produto::where('empresa_id', request()->empresa_id)->count();
        if ($sizeProdutos == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um produto!");
            return redirect()->route('produtos.create');
        }
        $cidades = Cidade::all();
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        }
        $caixa = __isCaixaAberto();
        $empresa = Empresa::findOrFail(request()->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $caixa->local_id);

        $numeroNfce = Nfce::lastNumero($empresa);
        $naturezaPadrao = NaturezaOperacao::where('empresa_id', request()->empresa_id)
        ->where('padrao', 1)->first();

        $item = UsuarioEmissao::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
        ->select('usuario_emissaos.*')
        ->where('usuario_emissaos.usuario_id', get_id_user())
        ->first();

        if($item != null){
            $numeroNfce = $item->numero_ultima_nfce+1;
        }
        return view('nfce.create', compact('cidades', 'naturezas', 'numeroNfce', 'caixa', 'naturezaPadrao'));
    }

    public function edit($id)
    {
        $item = Nfce::findOrFail($id);
        __validaObjetoEmpresa($item);

        $cidades = Cidade::all();
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        $caixa = __isCaixaAberto();

        return view('nfce.edit', compact('item', 'cidades', 'naturezas', 'caixa'));
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $cliente_id = $request->cliente_id;
                $empresa = Empresa::findOrFail($request->empresa_id);
                $caixa = __isCaixaAberto();

                $empresa = __objetoParaEmissao($empresa, $caixa->local_id);

                $tipoPagamento = $request->tipo_pagamento;

                $numeroSerieNfce = $empresa->numero_serie_nfce ? $empresa->numero_serie_nfce : 0;
                $configUsuarioEmissao = UsuarioEmissao::where('usuario_empresas.empresa_id', request()->empresa_id)
                ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
                ->select('usuario_emissaos.*')
                ->where('usuario_emissaos.usuario_id', get_id_user())
                ->first();

                if($configUsuarioEmissao != null){
                    $numeroSerieNfce = $configUsuarioEmissao->numero_serie_nfce;
                }
                $request->merge([
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
                    'user_id' => \Auth::user()->id
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
                        $this->util->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $caixa->local_id);
                    }

                    $tipo = 'reducao';
                    $codigo_transacao = $nfce->id;
                    $tipo_transacao = 'venda_nfce';

                    $this->util->movimentacaoProduto($product->id, __convert_value_bd($request->quantidade[$i]), $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao_id);
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
                                'empresa_id' => $request->empresa_id,
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
    // echo $e->getFile() . '<br>' . $e->getLine();
    // die;
    session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
}
return redirect()->route('nfce.index');
}

public function update(Request $request, $id)
{
    try {
        DB::transaction(function () use ($request, $id) {
            $item = Nfce::findOrFail($id);
            __validaObjetoEmpresa($item);

            $config = Empresa::find($request->empresa_id);

            $tipoPagamento = $request->tipo_pagamento;
            $request->merge([
                'emissor_nome' => $config->nome,
                'emissor_cpf_cnpj' => $config->cpf_cnpj,
                'ambiente' => $config->ambiente,
                'numero' => $request->numero_nfce,
                // 'estado' => 'novo',
                'total' => ($request->valor_total) - __convert_value_bd($request->desconto) + __convert_value_bd($request->acrescimo),
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'valor_produtos' => __convert_value_bd($request->valor_total) ?? 0,
                'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                'tipo_pagamento' => $request->tipo_pagamento[0],
            ]);

            $item->fill($request->all())->save();

            foreach ($item->itens as $i) {
                if ($i->produto->gerenciar_estoque) {
                    $this->util->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
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
                    $this->util->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $item->local_id);
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
    return redirect()->route('nfce.index');
}

private function cadastrarCliente($request)
{
    $cliente = Cliente::create([
        'empresa_id' => $request->empresa_id,
        'razao_social' => $request->nome,
        'nome_fantasia' => $request->nome_fantasia,
        'cpf_cnpj' => $request->cpf_cnpj,
        'ie' => $request->ie,
        'contribuinte' => $request->contribuinte,
        'consumidor_final' => $request->consumidor_final,
        'email' => $request->email ?? '',
        'telefone' => $request->telefone ?? '',
        'cidade_id' => $request->cliente_cidade,
        'rua' => $request->cliente_rua,
        'cep' => $request->cep,
        'numero' => $request->cliente_numero,
        'bairro' => $request->cliente_bairro,
        'complemento' => $request->complemento
    ]);
    return $cliente->id;
}

private function atualizaCliente($request)
{
    $cliente = Cliente::findOrFail($request->cliente_id);
    $cliente->update([
        'razao_social' => $request->nome,
        'nome_fantasia' => $request->nome_fantasia,
        'cpf_cnpj' => $request->cpf_cnpj,
        'ie' => $request->ie,
        'contribuinte' => $request->contribuinte,
        'consumidor_final' => $request->consumidor_final,
        'email' => $request->email ?? '',
        'telefone' => $request->telefone ?? '',
        'cidade_id' => $request->cliente_cidade,
        'rua' => $request->cliente_rua,
        'cep' => $request->cep,
        'numero' => $request->cliente_numero,
        'bairro' => $request->cliente_bairro,
        'complemento' => $request->complemento
    ]);
    return $cliente->id;
}

public function imprimir($id)
{
    $item = Nfce::findOrFail($id);
    __validaObjetoEmpresa($item);

    if($item->contigencia && $item->reenvio_contigencia == 0){
        if (file_exists(public_path('xml_nfce_contigencia/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfce_contigencia/') . $item->chave . '.xml');
            $danfe = new Danfce($xml, $item);
            $empresa = $item->empresa;
            $empresa = __objetoParaEmissao($empresa, $item->local_id);

            if($empresa->logo){
                $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . $empresa->logo));
                $danfe->logoParameters($logo, 'L');
            }
            $pdf = $danfe->render();
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }else{

        if (file_exists(public_path('xml_nfce/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfce/') . $item->chave . '.xml');
            $danfe = new Danfce($xml, $item);
            $empresa = $item->empresa;
            $empresa = __objetoParaEmissao($empresa, $item->local_id);
            
            if($empresa->logo){
                $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . $empresa->logo));
                $danfe->logoParameters($logo, 'L');
            }
            $pdf = $danfe->render();
            header("Content-Disposition: ; filename=DANFCE $item->numero.pdf");
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }
}

public function downloadXml($id)
{
    $item = Nfce::findOrFail($id);
    __validaObjetoEmpresa($item);

    if($item->estado == 'aprovado'){
        if (file_exists(public_path('xml_nfce/') . $item->chave . '.xml')) {
            return response()->download(public_path('xml_nfce/') . $item->chave . '.xml');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }elseif($item->estado == 'cancelado'){
        if (file_exists(public_path('xml_nfce/') . $item->chave . '.xml')) {
            return response()->download(public_path('xml_nfce_cancelada/') . $item->chave . '.xml');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }else{
        session()->flash("flash_error", "Nada encontrado");
        return redirect()->back();
    }
}

public function xmlTempContigencia($id)
{
    $item = Nfce::findOrFail($id);
    __validaObjetoEmpresa($item);

    $xml = file_get_contents(public_path('xml_nfce_contigencia/'.$item->chave.'.xml'));
    return response($xml)
    ->header('Content-Type', 'application/xml');
}

public function xmlTemp($id)
{
    $item = Nfce::findOrFail($id);
    __validaObjetoEmpresa($item);

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
        // "schemes" => "PL_009_V4",
        "schemes" => "PL_010_V1.21",
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

public function danfceTemporaria($id)
{
    $item = Nfce::findOrFail($id);
    __validaObjetoEmpresa($item);

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
        $xmlTemp = simplexml_load_string($xml);

        $itensComErro = "";
        $regime = $empresa->tributacao;

        foreach ($xmlTemp->infNFe->det as $item) {
            if (isset($item->imposto->ICMS)) {
                $icms = (array_values((array)$item->imposto->ICMS));
                if(sizeof($icms) == 0){
                    $itensComErro .= " Produto " . $item->prod->xProd . " não formando a TAG ICMS, confira se o CST do item corresponde a tributação, regime configurado: $regime";
                }
            }
        }

        if($itensComErro){
            session()->flash("flash_error", $itensComErro);
            return redirect()->back();
        }

        $signed = $nfe_service->sign($xml);

        $danfce = new Danfce($signed);

        if($empresa->logo){
            $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . $empresa->logo));
            $danfce->logoParameters($logo, 'L');
        }
        $pdf = $danfce->render();

        return response($pdf)
        ->header('Content-Type', 'application/pdf');
        return response($xml)
        ->header('Content-Type', 'application/xml');
    } else {
        return response()->json($doc['erros_xml'], 401);
    }
}

public function destroy($id)
{
    $item = Nfce::findOrFail($id);
    __validaObjetoEmpresa($item);
    
    try {
        if($item->estado != 'cancelado'){
            foreach ($item->itens as $i) {
                if ($i->produto->gerenciar_estoque) {
                    $this->util->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
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
        // echo $e->getMessage() . '<br>' . $e->getLine();
        // die;
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->route('nfce.index');
}

public function inutilizar(Request $request)
{
    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    $data = Inutilizacao::where('empresa_id', request()->empresa_id)
    ->where('modelo', '65')->orderBy('id', 'desc')
    ->when(!empty($start_date), function ($query) use ($start_date) {
        return $query->whereDate('created_at', '>=', $start_date);
    })
    ->when(!empty($end_date), function ($query) use ($end_date,) {
        return $query->whereDate('created_at', '<=', $end_date);
    })
    ->get();
    $modelo = '65';
    return view('inutilizacao.index', compact('data', 'modelo'));
}

public function inutilStore(Request $request)
{
    $request->merge([
        'estado' => 'novo',
        'modelo' => '65'
    ]);
    try {
        Inutilizacao::create($request->all());
        session()->flash("flash_success", "Inutilização criada!");
    } catch (\Exception $e) {
        echo $e->getMessage() . '<br>' . $e->getLine();
        die;
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->back();
}

public function inutilDestroy($id)
{
    $item = Inutilizacao::findOrFail($id);
    try {
        $item->delete();
        session()->flash("flash_success", "Inutilização removida!");
    } catch (\Exception $e) {
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->back();
}

public function alterarEstado($id)
{
    $item = Nfce::findOrFail($id);
    return view('nfce.estado_fiscal', compact('item'));
}

public function storeEstado(Request $request, $id)
{
    $item = Nfce::findOrFail($id);
    try {
        $item->estado = $request->estado_emissao;
        if ($request->hasFile('file')) {
            $xml = simplexml_load_file($request->file);
            $chave = substr($xml->NFe->infNFe->attributes()->Id, 3, 44);
            $file = $request->file;

            $dir = 'xml_nfce/';
            if($item->estado == 'cancelado'){
                $dir = 'xml_nfce_cancelada/';
            }
            $file->move(public_path($dir), $chave . '.xml');
            $item->chave = $chave;
            $item->data_emissao = date('Y-m-d H:i:s');
            $item->numero = (int)$xml->NFe->infNFe->ide->nNF;
        }
        $item->save();
        session()->flash("flash_success", "Estado alterado");
    } catch (\Exception $e) {
        session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
    }
    return redirect()->route('nfce.index');
}

public function show($id)
{
    $data = Nfce::findOrFail($id);

    return view('nfce.show', compact('data'));
}

public function importZip(){

    $zip_loaded = extension_loaded('zip') ? true : false;
    if ($zip_loaded === false) {
        session()->flash('flash_error', "Por favor instale/habilite o PHP zip para importar");
        return redirect()->back();
    }
    return view('nfce.import_zip');
}

public function importZipStore(Request $request){
    if ($request->hasFile('file')) {

        if (!is_dir(public_path('extract'))) {
            mkdir(public_path('extract'), 0777, true);
        }

        $zip = new \ZipArchive();
        $zip->open($request->file);
        $destino = public_path('extract');

        $this->clearFolder($destino);

        if($zip->extractTo($destino) == TRUE){

            $data = $this->preparaXmls($destino);
            if(sizeof($data) == 0){
                session()->flash('flash_error', "Algo errado com o arquivo!");
                return redirect()->back();
            }

            return view('nfce.import_zip_view', compact('data'));

        }else {
            session()->flash('flash_error', "Erro ao desconpactar arquivo");
            return redirect()->back();
        }
        $zip->close();
    }else{
        session()->flash('flash_error', 'Nenhum arquivo selecionado!');
        return redirect()->back();
    }
}

private function preparaXmls($destino){
    $files = glob($destino."/*");
    $data = [];
    foreach($files as $file){
        if(is_file($file)){

            $xml = simplexml_load_file($file);

            $produtos = $this->getProdutos($xml);
            $fatura = $this->getFatura($xml);

            if($produtos != null){
                $item = [
                    'data' => (string)$xml->NFe->infNFe->ide->dhEmi,
                    'serie' => (string)$xml->NFe->infNFe->ide->serie,
                    'chave' => substr($xml->NFe->infNFe->attributes()->Id, 3, 44),
                    'valor_total' => (float)$xml->NFe->infNFe->total->ICMSTot->vProd,
                    'numero_nfe' => (int)$xml->NFe->infNFe->ide->nNF,
                    'desconto' => (float)$xml->NFe->infNFe->total->ICMSTot->vDesc,
                    'produtos' => $produtos,
                    'fatura' => $fatura,
                    'file' => $file,
                    'natureza' => (string)$xml->NFe->infNFe->ide->natOp[0],
                    'observacao' => (string)$xml->NFe->infNFe->infAdic ? (string)$xml->NFe->infNFe->infAdic->infCpl[0] : '',
                    'tipo_pagamento' => (string)$xml->NFe->infNFe->pag->detPag->tPag,
                    'finNFe' => (string)$xml->NFe->infNFe->ide->finNFe,
                    'data_emissao'
                ];
                array_push($data, $item);
                // dd($item);
            }
        }
    }

    return $data;
}

private function getFatura($xml){
    $fatura = [];

    try{
        if (!empty($xml->NFe->infNFe->cobr->dup))
        {
            foreach($xml->NFe->infNFe->cobr->dup as $dup) {
                $titulo = $dup->nDup;
                $vencimento = $dup->dVenc;

                $vlr_parcela = number_format((double) $dup->vDup, 2, ",", "."); 

                $parcela = [
                    'numero' => (int)$titulo,
                    'vencimento' => (string)$dup->dVenc,
                    'valor_parcela' => $vlr_parcela,
                    'rand' => rand(0, 10000)
                ];
                array_push($fatura, $parcela);
            }
        }else{

            $vencimento = explode('-', substr($xml->NFe->infNFe->ide->dhEmi[0], 0,10));

            $parcela = [
                'numero' => 1,
                'vencimento' => substr($xml->NFe->infNFe->ide->dhEmi[0], 0,10),
                'valor_parcela' => (float)$xml->NFe->infNFe->pag->detPag->vPag[0],
                'rand' => rand(0, 10000)
            ];
            array_push($fatura, $parcela);
        }
    }catch(\Exception $e){

    }

    return $fatura;
}

private function getProdutos($xml){
    $itens = [];
    try{

        foreach($xml->NFe->infNFe->det as $item) {

            $produto = Produto::verificaCadastrado(
                $item->prod->cEAN,
                $item->prod->xProd,
                $item->prod->cProd,
                request()->empresa_id
            );

            $trib = Produto::getTrib($item->imposto);
            // dd($trib);
            $cfops = $this->getCfpos((string)$item->prod->CFOP);
            $item = [
                'codigo' => (string)$item->prod->cProd,
                'nome' => (string)$item->prod->xProd,
                'ncm' => (string)$item->prod->NCM,
                'cfop' => (string)$item->prod->CFOP,
                'cfop_estadual' => $cfops['cfop_estadual'],
                'cfop_outro_estado' => $cfops['cfop_outro_estado'],
                'cfop_entrada_estadual' => $cfops['cfop_entrada_estadual'],
                'cfop_entrada_outro_estado' => $cfops['cfop_entrada_outro_estado'],
                'unidade' => (string)$item->prod->uCom,
                'valor_unitario' => (float)$item->prod->vUnCom,
                'sub_total' => (float)$item->prod->vProd,
                'quantidade' => (float)$item->prod->qCom,
                'codigo_barras' => (string)$item->prod->cEAN == 'SEM GTIN' ? '' : (string)$item->prod->cEAN,
                'produto_id' => $produto ? $produto->id : 0,
                'cest' => (string)$item->prod->CEST ? (string)$item->prod->CEST : '',
                'empresa_id' => request()->empresa_id,

                'cst_csosn' => $trib['cst_csosn'],
                'cst_ipi' => $trib['cst_ipi'],
                'cst_pis' => $trib['cst_pis'],
                'cst_cofins' => $trib['cst_cofins'],

                'perc_icms' => $trib['pICMS'],
                'perc_pis' => $trib['pPIS'],
                'perc_cofins' => $trib['pCOFINS'],
                'perc_ipi' => $trib['pIPI'],
                'perc_red_bc' => $trib['pRedBC'],
                'origem' => $trib['orig'],
                'cEnq' => '999'

            ];
            array_push($itens, $item);

        }
        return $itens;
    }catch(\Exception $e){
        // echo $e->getMessage();
        // die;
        return null;
    }
}

private function getCfpos($cfop){

    $n = substr($cfop, 1, 4);
    return [
        'cfop_estadual' => '5'.$n,
        'cfop_outro_estado' => '6'.$n,
        'cfop_entrada_estadual' => '1'.$n,
        'cfop_entrada_outro_estado' => '2'.$n,
    ];
}

private function clearFolder($destino){
    $files = glob($destino."/*");
    foreach($files as $file){ 
        if(is_file($file)) unlink($file); 
    }
}

public function importZipStoreFiles(Request $request){
    try{

        $cont = DB::transaction(function () use ($request) {
            $selecionados = [];
            for($i=0; $i<sizeof($request->file_id); $i++){
                $selecionados[] = $request->file_id[$i];
            }
            $cont = 0;
            for($i=0; $i<sizeof($request->data); $i++){
                $data = json_decode($request->data[$i]);
                if(in_array($data->chave, $selecionados)){

                    $produtos = $this->insereProdutos($data->produtos, $request->local_id);

                    $nfe = $this->salvarVenda($data, $request->local_id);
                    if($nfe != 0){
                        File::copy($data->file, public_path("xml_nfce/").$data->chave.".xml");
                        $cont++;
                    }
                }
            }
            return $cont;
        });
        session()->flash("flash_success", 'Total de vendas salvas: ' . $cont);
        return redirect()->route('nfce.index');
    }catch(\Exception $e){
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        return redirect()->route('nfce.index');
    }
}

private function salvarVenda($venda, $local_id){
    $natureza = $this->insereNatureza($venda->natureza);
    $empresa = Empresa::findOrFail(request()->empresa_id);
    $empresa = __objetoParaEmissao($empresa, $local_id);
    // dd($venda);

    $dataVenda = [
        'estado' => 'aprovado',
        'empresa_id' => request()->empresa_id,
        'numero' => $venda->numero_nfe,
        'chave' => $venda->chave,
        'natureza_id' => $natureza->id,
        'emissor_nome' => $empresa->nome,
        'emissor_cpf_cnpj' => $empresa->cpf_cnpj,
        'numero_serie' => $venda->serie,
        'total' => $venda->valor_total,
        'desconto' => $venda->desconto,
        'tipo_pagamento' => $venda->tipo_pagamento,
        'observacao' => $venda->observacao,
        'tpNF' => 1,
        'local_id' => $local_id
    ];

    $nfe = Nfce::where('empresa_id', request()->empresa_id)
    ->where('chave', $venda->chave)->first();

    if($nfe == null){

        try{
            // dd($dataVenda);
            $nfe = Nfce::create($dataVenda);

            $nfe->data_emissao = $venda->data;
            $nfe->created_at = $venda->data;
            $nfe->save();
        }catch(\Exception $e){
            dd($dataVenda);
        }
    }else{
        $nfe->data_emissao = $venda->data;
        $nfe->created_at = $venda->data;
        $nfe->save();
        return 0;
    }
    

    $nfe->data_emissao = $venda->data;
    $nfe->save();
    foreach($venda->produtos as $i){
        $p = Produto::where('empresa_id', request()->empresa_id)
        ->where('nome', $i->nome)->first();

        if($p != null){
            $ncm = $i->ncm;
            $mask = '####.##.##';
            if(!str_contains($ncm, ".")){
                $ncm = __mask($ncm, $mask);
            }
            ItemNfce::create([
                'nfce_id' => $nfe->id,
                'produto_id' => $p->id,
                'quantidade' => $i->quantidade,
                'valor_unitario' => $i->valor_unitario,
                'sub_total' => $i->sub_total,
                'perc_icms' => $i->perc_icms,
                'perc_pis' => $i->perc_pis,
                'perc_cofins' => $i->perc_pis,
                'perc_ipi' => $i->perc_pis,
                'cst_csosn' => $i->perc_pis,
                'cst_pis' => $i->perc_pis,
                'cst_cofins' => $i->perc_pis,
                'cst_ipi' => $i->perc_pis,
                'perc_red_bc' => $i->perc_pis,
                'cfop' => $i->cfop,
                'ncm' => $ncm,
                'cEnq' => $i->cEnq,
                'origem' => $i->origem,
                'cest' => $i->cest

            ]);
        }
    }

    foreach($venda->fatura as $f){
        FaturaNfce::create([
            'nfce_id' => $nfe->id,
            'tipo_pagamento' => $venda->tipo_pagamento,
            'data_vencimento' => $f->vencimento,
            'valor' => __convert_value_bd($f->valor_parcela)
        ]);

        if(strtotime($f->vencimento) >= strtotime(date('Y-m-d'))){
            ContaReceber::create([
                'empresa_id' => $nfe->empresa_id,
                'nfce_id' => $nfe->id,
                'valor_integral' => __convert_value_bd($f->valor_parcela),
                'tipo_pagamento' => $venda->tipo_pagamento,
                'data_vencimento' => $f->vencimento,
                'local_id' => $local_id,
            ]);
        }
    }

    return 1;

}

private function insereNatureza($descricao){
    $natureza = NaturezaOperacao::where('descricao', $descricao)
    ->where('empresa_id', request()->empresa_id)
    ->first();

    if($natureza != null) return $natureza;

    $data = [
        'descricao' => $descricao,
        'empresa_id' => request()->empresa_id,
    ];
    return NaturezaOperacao::create($data);
}


private function insereProdutos($data, $local_id){
    $produtos = [];
    foreach($data as $item){
        $produto = Produto::where('empresa_id', request()->empresa_id)
        ->where('nome', $item->nome)->first();

        if($produto == null){

            $ncm = $item->ncm;
            $mask = '####.##.##';
            if(!str_contains($ncm, ".")){
                $item->ncm = __mask($ncm, $mask);
            }
            $item->referencia = $item->codigo;
            $item->valor_prazo = 0;
            
            $p = Produto::create((array)$item);
            ProdutoLocalizacao::updateOrCreate([
                'produto_id' => $p->id, 
                'localizacao_id' => $local_id
            ]);
            array_push($produtos, $p);
        }else{
            array_push($produtos, $produto);
        }

    }
    return $produtos;
}

}
