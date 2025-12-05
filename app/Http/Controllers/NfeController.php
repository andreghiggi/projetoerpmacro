<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Fornecedor;
use App\Models\Cliente;
use App\Models\ProdutoFornecedor;
use App\Models\Empresa;
use App\Models\ProdutoUnico;
use App\Models\FaturaNfe;
use App\Models\ItemNfe;
use App\Models\Troca;
use App\Models\ItemDimensaoNfe;
use App\Models\ProdutoLocalizacao;
use App\Models\NaturezaOperacao;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Garantia;
use App\Models\Nfce;
use App\Models\MetaResultado;
use App\Models\EmailConfig;
use App\Models\PedidoEcommerce;
use App\Models\ComissaoVenda;
use App\Models\Cotacao;
use App\Models\Reserva;
use App\Models\Produto;
use App\Models\ConfigGeral;
use App\Models\PedidoVendiZap;
use App\Models\Inutilizacao;
use App\Models\Transportadora;
use App\Models\Funcionario;
use App\Models\MargemComissao;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Laravel\Ui\Presets\React;
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\NFe\DanfeSimples;
use NFePHP\DA\NFe\DanfeEtiqueta;
use App\Services\NFeService;
use NFePHP\DA\NFe\Daevento;
use App\Utils\EstoqueUtil;
use App\Models\ContaReceber;
use App\Models\ContaPagar;
use App\Models\NuvemShopPedido;
use App\Models\WoocommercePedido;
use App\Models\OrdemServico;
use App\Models\PedidoMercadoLivre;
use Dompdf\Dompdf;
use File;
use App\Models\Contigencia;
use App\Utils\EmailUtil;
use Mail;
use Illuminate\Support\Str;
use App\Utils\SiegUtil;
use App\Utils\FilaEnvioUtil;
use App\Models\ItemProducao;

class NfeController extends Controller
{
    protected $util;
    protected $emailUtil;
    protected $siegUtil;
    protected $filaEnvioUtil;

    public function __construct(EstoqueUtil $util, EmailUtil $emailUtil, SiegUtil $siegUtil, FilaEnvioUtil $filaEnvioUtil)
    {
        $this->util = $util;
        $this->emailUtil = $emailUtil;
        $this->siegUtil = $siegUtil;
        $this->filaEnvioUtil = $filaEnvioUtil;

        if (!is_dir(public_path('xml_nfe'))) {
            mkdir(public_path('xml_nfe'), 0777, true);
        }
        if (!is_dir(public_path('xml_nfe_cancelada'))) {
            mkdir(public_path('xml_nfe_cancelada'), 0777, true);
        }
        if (!is_dir(public_path('xml_nfe_correcao'))) {
            mkdir(public_path('xml_nfe_correcao'), 0777, true);
        }
        if (!is_dir(public_path('danfe_temp'))) {
            mkdir(public_path('danfe_temp'), 0777, true);
        }

        if (!is_dir(public_path('danfe'))) {
            mkdir(public_path('danfe'), 0777, true);
        }

        // $this->middleware('permission:nfe_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:nfe_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:nfe_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:nfe_delete', ['only' => ['destroy']]);
    }

    private function setNumeroSequencial(){
        $docs = Nfe::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->where('orcamento', 0)
        ->get();

        $last = Nfe::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('orcamento', 0)
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
        ->where('documento', 'NFe')
        ->first();
        return $active;
    }

    private function corrigeNumeros($empresa_id){
        $config = ConfigGeral::where('empresa_id', $empresa_id)->first();
        if($config != null && $config->corrigir_numeracao_fiscal == 0){
            return;
        }
        $empresa = Empresa::findOrFail($empresa_id);
        $caixa = __isCaixaAberto();
        if($caixa){
            $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
        }else{
            return;
        }

        if($empresa->ambiente == 1){
            $numero = $empresa->numero_ultima_nfe_producao;
        }else{
            $numero = $empresa->numero_ultima_nfe_homologacao;
        }

        if($numero){
            Nfe::where(function($q) {
                $q->where('estado', 'novo')->orWhere('estado', 'rejeitado');
            })
            ->where('empresa_id', $empresa_id)
            // ->when($caixa, function ($query) use ($caixa) {
            //     return $query->where('caixa_id', $caixa->id);
            // })
            ->where('chave_importada', null)
            ->update(['numero' => $numero+1]);
        }
    }

    public function index(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $estado = $request->get('estado');
        $tpNF = $request->get('tpNF');
        $local_id = $request->get('local_id');
        $numero_nfe = $request->get('numero_nfe');

        $this->corrigeNumeros(request()->empresa_id);

        $this->setNumeroSequencial();
        if ($tpNF == "") {
            $tpNF = 1;
        }

        $teste = Nfe::where('empresa_id', request()->empresa_id)->where('orcamento', 0)->get();

        $somaGeral = 0;
        $query = Nfe::where('empresa_id', request()->empresa_id)->where('orcamento', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($tpNF != "-", function ($query) use ($tpNF) {
            return $query->where('tpNF', $tpNF);
        })
        ->when($numero_nfe, function ($query) use ($numero_nfe) {
            return $query->where('numero', $numero_nfe);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->orderBy('created_at', 'desc');

        $somaGeral = $query->sum('total');
        $data = $query->paginate(__itensPagina());

        $contigencia = $this->getContigencia($request->empresa_id);

        $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();
        $envioWppLink = 0;
        if($config != null && $config->status_wpp_link){
            $envioWppLink = 1;
        }
        return view('nfe.index', compact('data', 'contigencia', 'somaGeral', 'envioWppLink'));
    }

    public function create(Request $request)
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $clientes = Cliente::where('empresa_id', request()->empresa_id)->count();
        if ($clientes == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um cliente!");
            return redirect()->route('clientes.create');
        }
        $sizeProdutos = Produto::where('empresa_id', request()->empresa_id)->count();
        if ($sizeProdutos == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um produto!");
            return redirect()->route('produtos.create');
        }
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();
        $cidades = Cidade::all();
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        }
        $empresa = Empresa::findOrFail(request()->empresa_id);
        $caixa = __isCaixaAberto();
        $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
        $numeroNfe = Nfe::lastNumero($empresa);

        $isOrcamento = 0;
        if(isset($request->orcamento)){
            $isOrcamento = 1;
        }

        $naturezaPadrao = NaturezaOperacao::where('empresa_id', request()->empresa_id)
        ->where('padrao', 1)->first();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        return view('nfe.create', 
            compact('transportadoras', 'cidades', 'naturezas', 'numeroNfe', 'empresa', 'caixa', 
                'isOrcamento', 'naturezaPadrao', 'config', 'funcionarios')
        );
    }

    public function edit($id)
    {
        $item = Nfe::findOrFail($id);
        if(sizeof($item->contasReceber) > 0){
            if ($item->contasReceber()->where('status', 1)->exists()) {
                session()->flash("flash_warning", "Não é possível editar venda com 1 ou mais faturas recebidas!");
                return redirect()->back();
            }
        }

        $troca = Troca::where('nfe_id', $id)->first();

        if($troca != null){
            session()->flash("flash_warning", "Não é possível editar venda com uma troca!");
            return redirect()->back();
        }

        __validaObjetoEmpresa($item);
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();
        $cidades = Cidade::all();
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        $caixa = __isCaixaAberto();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        return view('nfe.edit', compact('item', 'transportadoras', 'cidades', 'naturezas', 'caixa', 'funcionarios', 'config'));
    }

    public function duplicar($id)
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();
        $cidades = Cidade::all();
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        $caixa = __isCaixaAberto();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        return view('nfe.duplicar', compact('item', 'transportadoras', 'cidades', 'naturezas', 'caixa', 'funcionarios',
            'config'));
    }

    public function imprimir($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);

        $empresa = $item->empresa;
        if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfe/') . $item->chave . '.xml');

