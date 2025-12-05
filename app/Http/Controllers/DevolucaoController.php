<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Cidade;
use App\Models\Fornecedor;
use App\Models\Produto;
use App\Models\Transportadora;
use App\Models\Empresa;
use App\Models\ItemNfe;
use App\Models\FaturaNfe;
use App\Models\NaturezaOperacao;
use Illuminate\Support\Facades\DB;
use App\Models\ProdutoLocalizacao;

class DevolucaoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:devolucao_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:devolucao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:devolucao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:devolucao_delete', ['only' => ['destroy']]);
    }

    private function setNumeroSequencial(){
        $docs = Nfe::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->get();

        $last = Nfe::where('empresa_id', request()->empresa_id)
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

    public function index(Request $request){

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $fornecedor_id = $request->get('fornecedor_id');
        $estado = $request->get('estado');
        $tpNF = $request->get('tpNF');
        $local_id = $request->get('local_id');

        $this->setNumeroSequencial();

        $data = Nfe::where('empresa_id', request()->empresa_id)->where('orcamento', 0)
        ->where('finNFe', 4)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($fornecedor_id), function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($tpNF != "", function ($query) use ($tpNF) {
            return $query->where('tpNF', $tpNF);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        return view('devolucao.index', compact('data'));

    }

    public function xml(){
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        return view('devolucao.xml');
    }

    public function storeXml(Request $request)
    {
        if ($request->hasFile('file')) {

            $arquivo = $request->hasFile('file');
            $file = $request->file;

            $xml = simplexml_load_file($request->file);

            if ($xml->NFe->infNFe == null) {
                session()->flash('flash_error', 'Este XML parece inválido!');
                return redirect()->back();
            }

            $chave = substr($xml->NFe->infNFe->attributes()->Id, 3, 44);
            $file->move(public_path('xml_entrada'), $chave . ".xml");

            $cidade = Cidade::where('codigo', $xml->NFe->infNFe->emit->enderEmit->cMun)
            ->first();

            $doc = $xml->NFe->infNFe->emit->CNPJ ? $xml->NFe->infNFe->emit->CNPJ : $xml->NFe->infNFe->emit->CPF;
            $doc = trim($doc);
            $mask = '##.###.###/####-##';
            if (strlen($doc) == 11) {
                $mask = '###.###.###-##';
            }
            $doc = __mask($doc, $mask);
            $empresa = Empresa::findOrFail($request->empresa_id);
            $dataFornecedor = [

                'empresa_id' => $request->empresa_id,
                'razao_social' => $xml->NFe->infNFe->emit->xNome,
                'nome_fantasia' => $xml->NFe->infNFe->emit->xFant,
                'cpf_cnpj' => $doc,
                'ie' => $xml->NFe->infNFe->emit->IE,
                'contribuinte' => $xml->NFe->infNFe->emit->IE != '' ? 1 : 0,
                'consumidor_final' => 0,
                'email' => $xml->NFe->infNFe->emit->enderEmit->xBairro,
                'telefone' => $xml->NFe->infNFe->emit->enderEmit->fone,
                'cidade_id' => $cidade->id,
                'rua' => $xml->NFe->infNFe->emit->enderEmit->xLgr,
                'cep' => $xml->NFe->infNFe->emit->enderEmit->CEP,
                'numero' => $xml->NFe->infNFe->emit->enderEmit->nro,
                'bairro' => $xml->NFe->infNFe->emit->enderEmit->xBairro,
                'complemento' => $xml->NFe->infNFe->emit->enderEmit->xBairro
            ];

            $fornecedor = $this->cadastraFornecedor($dataFornecedor);
            $vFrete = (float)$xml->NFe->infNFe->total->ICMSTot->vFrete;
            $vDesc = (float)$xml->NFe->infNFe->total->ICMSTot->vDesc;
            $vOutro = (float)$xml->NFe->infNFe->total->ICMSTot->vOutro;

            $itens = [];
            $contSemRegistro = 0;
            foreach ($xml->NFe->infNFe->det as $item) {

                $produto = Produto::verificaCadastrado(
                    $item->prod->cEAN,
                    $item->prod->xProd,
                    $item->prod->cProd,
                    $request->empresa_id
                );

                $vIpi = 0;
                $vICMSST = 0;
                if (isset($item->imposto->IPI)) {
                    $valor = (float)$item->imposto->IPI->IPITrib->vIPI;
                    if ($valor > 0)
                        $vIpi = $valor / (float)$item->prod->qCom;
                }

                if (isset($item->imposto->ICMS)) {
                    $arr = (array_values((array)$item->imposto->ICMS));
                    $cst = $arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN;
                    $valor = (float)$arr[0]->vICMSST ?? 0;
                    if ($valor > 0)
                        $vICMSST = $valor / $item->prod->qCom;
                }

                $nomeProduto = $item->prod->xProd;
                $nomeProduto = str_replace("'", "", $nomeProduto);
                $codigo = preg_replace('/[^0-9]/', '', $item->prod->cProd);

                if ($produto == null) {
                    $contSemRegistro++;
                }

                $prod = new \stdClass();

                $prod->id = $produto != null ? $produto->id : 0;
                $prod->codigo = $codigo;

                if (isset($item->imposto->ICMS)) {
                    $arr = (array_values((array)$item->imposto->ICMS));
                    $prod->modBCST = '';
                    $prod->pMVAST = '';
                    $prod->vBCST = '';
                    $prod->pICMSST = '';
                    $prod->vICMSST = '';
                    $prod->vBCFCPST = '';
                    $prod->pFCPST = '';
                    $prod->vFCPST = '';
                    $prod->vICMSSubstituto = '';
                    if(isset($arr[0]->modBCST)){
                        $prod->modBCST = (int)$arr[0]->modBCST;  
                    }
                    if(isset($arr[0]->pMVAST)){
                        $prod->pMVAST = (float)$arr[0]->pMVAST;  
                    }
                    if(isset($arr[0]->vBCST)){
                        $prod->vBCST = (float)$arr[0]->vBCST;  
                    }
                    if(isset($arr[0]->pICMSST)){
                        $prod->pICMSST = (float)$arr[0]->pICMSST;  
                    }
                    if(isset($arr[0]->vICMSST)){
                        $prod->vICMSST = (float)$arr[0]->vICMSST;  
                    }
                    if(isset($arr[0]->vBCFCPST)){
                        $prod->vBCFCPST = (float)$arr[0]->vBCFCPST;  
                    }
                    if(isset($arr[0]->pFCPST)){
                        $prod->pFCPST = (float)$arr[0]->pFCPST;  
                    }
                    if(isset($arr[0]->vFCPST)){
                        $prod->vFCPST = (float)$arr[0]->vFCPST;  
                    }
                    if(isset($arr[0]->vICMSSubstituto)){
                        $prod->vICMSSubstituto = (float)$arr[0]->vICMSSubstituto;  
                    }

                }

                $prod->xProd = $produto == null ? $nomeProduto : $produto->nome;
                $prod->ncm = (string)$item->prod->NCM;
                $prod->cest = (string)$item->prod->CEST;
                $prod->cfop = (string)$item->prod->CFOP;
                $prod->unidade = (string)$item->prod->uCom;
                // $prod->valor_unitario = number_format((float)$item->prod->vUnCom + $vIpi + $vICMSST, 2, '.', '');
                $prod->valor_unitario = number_format((float)$item->prod->vUnCom, 2, '.', '');
                $prod->quantidade = (float)$item->prod->qCom;
                $prod->sub_total = (float)$item->prod->vProd;
                $prod->codigo_barras = (string)$item->prod->cEAN;
                $prod->valor_venda = $produto == null ? 0 : $produto->valor_venda;
                $prod->valor_compra = $produto == null ? 0 : $produto->valor_compra;

                $arr = (array_values((array)$item->imposto->ICMS));
                $cst = (string)($arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN);
                $pICMS = (float)$arr[0]->pICMS ?? 0;
                $vbcICMS = (float)$arr[0]->vBC ?? 0;

                $prod->perc_red_bc = (float)$arr[0]->pRedBC ?? 0;
                $prod->origem = (int)$arr[0]->orig ?? 0;

                $prod->perc_icms = number_format($pICMS, 2);
                $prod->vbc_icms = $vbcICMS;
                $prod->cst_csosn = $cst;

                $arr = (array_values((array)$item->imposto->PIS));
                $prod->cst_pis = (string)$arr[0]->CST;
                $prod->perc_pis = (float)$arr[0]->pPIS ?? 0;
                $prod->vbc_pis = (float)$arr[0]->vBC ?? 0;

                $arr = (array_values((array)$item->imposto->COFINS));
                $prod->cst_cofins = $arr[0]->CST;
                $prod->perc_cofins = 0;
                if($arr[0]->pCOFINS){
                    $prod->perc_cofins = number_format((float)$arr[0]->pCOFINS, 2);
                }

                $prod->vbc_cofins = (float)$arr[0]->vBC ?? 0;

                $arr = (array_values((array)$item->imposto->IPI));
                if (isset($arr[1])) {

                    $cst_ipi = $arr[1]->CST ?? '99';
                    $pIPI = $arr[0]->IPI ?? 0;
                    if ($pIPI == 0) {
                        $pIPI = $arr[0]->pIPI ?? 0;
                    }

                    if (isset($arr[1]->pIPI)) {
                        $pIPI = $arr[1]->pIPI ?? 0;
                    } else {
                        if (isset($arr[4]->pIPI)) {
                            $ipi = $arr[4]->CST;
                            $pIPI = $arr[4]->pIPI;
                        } else {
                            $pIPI = 0;
                        }
                    }
                    $cEnq = '999';
                    if(is_numeric($arr[0])){
                        $cEnq = $arr[0];
                    }
                } else {
                    $cst_ipi = '99';
                    $pIPI = 0;
                    $cEnq = '999';
                }

                $prod->perc_ipi = $pIPI;
                $prod->cst_ipi = $cst_ipi;
                $prod->cEnq = $cEnq;
                $prod->vbc_ipi = 0;

                $prod->codigo_beneficio_fiscal = '';
                array_push($itens, $prod);
            }

            $dadosXml = [
                'chave' => $chave,
                'vProd' => (float)$xml->NFe->infNFe->total->ICMSTot->vNF,
                'indPag' => (int)$xml->NFe->infNFe->ide->indPag,
                'nNf' => (int)$xml->NFe->infNFe->ide->nNF,
                'vFrete' => $vFrete,
                'vDesc' => $vDesc,
                'vOutro' => $vOutro,
                'contSemRegistro' => $contSemRegistro,
                'data_emissao' => substr($xml->NFe->infNFe->ide->dhEmi[0], 0, 16),
                'itens' => $itens
            ];
            // dd($dadosXml);
            if (!is_dir(public_path('xml_entrada'))) {
                mkdir(public_path('xml_entrada'), 0777, true);
            }

            $fatura = [];
            if (!empty($xml->NFe->infNFe->cobr->dup)) {
                foreach ($xml->NFe->infNFe->cobr->dup as $dup) {
                    $titulo = $dup->nDup;
                    $vencimento = $dup->dVenc;
                    $vencimento = explode('-', $vencimento);
                    $vencimento = $vencimento[2] . "/" . $vencimento[1] . "/" . $vencimento[0];
                    $valor_parcela = number_format((float) $dup->vDup, 2, ".", "");
                    $parcela = [
                        'numero' => (int)$titulo,
                        'vencimento' => $vencimento,
                        'valor_parcela' => $valor_parcela,
                        'rand' => rand(0, 10000)
                    ];
                    array_push($fatura, $parcela);
                }
            } else {
                $vencimento = explode('-', substr($xml->NFe->infNFe->ide->dhEmi[0], 0, 10));
                $vencimento = $vencimento[2] . "/" . $vencimento[1] . "/" . $vencimento[0];
                $parcela = [
                    'numero' => 1,
                    'vencimento' => $vencimento,
                    'valor_parcela' => (float)$xml->NFe->infNFe->total->ICMSTot->vProd,
                    'rand' => rand(0, 10000)
                ];
                array_push($fatura, $parcela);
            }

            $dadosXml['fatura'] = $fatura;

            $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();
            $cidades = Cidade::all();
            $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
            if (sizeof($naturezas) == 0) {
                session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
                return redirect()->route('natureza-operacao.create');
            }
            $caixa = __isCaixaAberto();
            $empresa = __objetoParaEmissao($empresa, $caixa->local_id);

            $numeroNfe = Nfe::lastNumero($empresa);

            $transporta = null;
            // dd($xml->NFe->infNFe->transp->transporta);
            if($xml->NFe->infNFe->transp->transporta){
                $transporta['nome'] = (string)$xml->NFe->infNFe->transp->transporta->xNome;
                $transporta['cnpj'] = (string)$xml->NFe->infNFe->transp->transporta->CNPJ;
                $transporta['ie'] = (string)$xml->NFe->infNFe->transp->transporta->IE;
                $endereco = (string)$xml->NFe->infNFe->transp->transporta->xEnder;
                $endereco = explode(",", $endereco);

                $transporta['rua'] = $endereco[0];
                $transporta['numero'] = isset($endereco[1]) ? $endereco[1] : '';

                $cidade = Cidade::where('nome', (string)$xml->NFe->infNFe->transp->transporta->xMun)
                ->where('uf', (string)$xml->NFe->infNFe->transp->transporta->UF)
                ->first();
                $transporta['cidade'] = $cidade != null ? [$cidade->id => $cidade->info] : null;

            }
            // dd($transporta);

            return view('devolucao.import_xml', compact('dadosXml', 'transportadoras', 'cidades', 'naturezas', 'fornecedor', 
                'numeroNfe', 'caixa', 'transporta'));
        } else {
            session()->flash('flash_error', 'XML inválido!');
            return redirect()->back();
        }
    }

    private function cadastraFornecedor($dataFornecedor)
    {
        $fornecedor = Fornecedor::where('cpf_cnpj', $dataFornecedor['cpf_cnpj'])
        ->where('empresa_id', request()->empresa_id)->first();

        if ($fornecedor == null) {
            $fornecedor = Fornecedor::create($dataFornecedor);
        }

        return $fornecedor;
    }

    public function finishXml(Request $request)
    {
        try {

            $nfe = DB::transaction(function () use ($request) {

                $fornecedor_id = isset($request->fornecedor_id) ? $request->fornecedor_id : null;
                $empresa = Empresa::findOrFail($request->empresa_id);

                if (isset($request->fornecedor_id)) {
                    if ($request->fornecedor_id == null) {
                        $fornecedor_id = $this->cadastrarFornecedor($request);
                    } else {
                        $this->atualizaFornecedor($request);
                    }
                }
                $transportadora_id = $request->transportadora_id;
                if ($request->transportadora_id == null) {
                    $transportadora_id = $this->cadastrarTransportadora($request);
                } else {
                    $this->atualizaTransportadora($request);
                }
                $config = Empresa::find($request->empresa_id);

                $caixa = __isCaixaAberto();
                $local_id = $caixa->local_id;
                if(isset($request->local_id)){
                    $local_id = $request->local_id;
                }
                $tiposPagamento = $request->tipo_pagamento;
                $request->merge([
                    'emissor_nome' => $config->nome,
                    'emissor_cpf_cnpj' => $config->cpf_cnpj,
                    'ambiente' => $config->ambiente,
                    'chave' => '',
                    'fornecedor_id' => $fornecedor_id,
                    'transportadora_id' => $transportadora_id,
                    'numero_serie' => $empresa->numero_serie_nfe ? $empresa->numero_serie_nfe : 0,
                    'numero' => $request->numero_nfe ? $request->numero_nfe : 0,
                    'chave_importada' => $request->chave_importada,
                    'estado' => 'novo',
                    'total' => __convert_value_bd($request->valor_total),
                    'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                    'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                    'valor_produtos' => __convert_value_bd($request->valor_produtos),
                    'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                    'caixa_id' => $caixa ? $caixa->id : null,
                    'local_id' => $local_id,
                    'tipo_pagamento' => isset($request->tipo_pagamento[0]) ? $request->tipo_pagamento[0] : null,
                    'user_id' => \Auth::user()->id
                ]);

                // dd($request->tipo_pagamento[]);
                $nfe = Nfe::create($request->all());
                for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                    if ($request->produto_id[$i] == 0) {
                        //cadastrar produto
                        $product = $this->cadastrarProduto($request, $i, $local_id);
                    } else {
                        $product = Produto::findOrFail($request->produto_id[$i]);
                    }
                    // dd($request->all());
                    ItemNfe::create([
                        'nfe_id' => $nfe->id,
                        'produto_id' => $product->id,
                        'quantidade' => __convert_value_bd($request->quantidade[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
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

                        'vbc_icms' => __convert_value_bd($request->vbc_icms[$i]),
                        'vbc_pis' => __convert_value_bd($request->vbc_pis[$i]),
                        'vbc_cofins' => __convert_value_bd($request->vbc_cofins[$i]),
                        'vbc_ipi' => __convert_value_bd($request->vbc_ipi[$i]),
                        'cEnq' => $request->cEnq[$i],


                        'pMVAST' => $request->pMVAST[$i] ?? '',
                        'vBCST' => $request->vBCST[$i] ?? '',
                        'pICMSST' => $request->pICMSST[$i] ?? '',
                        'vICMSST' => $request->vICMSST[$i] ?? '',
                        'vBCFCPST' => $request->vBCFCPST[$i] ?? '',
                        'pFCPST' => $request->pFCPST[$i] ?? '',
                        'vFCPST' => $request->vFCPST[$i] ?? '',
                        'modBCST' => $request->modBCST[$i] ?? '',
                        'vICMSSubstituto' => $request->vICMSSubstituto[$i] ?? '',

                    ]);

                }

                if($tiposPagamento && sizeof($tiposPagamento) > 0){

                    for ($i = 0; $i < sizeof($tiposPagamento); $i++) {
                        if ($tiposPagamento[$i]) {
                            FaturaNfe::create([
                                'nfe_id' => $nfe->id,
                                'tipo_pagamento' => $tiposPagamento[$i],
                                'data_vencimento' => $request->data_vencimento[$i],
                                'valor' => __convert_value_bd($request->valor_fatura[$i])
                            ]);
                        }

                    }
                }
                return $nfe;
            });
$descricaoLog = $nfe->fornecedor->info . " R$ " . __moeda($nfe->total);
__createLog($request->empresa_id, 'Devolução XML', 'cadastrar', $descricaoLog);
session()->flash("flash_success", "Importação de devolução cadastrada!");
} catch (\Exception $e) {
    // echo $e->getMessage() . '<br>' . $e->getLine();
    // die;
    __createLog(request()->empresa_id, 'Devolução XML', 'erro', $e->getMessage());
    session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
}

return redirect()->route('devolucao.index');
}

private function atualizaFornecedor($request)
{
    $fornecedor = Fornecedor::findOrFail($request->fornecedor_id);
    $fornecedor->update([
        'razao_social' => $request->fornecedor_nome,
        'nome_fantasia' => $request->nome_fantasia,
        'cpf_cnpj' => $request->fornecedor_cpf_cnpj,
        'ie' => $request->ie,
        'contribuinte' => $request->contribuinte,
        'consumidor_final' => $request->consumidor_final,
        'email' => $request->email ?? '',
        'telefone' => $request->telefone ?? '',
        'cidade_id' => $request->fornecedor_cidade,
        'rua' => $request->fornecedor_rua,
        'cep' => $request->cep,
        'numero' => $request->fornecedor_numero,
        'bairro' => $request->fornecedor_bairro,
        'complemento' => $request->complemento
    ]);
    return $fornecedor->id;
}

private function cadastrarTransportadora($request)
{
    if ($request->razao_social_transp) {
        $transportadora = Transportadora::create([
            'empresa_id' => $request->empresa_id,
            'razao_social' => $request->razao_social_transp,
            'nome_fantasia' => $request->nome_fantasia_transp ?? '',
            'cpf_cnpj' => $request->cpf_cnpj_transp,
            'ie' => $request->ie_transp,
            'antt' => $request->antt,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'cidade_id' => $request->cidade_id,
            'rua' => $request->rua_transp,
            'cep' => $request->cep_transp ?? '',
            'numero' => $request->numero_transp ?? '',
            'bairro' => $request->bairro_transp ?? '',
            'complemento' => $request->complemento_transp
        ]);
        return $transportadora->id;
    }
    return null;
}

private function atualizaTransportadora($request)
{
    if ($request->razao_social_transp) {
        $transportadora = Transportadora::findOrFail($request->transportadora_id);
        $transportadora->update([
            'empresa_id' => $request->empresa_id,
            'razao_social' => $request->razao_social_transp,
            'nome_fantasia' => $request->nome_fantasia_transp ?? '',
            'cpf_cnpj' => $request->cpf_cnpj_transp,
            'ie' => $request->ie_transp,
            'antt' => $request->antt,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'cidade_id' => $request->cidade_id,
            'rua' => $request->rua_transp,
            'cep' => $request->cep_transp ?? '',
            'numero' => $request->numero_transp,
            'bairro' => $request->bairro_transp ?? '',
            'complemento' => $request->complemento_transp
        ]);
        return $transportadora->id;
    }
    return null;
}

private function cadastrarProduto($request, $i, $local_id)
{
        // dd($request);
    $cfop = $request->cfop[$i];
    $cfopOutroEstado = '';
    $cfopEstado = '';
    $digito = substr($cfop, 0, 1);

    $cfopEstado = '5' . substr($cfop, 1, 4);
    $cfopOutroEstado = '6' . substr($cfop, 1, 4);

    $p = Produto::create([
        'empresa_id' => $request->empresa_id,
        'nome' => $request->nome_produto[$i],
        'origem' => $request->origem[$i],
        'ncm' => $request->ncm[$i],
        'gerenciar_estoque' => 0,
        'unidade' => $request->unidade[$i],
        'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
        'perc_red_bc' => __convert_value_bd($request->perc_red_bc[$i]),
        'cfop_estadual' => $cfopEstado,
        'cfop_outro_estado' => $cfopOutroEstado,
        'valor_compra' => $request->valor_compra ? __convert_value_bd($request->valor_compra) : 0,

        'perc_red_bc' => __convert_value_bd($request->perc_red_bc[$i]),
        'cst_csosn' => $request->cst_csosn[$i],
        'cst_pis' => $request->cst_pis[$i],
        'cst_cofins' => $request->cst_cofins[$i],
        'cst_ipi' => $request->cst_ipi[$i],

        'perc_icms' => __convert_value_bd($request->perc_icms[$i]),
        'perc_pis' => __convert_value_bd($request->perc_pis[$i]),
        'perc_cofins' => __convert_value_bd($request->perc_cofins[$i]),
        'perc_ipi' => __convert_value_bd($request->perc_ipi[$i]),
        'valor_prazo' => 0
    ]);

    ProdutoLocalizacao::updateOrCreate([
        'produto_id' => $p->id, 
        'localizacao_id' => $local_id
    ]);
    return $p;
}

public function destroy($id)
{
    $item = Nfe::findOrFail($id);
    try {

        $descricaoLog = $item->fornecedor->info . " R$ " . __moeda($item->total);
        $item->itens()->delete();
        $item->fatura()->delete();
        $item->delete();
        __createLog(request()->empresa_id, 'Devolução XML', 'excluir', $descricaoLog);
        session()->flash("flash_success", "Devolução removida!");
    } catch (\Exception $e) {
        // echo $e->getLine();
        // die;
        __createLog(request()->empresa_id, 'Devolução XML', 'erro', $e->getMessage());
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->route('devolucao.index');
}

public function edit($id)
{
    $item = Nfe::findOrFail($id);
    $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();
    $cidades = Cidade::all();
    $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();

    if($item->cliente){
        return redirect()->route('nfe.edit', [$id]);
    }

    $transporta = null;
    return view('devolucao.edit', compact('item', 'cidades', 'transportadoras', 'naturezas', 'transporta'));
}

public function update(Request $request, $id)
{
    $nfe = Nfe::findOrFail($id);
    try {

        $nfe = DB::transaction(function () use ($request, $nfe) {
            $this->atualizaFornecedor($request);
            $this->atualizaTransportadora($request);
            $tiposPagamento = $request->tipo_pagamento;
            $request->merge([

                'total' => __convert_value_bd($request->valor_total),
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'valor_produtos' => __convert_value_bd($request->valor_produtos),
                'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                'tipo_pagamento' => isset($request->tipo_pagamento[0]) ? $request->tipo_pagamento[0] : null,
            ]);

            $nfe->fill($request->all())->save();
            $nfe->itens()->delete();
            for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                if ($request->produto_id[$i] == 0) {
                        //cadastrar produto
                    $product = $this->cadastrarProduto($request, $i, $nfe->local_id);
                } else {
                    $product = Produto::findOrFail($request->produto_id[$i]);
                }

                ItemNfe::create([
                    'nfe_id' => $nfe->id,
                    'produto_id' => $product->id,
                    'quantidade' => __convert_value_bd($request->quantidade[$i]),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
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
                    'vbc_icms' => __convert_value_bd($request->vbc_icms[$i]),
                    'vbc_pis' => __convert_value_bd($request->vbc_pis[$i]),
                    'vbc_cofins' => __convert_value_bd($request->vbc_cofins[$i]),
                    'vbc_ipi' => __convert_value_bd($request->vbc_ipi[$i]),
                    'cEnq' => $request->cEnq[$i],

                    'pMVAST' => $request->pMVAST[$i] ?? '',
                    'vBCST' => $request->vBCST[$i] ?? '',
                    'pICMSST' => $request->pICMSST[$i] ?? '',
                    'vICMSST' => $request->vICMSST[$i] ?? '',
                    'vBCFCPST' => $request->vBCFCPST[$i] ?? '',
                    'pFCPST' => $request->pFCPST[$i] ?? '',
                    'vFCPST' => $request->vFCPST[$i] ?? '',
                    'modBCST' => $request->modBCST[$i] ?? '',
                    'vICMSSubstituto' => $request->vICMSSubstituto[$i] ?? '',
                ]);

            }
            
            if($tiposPagamento && sizeof($tiposPagamento) > 0){
                $nfe->fatura()->delete();

                for ($i = 0; $i < sizeof($tiposPagamento); $i++) {
                    if ($tiposPagamento[$i]) {
                        FaturaNfe::create([
                            'nfe_id' => $nfe->id,
                            'tipo_pagamento' => $tiposPagamento[$i],
                            'data_vencimento' => $request->data_vencimento[$i],
                            'valor' => __convert_value_bd($request->valor_fatura[$i])
                        ]);
                    }

                }
            }
            return $nfe;
        });
$descricaoLog = $nfe->fornecedor->info . " R$ " . __moeda($nfe->total);
__createLog($request->empresa_id, 'Devolução XML', 'editar', $descricaoLog);
session()->flash("flash_success", "Devolução atualizada!");
} catch (\Exception $e) {
        // echo $e->getMessage() . '<br>' . $e->getLine();
        // die;
    __createLog(request()->empresa_id, 'Devolução XML', 'erro', $e->getMessage());
    session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
}
return redirect()->route('devolucao.index');
}

}