            $danfe = new Danfe($xml);
            if($empresa->logo){
                $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . 
                    $empresa->logo));
                $danfe->logoParameters($logo, 'L');
            }
            $danfe->exibirTextoFatura = 1;
            $pdf = $danfe->render();
            header("Content-Disposition: ; filename=DANFE $item->numero.pdf");
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function downloadXml($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);

        if($item->estado == 'aprovado'){
            if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
                return response()->download(public_path('xml_nfe/') . $item->chave . '.xml');
            } else {
                session()->flash("flash_error", "Arquivo não encontrado");
                return redirect()->back();
            }
        }elseif($item->estado == 'cancelado'){
            if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
                return response()->download(public_path('xml_nfe_cancelada/') . $item->chave . '.xml');
            } else {
                session()->flash("flash_error", "Arquivo não encontrado");
                return redirect()->back();
            }
        }else{
            session()->flash("flash_error", "Nada encontrado");
            return redirect()->back();
        }
    }

    public function danfeSimples($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);

        if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfe/') . $item->chave . '.xml');
            try {
                $danfe = new DanfeSimples($xml);
                $danfe->debugMode(false);
                $pdf = $danfe->render();
                return response($pdf)
                ->header('Content-Type', 'application/pdf');
            } catch (InvalidArgumentException $e) {
                echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
            }
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function danfeEtiqueta($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);

        if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfe/') . $item->chave . '.xml');
            try {
                $danfe = new DanfeEtiqueta($xml);
                $danfe->debugMode(false);
                $pdf = $danfe->render();
                return response($pdf)
                ->header('Content-Type', 'application/pdf');
            } catch (InvalidArgumentException $e) {
                echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
            }
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function imprimirCancela($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);

        $xml = file_get_contents(public_path('xml_nfe_cancelada/') . $item->chave . '.xml');
        $dadosEmitente = $this->getEmitente($item->empresa);

        $daevento = new Daevento($xml, $dadosEmitente);
        $daevento->debugMode(true);
        $pdf = $daevento->render();
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

    public function imprimirCorrecao($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresa($item);

        $xml = file_get_contents(public_path('xml_nfe_correcao/') . $item->chave . '.xml');
        $dadosEmitente = $this->getEmitente($item->empresa);
        $daevento = new Daevento($xml, $dadosEmitente);
        $daevento->debugMode(true);
        $pdf = $daevento->render();
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

    private function getEmitente($empresa)
    {
        return [
            'razao' => $empresa->nome,
            'logradouro' => $empresa->rua,
            'numero' => $empresa->numero,
            'complemento' => '',
            'bairro' => $empresa->bairro,
            'CEP' => preg_replace('/[^0-9]/', '', $empresa->cep),
            'municipio' => $empresa->cidade->nome,
            'UF' => $empresa->cidade->uf,
            'telefone' => $empresa->telefone,
            'email' => ''
        ];
    }

    private function validaCreditoCliente($request){

        $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();
        if($config == null || $config->limitar_credito_cliente == 0){
            return 0;
        }
        
        if ($request->tpNF == 0) {
            return 0;
        }

        if(!isset($request->tipo_pagamento)){
            return 0;
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        $faturaPrazo = 0;
        $total = 0;
        if($request->tipo_pagamento){
            for ($i = 0; $i < sizeof($request->tipo_pagamento); $i++) {
                $vencimento = $request->data_vencimento[$i];
                $dataAtual = date('Y-m-d');
                if(strtotime($vencimento) > strtotime($dataAtual)){
                    $faturaPrazo = 1;
                    $total += __convert_value_bd($request->valor_fatura[$i]);
                }
            }
        }

        if($faturaPrazo == 0){
            return 0;
        }

        if($cliente->limite_credito == null || $cliente->limite_credito == 0){
            return "Cliente sem limite de crédito definido!";
        }

        $somaPendente = ContaReceber::where('cliente_id', $cliente->id)
        ->where('status', 0)->sum('valor_integral');
        $somaPendente += $total;
        if($somaPendente > $cliente->limite_credito){
            return "Limite de crédito do cliente ultrapassou em R$ " . __moeda($somaPendente-$cliente->limite_credito) . 
            " - Total de crédito definido para este cliente R$ " . __moeda($cliente->limite_credito);
        }
        return 0;
    }

    private function getLastNumeroNfe($empresa_id){
        $last = Nfe::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    public function store(Request $request)
    {
        try {
            $retornoCredito = $this->validaCreditoCliente($request);
            if($retornoCredito != 0){

                session()->flash("flash_error", $retornoCredito);
                return redirect()->back();
            }
            $nfe = DB::transaction(function () use ($request) {
                $cliente_id = isset($request->cliente_id) ? $request->cliente_id : null;
                $fornecedor_id = isset($request->fornecedor_id) ? $request->fornecedor_id : null;
                $empresa = Empresa::findOrFail($request->empresa_id);

                if (isset($request->cliente_id)) {
                    if ($request->cliente_id == null) {
                        $cliente_id = $this->cadastrarCliente($request);
                    } else {
                        $this->atualizaCliente($request);
                    }
                }
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

                $tipoPagamento = $request->tipo_pagamento;

                $caixa = __isCaixaAberto();

                $local_id = $caixa->local_id;
                if(isset($request->local_id)){
                    $local_id = $request->local_id;
                }
                $valor_produto =  number_format($request->valor_produtos, 2);

                if($caixa != null){
                    $empresa = __objetoParaEmissao($empresa, $local_id);
                }
                $request->merge([
                    'emissor_nome' => $config->nome,
                    'emissor_cpf_cnpj' => $config->cpf_cnpj,
                    'ambiente' => $config->ambiente,
                    'chave' => '',
                    'cliente_id' => $cliente_id,
                    'fornecedor_id' => $fornecedor_id,
                    'transportadora_id' => $transportadora_id,
                    'numero_serie' => $empresa->numero_serie_nfe ? $empresa->numero_serie_nfe : 0,
                    'numero_sequencial' => $this->getLastNumeroNfe($request->empresa_id),
                    'numero' => $request->numero_nfe ? $request->numero_nfe : 0,
                    'estado' => 'novo',
                    'total' => __convert_value_bd($request->valor_total),
                    'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                    'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                    'valor_produtos' => __convert_value_bd($valor_produto),
                    'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                    'caixa_id' => $caixa ? $caixa->id : null,
                    'local_id' => $local_id,
                    // 'numero' => $request->numero ?? 0,
                    'tipo_pagamento' => $request->tipo_pagamento[0],
                    'user_id' => \Auth::user()->id,
                    'tpNF' => isset($request->is_compra) ? 0 : 1
                    // 'bandeira_cartao' => $request->bandeira_cartao ?? null,
                    // 'cnpj_cartao' => $request->cnpj_cartao ?? null,
                    // 'cAut_cartao' => $request->cAut_cartao ?? null
                ]);

                if($request->orcamento){
                    $request->merge([
                        'gerar_conta_receber' => 0,
                    ]);
                }

                $nfe = Nfe::create($request->all());

                for ($i = 0; $i < sizeof($request->produto_id); $i++) {

                    $product = Produto::findOrFail($request->produto_id[$i]);
                    $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;
                    
                    $itemNfe = ItemNfe::create([
                        'nfe_id' => $nfe->id,
                        'produto_id' => (int)$request->produto_id[$i],
                        'descricao' => $request->descricao[$i] ?? '',
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
                        'variacao_id' => $variacao_id,
                        'cEnq' => $product->cEnq,
                        'xPed' => $request->xPed[$i],
                        'nItemPed' => $request->nItemPed[$i],
                        'infAdProd' => $request->infAdProd[$i],
                    ]);

                    if($request->orcamento == 0 && $product->tipo_producao){
                        if(isset($request->dimensao_largura)){
                            for($l=0; $l<sizeof($request->dimensao_largura); $l++){
                                if($request->_key[$i] == $request->_line[$l]){
                                    ItemProducao::create([
                                        'produto_id' => $itemNfe->produto_id,
                                        'dimensao' => $request->dimensao_altura[$l] . ' x ' . $request->dimensao_largura[$l],
                                        'quantidade' => $request->dimensao_quantidade[$l],
                                        'status' => 0,
                                        'item_id' => $itemNfe->id,
                                        'observacao' => $request->dimensao_observacao[$l] ?? ''
                                    ]);
                                }
                            }
                        }else{
                            ItemProducao::create([
                                'produto_id' => $itemNfe->produto_id,
                                'quantidade' => $itemNfe->quantidade,
                                'status' => 0,
                                'item_id' => $itemNfe->id,
                                'observacao' => '',
                                'dimensao' => ''
                            ]);
                        }
                    }

                    if($product->prazo_garantia > 0){
                        Garantia::create([
                            'empresa_id' => $request->empresa_id,
                            'cliente_id' => $nfe->cliente_id,
                            'produto_id' => $product->id,
                            'nfe_id' => $nfe->id,
                            'usuario_id' => \Auth::user()->id,
                            'prazo_garantia' => $product->prazo_garantia,
                            'data_venda' => date('Y-m-d')
                        ]);
                    }

                    if (isset($request->is_compra) && isset($request->dados_importacao)) {

                        $dadosImportacao = json_decode($request->dados_importacao[$i]);
                        if($dadosImportacao){
                            $itemNfe->nDI = $dadosImportacao->nDI;
                            $itemNfe->dDI = $dadosImportacao->dDI;
                            $itemNfe->cidade_desembarque_id = $dadosImportacao->cidade_desembarque_id;
                            $itemNfe->dDesemb = $dadosImportacao->dDesemb;
                            $itemNfe->tpViaTransp = $dadosImportacao->tpViaTransp;
                            $itemNfe->vAFRMM = __convert_value_bd($dadosImportacao->vAFRMM);
                            $itemNfe->tpIntermedio = $dadosImportacao->tpIntermedio;
                            $itemNfe->cpf_cnpj_di = $dadosImportacao->cpf_cnpj_di;
                            $itemNfe->UFTerceiro = $dadosImportacao->UFTerceiro;
                            $itemNfe->cExportador = $dadosImportacao->cExportador;
                            $itemNfe->nAdicao = $dadosImportacao->nAdicao;
                            $itemNfe->cFabricante = $dadosImportacao->cFabricante;

                            $itemNfe->vBCII = __convert_value_bd($dadosImportacao->vBCII);
                            $itemNfe->vDespAdu = __convert_value_bd($dadosImportacao->vDespAdu);
                            $itemNfe->vII = __convert_value_bd($dadosImportacao->vII);
                            $itemNfe->vIOF = __convert_value_bd($dadosImportacao->vIOF);

                            $itemNfe->save();
                        }
                    }

                    // salvar dimensoes
                    // if($i == $request->_key[$i]){

                    // }
                    // dd($request->all());
                    if(isset($request->dimensao_largura)){
                        for($l=0; $l<sizeof($request->dimensao_largura); $l++){
                            if($request->_key[$i] == $request->_line[$l]){

                                ItemDimensaoNfe::create([
                                    'item_nfe_id' => $itemNfe->id,
                                    'valor_unitario_m2' => __convert_value_bd($request->dimensao_valor_unitario_m2[$l]),
                                    'largura' => $request->dimensao_largura[$l],
                                    'altura' => $request->dimensao_altura[$l],
                                    'quantidade' => $request->dimensao_quantidade[$l],
                                    'm2_total' => $request->dimensao_m2_total[$l],
                                    'espessura' => $request->dimensao_espessura[$l] ?? 0,
                                    'observacao' => $request->dimensao_observacao[$l] ?? '',
                                    'sub_total' => __convert_value_bd($request->dimensao_sub_total[$l])
                                ]);
                            }
                        }
                    }


                    if (isset($request->is_compra)) {

                        $product->valor_compra = __convert_value_bd($request->valor_unitario[$i]);
                        $product->save();

                        ProdutoFornecedor::updateOrCreate([
                            'produto_id' => $product->id,
                            'fornecedor_id' => $fornecedor_id
                        ]);
                    }

                    if ($product->gerenciar_estoque && $request->orcamento == 0 && $nfe->natureza->movimentar_estoque == 1) {
                        if (isset($request->is_compra)) {

                            $this->util->incrementaEstoque($product->id, __convert_value_bd($request->quantidade[$i]), 
                                $variacao_id, $local_id);
                        } else {
                            $this->util->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), 
                                $variacao_id, $local_id);
                        }
                    }

                    if($request->orcamento == 0 && $product->gerenciar_estoque && $nfe->natureza->movimentar_estoque == 1){
                        if ($request->is_compra) {

                            $tipo = 'incremento';
                            $codigo_transacao = $nfe->id;
                            $tipo_transacao = 'compra';
                            $this->util->movimentacaoProduto($product->id, __convert_value_bd($request->quantidade[$i]), $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao_id);
                        } else {
                            $tipo = 'reducao';
                            $codigo_transacao = $nfe->id;
                            $tipo_transacao = 'venda_nfe';
                            $this->util->movimentacaoProduto($product->id, __convert_value_bd($request->quantidade[$i]), $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao_id);
                        }
                    }
                }

                if($request->tipo_pagamento){
                    if ($request->tipo_pagamento[0] != '' && $request->valor_fatura[0] != '') {
                        for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                            FaturaNfe::create([
                                'nfe_id' => $nfe->id,
                                'tipo_pagamento' => $tipoPagamento[$i],
                                'data_vencimento' => $request->data_vencimento[$i] ? $request->data_vencimento[$i] : date('Y-m-d'),
                                'valor' => __convert_value_bd($request->valor_fatura[$i])
                            ]);
                        }

                        if ($request->tpNF == 1) {
                            if ($request->gerar_conta_receber) {
                                for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                                    ContaReceber::create([
                                        'empresa_id' => $request->empresa_id,
                                        'nfe_id' => $nfe->id,
                                        'cliente_id' => $cliente_id,
                                        'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                                        'tipo_pagamento' => $tipoPagamento[$i],
                                        'data_vencimento' => $request->data_vencimento[$i] ? $request->data_vencimento[$i] : date('Y-m-d'),
                                        'local_id' => $local_id,
                                        'descricao' => "Parcela " . $i+1 . " de " . sizeof($tipoPagamento),
                                        'referencia' => "Pedido " . $nfe->numero_sequencial
                                    ]);
                                }
                            }
                        } else {
                            for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                                if ($request->gerar_conta_pagar) {
                                    ContaPagar::create([
                                        'empresa_id' => $request->empresa_id,
                                        'nfe_id' => $nfe->id,
                                        'fornecedor_id' => $fornecedor_id,
                                        'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                                        'tipo_pagamento' => $tipoPagamento[$i],
                                        'data_vencimento' => $request->data_vencimento[$i] ? $request->data_vencimento[$i] : date('Y-m-d'),
                                        'local_id' => $local_id,
                                        'descricao' => "Parcela " . $i+1 . " de " . sizeof($tipoPagamento),
                                        'referencia' => "Pedido " . $nfe->numero_sequencial
                                    ]);
                                }
                            }
                        }
                    }
                }

                if ($request->funcionario_id != null) {

                    $funcionario = Funcionario::findOrFail($request->funcionario_id);
                    $comissao = $funcionario->comissao;
                    $valorRetorno = $this->calcularComissaoVenda($nfe, $comissao, $request->empresa_id);

                    if($valorRetorno > 0){
                        ComissaoVenda::create([
                            'funcionario_id' => $request->funcionario_id,
                            'nfce_id' => null,
                            'nfe_id' => $nfe->id,
                            'tabela' => 'nfe',
                            'valor' => $valorRetorno,
                            'valor_venda' => __convert_value_bd($request->valor_total),
                            'status' => 0,
                            'empresa_id' => $request->empresa_id
                        ]);
                    }
                }

                $this->filaEnvioUtil->adicionaVendaFila($nfe);

                // dd($request->all());
                if ($request->ordem_servico_id) {
                    $ordem = OrdemServico::findOrFail($request->ordem_servico_id);
                    $ordem->nfe_id = $nfe->id;
                    $ordem->save();
                }

                if ($request->pedido_ecommerce_id) {
                    $pedido = PedidoEcommerce::findOrFail($request->pedido_ecommerce_id);
                    $pedido->nfe_id = $nfe->id;
                    $pedido->estado = 'finalizado';
                    $pedido->save();
                }
                if ($request->pedido_mercado_livre_id) {
                    $pedido = PedidoMercadoLivre::findOrFail($request->pedido_mercado_livre_id);
                    $pedido->nfe_id = $nfe->id;
                    $pedido->save();
                }
                if ($request->pedido_nuvem_shop_id) {
                    $pedido = NuvemShopPedido::findOrFail($request->pedido_nuvem_shop_id);
                    $pedido->nfe_id = $nfe->id;
                    $pedido->save();
                }
                if ($request->cotacao_id) {
                    $cotacao = Cotacao::findOrFail($request->cotacao_id);
                    $cotacao->nfe_id = $nfe->id;
                    $cotacao->escolhida = 1;
                    $cotacao->estado = 'aprovada';
                    $cotacao->save();
                }
                if ($request->reserva_id) {
                    $reserva = Reserva::findOrFail($request->reserva_id);
                    $reserva->nfe_id = $nfe->id;
                    $reserva->save();
                }

                if ($request->pedido_vendizap_id) {
                    $pedido = PedidoVendiZap::findOrFail($request->pedido_vendizap_id);
                    $pedido->nfe_id = $nfe->id;
                    $pedido->save();
                }

                if ($request->pedido_woocommerce_id) {
                    $pedido = WoocommercePedido::findOrFail($request->pedido_woocommerce_id);
                    $pedido->nfe_id = $nfe->id;
                    $pedido->save();
                }

                if($request->orcamento_id){
                    for($i=0; $i<sizeof($request->orcamento_id); $i++){
                        $orcamento = Nfe::findOrFail($request->orcamento_id[$i]);
                        $orcamento->itens()->delete();
                        $orcamento->fatura()->delete();
                        $orcamento->delete();
                    }
                }

                return $nfe;
            });
session()->flash("flash_success", "Venda cadastrada!");
} catch (\Exception $e) {
    // echo $e->getMessage() . '<br>' . $e->getLine();
    // die;
    if ($request->orcamento == 1) {
        __createLog(request()->empresa_id, 'Orçamento', 'erro', $e->getMessage());
    }else if (isset($request->is_compra)) {
        __createLog(request()->empresa_id, 'Compra', 'erro', $e->getMessage());
    }else{
        __createLog(request()->empresa_id, 'Venda', 'erro', $e->getMessage());
    }
    session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage() . " - " . $e->getLine());
    return redirect()->back();
}

if ($request->orcamento == 1) {
    $descricaoLog = $nfe->cliente->info . " R$ " . __moeda($nfe->total);
    __createLog($request->empresa_id, 'Orçamento', 'cadastrar', $descricaoLog);
    session()->flash("flash_success", "Orçamento cadastrado!");
    return redirect()->route('orcamentos.index');
}
if (isset($request->is_compra)) {

    $descricaoLog = $nfe->fornecedor->info . " R$ " . __moeda($nfe->total);
    __createLog($request->empresa_id, 'Compra', 'cadastrar', $descricaoLog);
    session()->flash("flash_success", "Compra cadastrada!");
    if($nfe){
        if ($nfe->isItemValidade()) {
            return redirect()->route('compras.info-validade', $nfe->id);
        }

        foreach($nfe->itens as $i){
            if($i->produto->tipo_unico){
                return redirect()->route('compras.set-codigo-unico', $nfe->id);
            }
        }
    }
    return redirect()->route('compras.index');
} else {
    $descricaoLog = $nfe->cliente->info . " R$ " . __moeda($nfe->total);
    __createLog($request->empresa_id, 'Venda', 'cadastrar', $descricaoLog);

    foreach($nfe->itens as $i){
        if($i->produto->tipo_unico){
            return redirect()->route('nfe.set-codigo-unico', $nfe->id);
        }
    }

    if(sizeof($nfe->fatura) > 0){
        return redirect()->route('nfe.index')->with('url_carne', route('nfe.imprimir-carne', $nfe->id));
    }else{
        return redirect()->route('nfe.index');
    }
}
}

private function calcularComissaoVenda($nfce, $comissao, $empresa_id)
{
    $valorRetorno = 0;
    $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

    $tipoComissao = 'percentual_vendedor';
    if($config != null && $config->tipo_comissao == 'percentual_margem'){
        $tipoComissao = 'percentual_margem';
    }
    if($tipoComissao == 'percentual_vendedor'){
        $valorRetorno = ((float)$nfce->total * (float)$comissao) / 100;
    }else{
        foreach ($nfce->itens as $i) {

            $percentualLucro = ((($i->produto->valor_compra-$i->valor_unitario)/$i->produto->valor_compra)*100)*-1;
            $margens = MargemComissao::where('empresa_id', request()->empresa_id)->get();
            $margemComissao = null;
            $dif = 0;
            $difAnterior = 100;
            foreach($margens as $m){
                $margem = $m->margem;
                if($percentualLucro >= $margem){
                    $dif = $percentualLucro - $margem;
                    if($dif < $difAnterior){
                        $margemComissao = $m;
                        $difAnterior = $dif;
                    }
                }
            }
            if($margemComissao){
                $valorRetorno += ($i->sub_total * $margemComissao->percentual) / 100;
            }
        }
    }
    return $valorRetorno;
}

private function cadastrarCliente($request)
{
    $cliente = Cliente::create([
        'empresa_id' => $request->empresa_id,
        'razao_social' => $request->cliente_nome,
        'nome_fantasia' => $request->nome_fantasia ?? '',
        'cpf_cnpj' => $request->cliente_cpf_cnpj,
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
        'razao_social' => $request->cliente_nome,
        'nome_fantasia' => $request->nome_fantasia ?? '',
        'cpf_cnpj' => $request->cliente_cpf_cnpj,
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

private function cadastrarFornecedor($request)
{
    $fornecedor = Fornecedor::create([
        'empresa_id' => $request->empresa_id,
        'razao_social' => $request->fornecedor_nome,
        'nome_fantasia' => $request->nome_fantasia ?? '',
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

private function atualizaFornecedor($request)
{
    $fornecedor = Fornecedor::findOrFail($request->fornecedor_id);
    $fornecedor->update([
        'razao_social' => $request->fornecedor_nome,
        'nome_fantasia' => $request->nome_fantasia ?? '',
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
            'email' => $request->email_transp,
            'cidade_id' => $request->cidade_transp,
            'telefone' => $request->telefone_transp,
            'rua' => $request->rua_transp,
            'cep' => $request->cep_transp,
            'numero' => $request->numero_transp,
            'bairro' => $request->bairro_transp,
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
            'cidade_id' => $request->cidade_transp,
            'rua' => $request->rua_transp,
            'cep' => $request->cep_transp,
            'numero' => $request->numero_transp,
            'bairro' => $request->bairro_transp,
            'complemento' => $request->complemento_transp
        ]);
        return $transportadora->id;
    }
    return null;
}

public function update(Request $request, $id)
{
        // dd($request);
    try {

        DB::transaction(function () use ($request, $id) {
            $item = Nfe::findOrFail($id);
            $transportadora_id = $request->transportadora_id;
            if ($request->transportadora_id == null) {
                $transportadora_id = $this->cadastrarTransportadora($request);
            }
            $config = Empresa::find($request->empresa_id);
            $tipoPagamento = $request->tipo_pagamento;

            $request->merge([
                'emissor_nome' => $config->nome,
                'emissor_cpf_cnpj' => $config->cpf_cnpj,
                'ambiente' => $config->ambiente,
                'chave' => '',
                'transportadora_id' => $transportadora_id,
                'numero' => $request->numero_nfe ? $request->numero_nfe : 0,
                'total' => __convert_value_bd($request->valor_total),
                'desconto' => __convert_value_bd($request->desconto),
                'acrescimo' => __convert_value_bd($request->acrescimo),
                'valor_produtos' => __convert_value_bd($request->valor_total) ?? 0,
                'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                'tipo_pagamento' => $request->tipo_pagamento[0],
            ]);

            $item->fill($request->all())->save();

            if(isset($request->data_emissao)){
                $dataEmissao = str_replace("/", "-", $request->data_emissao);
                $dataEmissao = \Carbon\Carbon::parse($dataEmissao)->format('Y-m-d H:i');
                $item->data_emissao = $dataEmissao;
                $item->save();
            }

            foreach($item->itens as $x){
                $product = $x->produto;
                if ($product->gerenciar_estoque && $item->orcamento == 0) {
                    if (isset($request->is_compra)) {
                        $this->util->reduzEstoque($product->id, $x->quantidade, $x->variacao_id, $item->local_id);
                    } else {
                        $this->util->incrementaEstoque($product->id, $x->quantidade, $x->variacao_id, $item->local_id);
                    }
                }
            }

            foreach($item->itens as $it){
                $it->itensDimensao()->delete();
                $it->delete();
            }
            $item->fatura()->delete();

            for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                $product = Produto::findOrFail($request->produto_id[$i]);
                $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;

                $itemNfe = ItemNfe::create([
                    'nfe_id' => $item->id,
                    'produto_id' => (int)$request->produto_id[$i],
                    'descricao' => $request->descricao[$i] ?? '',
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
                    'variacao_id' => $variacao_id,
                    'xPed' => $request->xPed[$i],
                    'nItemPed' => $request->nItemPed[$i],
                    'infAdProd' => $request->infAdProd[$i],
                ]);

                // dd($request->all());
                if(isset($request->dimensao_largura)){
                    if(isset($request->_line[$i])){
                        for($l=0; $l<sizeof($request->dimensao_largura); $l++){
                            if($i == $request->_line[$i]){
                                ItemDimensaoNfe::create([
                                    'item_nfe_id' => $itemNfe->id,
                                    'valor_unitario_m2' => __convert_value_bd($request->dimensao_valor_unitario_m2[$l]),
                                    'largura' => $request->dimensao_largura[$l],
                                    'altura' => $request->dimensao_altura[$l],
                                    'quantidade' => $request->dimensao_quantidade[$l],
                                    'm2_total' => $request->dimensao_m2_total[$l],
                                    'espessura' => $request->dimensao_espessura[$l] ?? 0,
                                    'observacao' => $request->dimensao_observacao[$l] ?? '',
                                    'sub_total' => __convert_value_bd($request->dimensao_sub_total[$l])
                                ]);
                            }
                        }
                    }
                }

                if (isset($request->is_compra) && isset($request->dados_importacao)) {
                    $dadosImportacao = json_decode($request->dados_importacao[$i]);

                    $itemNfe->nDI = $dadosImportacao->nDI;
                    $itemNfe->dDI = $dadosImportacao->dDI;
                    $itemNfe->cidade_desembarque_id = $dadosImportacao->cidade_desembarque_id;
                    $itemNfe->dDesemb = $dadosImportacao->dDesemb;
                    $itemNfe->tpViaTransp = $dadosImportacao->tpViaTransp;
                    $itemNfe->vAFRMM = __convert_value_bd($dadosImportacao->vAFRMM);
                    $itemNfe->tpIntermedio = $dadosImportacao->tpIntermedio;
                    $itemNfe->cpf_cnpj_di = $dadosImportacao->cpf_cnpj_di;
                    $itemNfe->UFTerceiro = $dadosImportacao->UFTerceiro;
                    $itemNfe->cExportador = $dadosImportacao->cExportador;
                    $itemNfe->nAdicao = $dadosImportacao->nAdicao;
                    $itemNfe->cFabricante = $dadosImportacao->cFabricante;

                    $itemNfe->vBCII = __convert_value_bd($dadosImportacao->vBCII);
                    $itemNfe->vDespAdu = __convert_value_bd($dadosImportacao->vDespAdu);
                    $itemNfe->vII = __convert_value_bd($dadosImportacao->vII);
                    $itemNfe->vIOF = __convert_value_bd($dadosImportacao->vIOF);

                    $itemNfe->save();
                }

                if ($product->gerenciar_estoque && $item->orcamento == 0 && $item->natureza->movimentar_estoque == 1) {
                    if (isset($request->is_compra)) {
                        $this->util->incrementaEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $item->local_id);
                    } else {
                        $this->util->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $item->local_id);
                    }
                }
            }

            ContaReceber::where('nfe_id', $item->id)->delete();
            ContaPagar::where('nfe_id', $item->id)->delete();
            FaturaNfe::where('nfe_id', $item->id)->delete();

            if ($request->tpNF == 1) {

                if ($request->gerar_conta_receber) {

                    for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                        if(isset($request->valor_fatura[$i])){
                            ContaReceber::create([
                                'empresa_id' => $request->empresa_id,
                                'nfe_id' => $item->id,
                                'cliente_id' => $request->cliente_id,
                                'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                                'tipo_pagamento' => $tipoPagamento[$i],
                                'data_vencimento' => $request->data_vencimento[$i],
                                'local_id' => $item->local_id,
                                'descricao' => "Parcela " . $i+1 . " de " . sizeof($tipoPagamento),
                                'referencia' => "Pedido " . $item->numero_sequencial
                            ]);
                        }
                    }
                }
            } else {

                if ($request->gerar_conta_pagar) {
                    for ($i = 0; $i < sizeof($tipoPagamento); $i++) {

                        ContaPagar::create([
                            'empresa_id' => $request->empresa_id,
                            'nfe_id' => $item->id,
                            'fornecedor_id' => $request->fornecedor_id,
                            'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                            'tipo_pagamento' => $tipoPagamento[$i],
                            'descricao' => "Parcela " . $i+1 . " de " . sizeof($tipoPagamento),
                            'data_vencimento' => $request->data_vencimento[$i],
                            'local_id' => $item->local_id,
                            'referencia' => "Pedido " . $item->numero_sequencial
                        ]);
                    }
                }
            }

            if ($request->tipo_pagamento) {
                for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                    $d = FaturaNfe::create([
                        'nfe_id' => $item->id,
                        'tipo_pagamento' => $tipoPagamento[$i],
                        'data_vencimento' => $request->data_vencimento[$i],
                        'valor' => __convert_value_bd($request->valor_fatura[$i])
                    ]);
                }
            }

            if ($request->funcionario_id != null) {

                $comissao = ComissaoVenda::where('empresa_id', $item->empresa_id)
                ->where('nfe_id', $item->id)->first();

                if($comissao){
                    $comissao->delete();
                }

                $funcionario = Funcionario::findOrFail($request->funcionario_id);
                $comissao = $funcionario->comissao;
                $valorRetorno = $this->calcularComissaoVenda($item, $comissao, $request->empresa_id);

                if($valorRetorno > 0){
                    ComissaoVenda::create([
                        'funcionario_id' => $request->funcionario_id,
                        'nfce_id' => null,
                        'nfe_id' => $item->id,
                        'tabela' => 'nfe',
                        'valor' => $valorRetorno,
                        'valor_venda' => __convert_value_bd($request->valor_total),
                        'status' => 0,
                        'empresa_id' => $request->empresa_id
                    ]);
                }
            }

        });
session()->flash("flash_success", "Venda alterada com sucesso!");
} catch (\Exception $e) {
    // echo $e->getMessage() . '<br>' . $e->getLine();
    // die;
    $item = Nfe::findOrFail($id);
    if ($item->orcamento == 1) {
        __createLog(request()->empresa_id, 'Orçamento', 'erro', $e->getMessage());
    }else if ($item->tpNF == 0) {
        __createLog(request()->empresa_id, 'Compra', 'erro', $e->getMessage());
    }else{
        __createLog(request()->empresa_id, 'Venda', 'erro', $e->getMessage());
    }
    session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
}

$item = Nfe::findOrFail($id);

if ($item->orcamento == 1) {
    $descricaoLog = $item->cliente->info . " R$ " . __moeda($item->total);
    __createLog($request->empresa_id, 'Orçamento', 'editar', $descricaoLog);
    session()->flash("flash_success", "Orçamento atualizado!");
    return redirect()->route('orcamentos.index');
}
if (isset($request->is_compra)) {
    $descricaoLog = $item->fornecedor->info . " R$ " . __moeda($item->total);
    __createLog($request->empresa_id, 'Compra', 'editar', $descricaoLog);
    session()->flash("flash_success", "Compra atualizada!");
    return redirect()->route('compras.index');
} else {
    $descricaoLog = $item->cliente->info . " R$ " . __moeda($item->total);
    __createLog($request->empresa_id, 'Venda', 'editar', $descricaoLog);

    return redirect()->route('nfe.index');
}
}


public function destroy($id)
{
    $item = Nfe::findOrFail($id);
    __validaObjetoEmpresa($item);

    try {
        if($item->estado != 'cancelado'){
            foreach ($item->itens as $i) {
                if ($i->produto->gerenciar_estoque) {
                    if ($item->tpNF == 1) {
                        $this->util->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
                    }else{
                        $this->util->reduzEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
                    }
                }

                ItemProducao::where('item_id', $i->id)->delete();
                ItemDimensaoNfe::where('item_nfe_id', $i->id)->delete();
            }
        }

        $comissao = ComissaoVenda::where('empresa_id', $item->empresa_id)
        ->where('nfe_id', $item->id)->first();

        if($comissao){
            $comissao->delete();
        }

        $garantia = Garantia::where('empresa_id', $item->empresa_id)
        ->where('nfe_id', $item->id)->first();

        if($garantia){
            $garantia->delete();
        }

        ContaPagar::where('nfe_id', $item->id)->delete();
        ContaReceber::where('nfe_id', $item->id)->delete();

        $item->itens()->delete();
        $item->fatura()->delete();
        ProdutoUnico::where('nfe_id', $id)->delete();
        $item->delete();

        if($item->orcamento == 1){
            $descricaoLog = $item->cliente->info . " R$ " . __moeda($item->total);
            __createLog(request()->empresa_id, 'Orçamento', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Orçamento removido!");
        }else if($item->tpNF == 0){

            if($item->manifesto){
                $manifesto = $item->manifesto;
                $manifesto->compra_id = null;
                $manifesto->save();
            }
            $descricaoLog = $item->fornecedor->info . " R$ " . __moeda($item->total);
            __createLog(request()->empresa_id, 'Compra', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Compra removida!");
        }else{
            $descricaoLog = $item->cliente->info . " R$ " . __moeda($item->total);
            __createLog(request()->empresa_id, 'Venda', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Venda removida!");
        }
    } catch (\Exception $e) {
        // echo $e->getLine();
        // die;
        if ($item->orcamento == 1) {
            __createLog(request()->empresa_id, 'Orçamento', 'erro', $e->getMessage());
        }else if ($item->tpNF == 0) {
            __createLog(request()->empresa_id, 'Compra', 'erro', $e->getMessage());
        }else{
            __createLog(request()->empresa_id, 'Venda', 'erro', $e->getMessage());
        }
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->back();
}

public function xmlTemp($id)
{
    $item = Nfe::findOrFail($id);

    $empresa = $item->empresa;
    $empresa = __objetoParaEmissao($empresa, $item->local_id);

    if ($empresa->arquivo == null) {
        session()->flash("flash_error", "Certificado não encontrado para este emitente");
        return redirect()->route('config.index');
    }

    $nfe_service = new NFeService([
        "atualizacao" => date('Y-m-d h:i:s'),
        "tpAmb" => (int)$empresa->ambiente,
        "razaosocial" => $empresa->nome,
        "siglaUF" => $empresa->cidade->uf,
        "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
        // "schemes" => "PL_009_V4",
        "schemes" => "PL_010_V1.21",
        "versao" => "4.00",
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

function validarTagsICMS($xmlString)
{
    libxml_use_internal_errors(true);

    $xml = simplexml_load_string($xmlString);

    foreach ($xml->infNFe->det as $det) {

        $nItem = (string) $det['nItem'];

        $imposto = $det->imposto;

        $gruposICMS = [
            'ICMS00','ICMS10','ICMS20','ICMS30','ICMS40','ICMS51',
            'ICMS60','ICMS70','ICMS90','ICMSST'
        ];

        foreach ($gruposICMS as $g) {
            if (isset($imposto->ICMS->{$g})) {
                return true;
            }
        }
        if (isset($imposto->ICMS->ICMSST)) {
            return true;
        }

        $gruposSN = [
            'ICMSSN101','ICMSSN102','ICMSSN201','ICMSSN202','ICMSSN203',
            'ICMSSN500','ICMSSN900'
        ];

        foreach ($gruposSN as $g) {
            if (isset($imposto->ICMS->{$g})) {
                return true;
            }
        }
    }

    return false;
}

public function danfeTemporaria($id)
{
    $item = Nfe::findOrFail($id);
    __validaObjetoEmpresa($item);

    $empresa = $item->empresa;
    $empresa = __objetoParaEmissao($empresa, $item->local_id);
    // dd($empresa);
    if ($empresa->arquivo == null) {
        session()->flash("flash_error", "Certificado não encontrado para este emitente");
        return redirect()->route('config.index');
    }

    $nfe_service = new NFeService([
        "atualizacao" => date('Y-m-d h:i:s'),
        "tpAmb" => (int)$empresa->ambiente,
        "razaosocial" => $empresa->nome,
        "siglaUF" => $empresa->cidade->uf,
        "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
        // "schemes" => "PL_009_V4",
        "schemes" => "PL_010_V1.21",
        "versao" => "4.00",
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

        $danfe = new Danfe($xml);
        if($empresa->logo){
            $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . $empresa->logo));
            $danfe->logoParameters($logo, 'L');
        }
        $danfe->exibirTextoFatura = 1;

        $pdf = $danfe->render();
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    } else {
        return response()->json($doc['erros_xml'], 401);
    }
}

public function inutilizar(Request $request)
{
    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    $data = Inutilizacao::where('empresa_id', request()->empresa_id)
    ->where('modelo', '55')->orderBy('id', 'desc')
    ->when(!empty($start_date), function ($query) use ($start_date) {
        return $query->whereDate('created_at', '>=', $start_date);
    })
    ->when(!empty($end_date), function ($query) use ($end_date,) {
        return $query->whereDate('created_at', '<=', $end_date);
    })
    ->get();
    $modelo = '55';
    return view('inutilizacao.index', compact('data', 'modelo'));
}

public function inutilStore(Request $request)
{
    $request->merge([
        'estado' => 'novo',
        'modelo' => '55'
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
    $item = Nfe::findOrFail($id);
    $tipo = request()->tipo;

    return view('nfe.estado_fiscal', compact('item', 'tipo'));
}

public function storeEstado(Request $request, $id)
{
    $item = Nfe::findOrFail($id);
    try {
        $item->estado = $request->estado_emissao;
        if ($request->hasFile('file')) {
            $xml = simplexml_load_file($request->file);
            $chave = substr($xml->NFe->infNFe->attributes()->Id, 3, 44);
            $file = $request->file;

            $dir = 'xml_nfe/';
            if($item->estado == 'cancelado'){
                $dir = 'xml_nfe_cancelada/';
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
    if($request->tipo == 'devolucao'){
        return redirect()->route('devolucao.index');
    }
    return redirect()->route('nfe.index');
}

public function imprimirVenda($id)
{
    $item = Nfe::findOrFail($id);
    __validaObjetoEmpresa($item);
    $config = Empresa::where('id', $item->empresa_id)->first();

    $config = __objetoParaEmissao($config, $item->local_id);
    $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

    if($configGeral && $configGeral->mensagem_padrao_impressao_venda){
        $item->observacao .= " " . $configGeral->mensagem_padrao_impressao_venda;
    }

    $p = view('nfe.imprimir', compact('config', 'item', 'configGeral'));

    $domPdf = new Dompdf(["enable_remote" => true]);
    $domPdf->loadHtml($p);
    $pdf = ob_get_clean();
    $domPdf->setPaper("A4");
    $domPdf->render();
    header("Content-Disposition: ; filename=Pedido.pdf");
    $domPdf->stream("Venda #$item->numero_sequencial.pdf", array("Attachment" => false));
}

public function show($id)
{
    $data = Nfe::findOrFail($id);
    __validaObjetoEmpresa($data);
    return view('nfe.show', compact('data'));
}

public function importZip(){

    $zip_loaded = extension_loaded('zip') ? true : false;
    if ($zip_loaded === false) {
        session()->flash('flash_error', "Por favor instale/habilite o PHP zip para importar");
        return redirect()->back();
    }
    return view('nfe.import_zip');
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

            return view('nfe.import_zip_view', compact('data'));

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

private function clearFolder($destino){
    $files = glob($destino."/*");
    foreach($files as $file){ 
        if(is_file($file)) unlink($file); 
    }
}

private function preparaXmls($destino){
    $files = glob($destino."/*");
    $data = [];
    foreach($files as $file){
        if(is_file($file)){
            try{
                $xml = simplexml_load_file($file);

                $cliente = $this->getCliente($xml);

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
                        'cliente' => $cliente,
                        'produtos' => $produtos,
                        'fatura' => $fatura,
                        'file' => $file,
                        'natureza' => (string)$xml->NFe->infNFe->ide->natOp[0],
                        'observacao' => (string)$xml->NFe->infNFe->infAdic ? $xml->NFe->infNFe->infAdic->infCpl[0] : '',
                        'tipo_pagamento' => (string)$xml->NFe->infNFe->pag->detPag->tPag,
                        'finNFe' => (string)$xml->NFe->infNFe->ide->finNFe,
                        'data_emissao'
                    ];
                    array_push($data, $item);
                    // dd($item);
                }
            }catch(\Exception $e){

            }
        }
    }

    return $data;
}

private function getCliente($xml){

    if(!isset($xml->NFe->infNFe->dest->enderDest->cMun)){ 
        return null;
    }
    $cidade = Cidade::where('codigo', $xml->NFe->infNFe->dest->enderDest->cMun)->first();

    $dadosCliente = [
        'cpf_cnpj' => isset($xml->NFe->infNFe->dest->CNPJ) ? (string)$xml->NFe->infNFe->dest->CNPJ : (string)$xml->NFe->infNFe->dest->CPF,
        'razao_social' => (string)$xml->NFe->infNFe->dest->xNome,               
        'nome_fantasia' => (string)$xml->NFe->infNFe->dest->xFant,
        'rua' => (string)$xml->NFe->infNFe->dest->enderDest->xLgr,
        'numero' => (string)$xml->NFe->infNFe->dest->enderDest->nro,
        'bairro' => (string)$xml->NFe->infNFe->dest->enderDest->xBairro,
        'cep' => (string)$xml->NFe->infNFe->dest->enderDest->CEP,
        'telefone' => (string)$xml->NFe->infNFe->dest->enderDest->fone,
        'ie_rg' => (string)$xml->NFe->infNFe->dest->IE,
        'cidade_id' => $cidade->id,
        'cidade_info' => "$cidade->nome ($cidade->uf)",
        'consumidor_final' => (string)$xml->NFe->infNFe->dest->IE ? 1 : 0,
        'status' => 1,
        'contribuinte' => 1,
        'empresa_id' => request()->empresa_id
    ];
    return $dadosCliente;
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
        return null;
    }
}

private function getFatura($xml){
    $fatura = [];

    try{
        if (!empty($xml->NFe->infNFe->cobr->dup))
        {
            foreach($xml->NFe->infNFe->cobr->dup as $dup) {
                $titulo = $dup->nDup;
                $vencimento = $dup->dVenc;

                $vlr_parcela = number_format((double) $dup->vDup, 2, ".", ""); 

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

private function getCfpos($cfop){

    $n = substr($cfop, 1, 4);
    return [
        'cfop_estadual' => '5'.$n,
        'cfop_outro_estado' => '6'.$n,
        'cfop_entrada_estadual' => '1'.$n,
        'cfop_entrada_outro_estado' => '2'.$n,
    ];
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

                    $cliente = $this->insereCliente($data->cliente);
                    $produtos = $this->insereProdutos($data->produtos, $request->local_id);

                    $nfe = $this->salvarVenda($data, $cliente, $request->local_id);
                    if($nfe != 0){
                        File::copy($data->file, public_path("xml_nfe/").$data->chave.".xml");
                        $cont++;
                    }
                }
            }
            return $cont;
        });
        session()->flash("flash_success", 'Total de vendas salvas: ' . $cont);
        return redirect()->route('nfe.index');
    }catch(\Exception $e){
        // echo $e->getLine();
        // die;
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        return redirect()->route('nfe.index');
    }
}

public function imprimirCarne($id){
    $item = Nfe::findOrFail($id);
    $config = Empresa::findOrFail(request()->empresa_id);

    $p = view('nfe.carne', compact('config', 'item'));
    $domPdf = new Dompdf(["enable_remote" => true]);
    $domPdf->loadHtml($p);
    $pdf = ob_get_clean();

    $domPdf->setPaper("A4");
    $domPdf->render();
    $domPdf->stream("Carnê de venda.pdf", array("Attachment" => false));

}

private function salvarVenda($venda, $cliente, $local_id){
    $natureza = $this->insereNatureza($venda->natureza);
    $empresa = Empresa::findOrFail($cliente->empresa_id);
    $empresa = __objetoParaEmissao($empresa, $local_id);

    $dataVenda = [
        'cliente_id' => $cliente->id,
        'estado' => 'aprovado',
        'empresa_id' => $cliente->empresa_id,
        'numero' => $venda->numero_nfe,
        'natureza_id' => $natureza->id,
        'chave' => $venda->chave,
        'emissor_nome' => $empresa->nome,
        'emissor_cpf_cnpj' => $empresa->cpf_cnpj,
        'numero_serie' => $venda->serie,
        'total' => $venda->valor_total,
        'desconto' => $venda->desconto,
        'tipo_pagamento' => $venda->tipo_pagamento,
        'observacao' => $venda->observacao,
        'tpNF' => 1,
        'finNFe' => $venda->finNFe,
        'local_id' => $local_id,
    ];

    $nfe = Nfe::where('empresa_id', $cliente->empresa_id)
    ->where('chave', $venda->chave)->first();
    if($nfe == null){
        $nfe = Nfe::create($dataVenda);

        $nfe->data_emissao = $venda->data;
        $nfe->created_at = $venda->data;
        $nfe->save();
    }else{

        $nfe->data_emissao = $venda->data;
        $nfe->created_at = $venda->data;
        $nfe->save();
        return 0;
    }

    $nfe->data_emissao = $venda->data;
    $nfe->save();
    foreach($venda->produtos as $i){
        $p = Produto::where('empresa_id', $cliente->empresa_id)
        ->where('nome', $i->nome)->first();
        if($p != null){
            $ncm = $i->ncm;
            $mask = '####.##.##';
            if(!str_contains($ncm, ".")){
                $ncm = __mask($ncm, $mask);
            }

            ItemNfe::create([
                'nfe_id' => $nfe->id,
                'produto_id' => $p->id,
                'quantidade' => $i->quantidade,
                'valor_unitario' => $i->valor_unitario,
                'sub_total' => $i->sub_total,
                'perc_icms' => $i->perc_icms,
                'perc_pis' => $i->perc_pis,
                'perc_cofins' => $i->perc_cofins,
                'perc_ipi' => $i->perc_ipi,
                'cst_csosn' => $i->cst_csosn,
                'cst_pis' => $i->cst_pis,
                'cst_cofins' => $i->cst_cofins,
                'cst_ipi' => $i->cst_ipi,
                'perc_red_bc' => $i->perc_red_bc,
                'cfop' => $i->cfop,
                'ncm' => $ncm,
                'cEnq' => $i->cEnq,
                'origem' => $i->origem,
                'cest' => $i->cest

            ]);
        }
    }

    foreach($venda->fatura as $f){
        FaturaNfe::create([
            'nfe_id' => $nfe->id,
            'tipo_pagamento' => $venda->tipo_pagamento,
            'data_vencimento' => $f->vencimento,
            'valor' => __convert_value_bd($f->valor_parcela)
        ]);

        if(strtotime($f->vencimento) >= strtotime(date('Y-m-d'))){
            ContaReceber::create([
                'empresa_id' => $nfe->empresa_id,
                'nfe_id' => $nfe->id,
                'cliente_id' => $cliente->id,
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

private function insereCliente($data){

    if(!isset($data->cpf_cnpj)) return null;
    $cpf_cnpj = $data->cpf_cnpj;

    $mask = '##.###.###/####-##';

    if(strlen($cpf_cnpj) == 11){
        $mask = '###.###.###.##';
    }

    if(!str_contains($cpf_cnpj, ".")){
        $cpf_cnpj = __mask($cpf_cnpj, $mask);
    }

    $data->cpf_cnpj = $cpf_cnpj;

    $cliente = Cliente::where('cpf_cnpj', $cpf_cnpj)->where('empresa_id', request()->empresa_id)
    ->first();

    if($cliente != null) return $cliente;

    return Cliente::create((array)$data);

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

public function setCodigoUnico($id)
{
    $item = Nfe::findOrFail($id);
    __validaObjetoEmpresa($item);

    $produtos = [];
    foreach ($item->itens as $i) {
        if ($i->produto->tipo_unico) {
            for ($x=0; $x<$i->quantidade; $x++) {
                array_push($produtos, $i);
            }
        }
    }
    return view('nfe.set_codigo_unico', compact('produtos', 'item'));
}

public function setarCodigoUnico(Request $request)
{   
    $nfe = Nfe::findOrFail($request->nfe_id);
    for ($i = 0; $i < sizeof($request->codigo); $i++) {
        $item = ProdutoUnico::findOrFail($request->codigo[$i]);

        $itemNfe = ItemNfe::findOrFail($request->item_id[$i]);
        $itemNfe->infAdProd = $item->codigo;
        $itemNfe->save();
        $item->em_estoque = 0;
        $item->save();
        ProdutoUnico::create([
            'nfe_id' => $request->nfe_id,
            'nfce_id' => null,
            'produto_id' => $item->produto_id,
            'codigo' => $item->codigo,
            'observacao' => $request->observacao[$i] ?? '',
            'tipo' => 'saida',
            'em_estoque' => 0
        ]);
    }

    session()->flash('flash_success', 'Dados definidos com sucesso!');
    return redirect()->route('nfe.index');
}

public function sendEmail(Request $request){
    $email = $request->email;
    $xml = $request->xml;
    $danfe = $request->danfe;
    $id = $request->id;

    $nfe = Nfe::findOrFail($id);
    if(!$nfe){
        session()->flash("flash_error", "NFe não encontrada!");
        return redirect()->back();
    }

    $docs = [];
    if($xml){
        $docs[] = public_path('xml_nfe/').$nfe->chave.'.xml';
    }
    if($danfe){
        $this->gerarDanfeTemporaria($nfe);
        $docs[] = public_path('danfe_temp/').$nfe->chave.'.pdf';
    }

    if ($request->hasFile('arquivo')) {

        if (!is_dir(public_path('arquivos_temporarios'))) {
            mkdir(public_path('arquivos_temporarios'), 0777, true);
        }
        $file = $request->arquivo;
        $ext = $file->getClientOriginalExtension();
        $file_name = $file->getClientOriginalName() . ".$ext";
        $file->move(public_path('arquivos_temporarios/'), $file_name);

        $docs[] = public_path('arquivos_temporarios/').$file_name;
    }

    $emailConfig = EmailConfig::where('empresa_id', $nfe->empresa_id)
    ->where('status', 1)
    ->first();
    try{
        if($emailConfig != null){

            $body = view('mail.nfe', compact('nfe'));
            $result = $this->emailUtil->enviaEmailPHPMailer($email, 'Envio de documento', $body, $emailConfig, $docs);
        }else{
            Mail::send('mail.nfe', ['nfe' => $nfe], function($m) use ($email, $docs){
                $nomeEmail = env('MAIL_FROM_NAME');
                $m->from(env('MAIL_USERNAME'), $nomeEmail);
                $m->subject('Envio de documento');
                foreach($docs as $f){
                    $m->attach($f); 
                }
                $m->to($email);
            });
        }
            //limpa diretorio danfe_temp
        $this->unlinkr(public_path('danfe_temp'));
        // $this->unlinkr(public_path('arquivos_temporarios'));
        session()->flash("flash_success", "Email enviado!");

    }catch(\Exception $e){
        session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
    }

    return redirect()->back();

}

private function gerarDanfeTemporaria($nfe){
    if (!is_dir(public_path('danfe_temp'))) {
        mkdir(public_path('danfe_temp'), 0777, true);
    }
    $xml = file_get_contents(public_path('xml_nfe/').$nfe->chave.'.xml');
    $danfe = new Danfe($xml);
    $pdf = $danfe->render();
    file_put_contents(public_path('danfe_temp/') . $nfe->chave . '.pdf', $pdf);

}

private function unlinkr($dir){ 
    $files = array_diff(scandir($dir), array('.', '..')); 
    foreach ($files as $file) { 
        (is_dir("$dir/$file")) ? $this->unlinkr("$dir/$file") : unlink("$dir/$file"); 
    }
    return rmdir($dir); 
}

public function metas(Request $request){

    $metas = MetaResultado::where('empresa_id', $request->empresa_id)
    ->where('tabela', 'Vendas')
    ->get();

    if(sizeof($metas) == 0){
        session()->flash("flash_warning", "Defina uma meta para vendas!");
        return redirect()->route('metas.index');
    }

    $totalMeta = $metas->sum('valor');
    $somaVendasMes = $this->somaVendasMes($request->empresa_id);

    $periodosMeta = $this->periodosMeta($request->empresa_id);
    return view('nfe.metas', compact('metas', 'totalMeta', 'somaVendasMes', 'periodosMeta'));
}

private function periodosMeta($empresa_id){
    $meses = [];

    $periodoAtual = date('m/Y');
    $primeiroPeriodo = null;
    $data1 = Nfe::where('empresa_id', $empresa_id)
    ->select('created_at')
    ->orderBy('created_at', 'asc')
    ->first();

    $data2 = Nfce::where('empresa_id', $empresa_id)
    ->select('created_at')
    ->orderBy('created_at', 'asc')
    ->first();

    if(strtotime($data1) > strtotime($data2)){
        $primeiroPeriodo = $data1->created_at;
    }else{
        $primeiroPeriodo = $data2->created_at;
    }

    $temp = $primeiroPeriodo;

    $tempPeriodo = $primeiroPeriodo;
    while($tempPeriodo != $periodoAtual){
        $meses[] = \Carbon\Carbon::parse($temp)->format('m/Y');
        $tempPeriodo = \Carbon\Carbon::parse($temp)->format('m/Y');

        $mes = \Carbon\Carbon::parse($temp)->format('m');
        $ano = \Carbon\Carbon::parse($temp)->format('Y');

        $temp = date("Y-m-d", strtotime("+1 month", strtotime($temp)));
    }
    return $meses;
}

private function somaVendasMes($empresa_id){
    $soma = Nfe::where('empresa_id', $empresa_id)
    ->where('estado', '!=', 'cancelado')
    ->whereMonth('created_at', date('m'))
    ->whereYear('created_at', date('Y'))
    ->where('orcamento', 0)
    ->sum('total');

    $soma += Nfce::where('empresa_id', $empresa_id)
    ->where('estado', '!=', 'cancelado')
    ->whereMonth('created_at', date('m'))
    ->whereYear('created_at', date('Y'))
    
    ->sum('total');

    return $soma;
}

public function siegTeste(Request $request){
    $item =  Nfe::where('empresa_id', $request->empresa_id)
    ->where('estado', 'aprovado')
    ->orderBy('id', 'desc')
    ->first();

    if($item == null){
        session()->flash("flash_warning", "Nenhuma NFe aprovada encontrada!");
        return redirect()->back();
    }

    try{
        $fileDir = public_path('xml_nfe/').$item->chave.'.xml';
        $teste = $this->siegUtil->enviarXml($item->empresa_id, $fileDir);
        dd($teste);
    }catch(\Exception $e){
        echo $e->getMessage();
    }

}

}
