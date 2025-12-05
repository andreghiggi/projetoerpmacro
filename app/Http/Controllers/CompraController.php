<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Nfe;
use App\Models\ItemNfe;
use App\Models\Produto;
use App\Models\ManifestoDfe;
use App\Models\ProdutoFornecedor;
use App\Models\Cidade;
use App\Models\ModeloEtiqueta;
use App\Models\RelacaoDadosFornecedor;
use App\Models\PadraoTributacaoProduto;
use App\Models\ContaPagar;
use App\Models\NaturezaOperacao;
use App\Models\Transportadora;
use App\Models\Empresa;
use App\Models\Contigencia;
use App\Models\Funcionario;
use App\Models\FaturaNfe;
use App\Models\Cotacao;
use App\Models\ConfigGeral;
use App\Models\ProdutoUnico;
use App\Models\CategoriaProduto;
use App\Models\Marca;
use App\Models\UnidadeMedida;
use App\Utils\EstoqueUtil;
use Illuminate\Support\Facades\DB;
use App\Models\ProdutoLocalizacao;
use App\Models\Localizacao;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:compras_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:compras_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:compras_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:compras_delete', ['only' => ['destroy']]);

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
        if($empresa->ambiente == 1){
            $numero = $empresa->numero_ultima_nfe_producao;
        }else{
            $numero = $empresa->numero_ultima_nfe_homologacao;
        }
        
        if($numero){
            Nfe::where('estado', 'novo')
            ->where('empresa_id', $empresa_id)
            ->where('chave_importada', null)
            ->update(['numero' => $numero+1]);
        }
    }

    public function index(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $this->corrigeNumeros($request->empresa_id);

        $fornecedores = Fornecedor::where('empresa_id', request()->empresa_id)->get();

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $fornecedor_id = $request->get('fornecedor_id');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');

        $this->setNumeroSequencial();

        $data = Nfe::where('empresa_id', request()->empresa_id)
        ->where('tpNF', 0)
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
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $contigencia = $this->getContigencia($request->empresa_id);
        return view('compras.index', compact('data', 'fornecedores', 'contigencia'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $sizeFornecedores = Fornecedor::where('empresa_id', request()->empresa_id)->count();
        if ($sizeFornecedores == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um fornecedor!");
            return redirect()->route('fornecedores.create');
        }
        $produtos = Produto::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($produtos) == 0) {
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
        $caixa = __isCaixaAberto();
        $empresa = Empresa::findOrFail(request()->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
        
        $numeroNfe = Nfe::lastNumero($empresa);

        $cotacao = null;
        if(isset($request->cotacao_id)){
            $cotacao = Cotacao::findOrfail($request->cotacao_id);
        }

        $isCompra = 1;

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        return view(
            'nfe.create', compact('produtos', 'transportadoras', 'cidades', 'naturezas', 'numeroNfe', 'isCompra', 
                'cotacao', 'empresa', 'caixa', 'funcionarios', 'config')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function xml()
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        return view('compras.xml');
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

            $nfeImportada = Nfe::where('empresa_id', $request->empresa_id)
            ->where('chave_importada', $chave)->first();

            if($nfeImportada != null){
                session()->flash('flash_error', 'Este XML já foi importado!');
                return redirect()->back();
            }

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

            $dataFornecedor = [

                'empresa_id' => $request->empresa_id,
                'razao_social' => $xml->NFe->infNFe->emit->xNome,
                'nome_fantasia' => $xml->NFe->infNFe->emit->xFant,
                'cpf_cnpj' => $doc,
                'ie' => $xml->NFe->infNFe->emit->IE,
                'contribuinte' => $xml->NFe->infNFe->emit->IE != '' ? 1 : 0,
                'consumidor_final' => 0,
                'email' => $xml->NFe->infNFe->emit->email,
                'telefone' => $xml->NFe->infNFe->emit->enderEmit->fone,
                'cidade_id' => $cidade->id,
                'rua' => $xml->NFe->infNFe->emit->enderEmit->xLgr,
                'cep' => $xml->NFe->infNFe->emit->enderEmit->CEP,
                'numero' => $xml->NFe->infNFe->emit->enderEmit->nro,
                'bairro' => $xml->NFe->infNFe->emit->enderEmit->xBairro,
                'complemento' => $xml->NFe->infNFe->emit->enderEmit->xBairro
            ];
            // dd($xml->NFe->infNFe->emit);
            $fornecedor = $this->cadastraFornecedor($dataFornecedor);
            $vFrete = (float)$xml->NFe->infNFe->total->ICMSTot->vFrete;
            $vDesc = (float)$xml->NFe->infNFe->total->ICMSTot->vDesc;

            $itens = [];
            $contSemRegistro = 0;
            foreach ($xml->NFe->infNFe->det as $item) {

                $produto = Produto::verificaCadastrado(
                    $item->prod->cEAN,
                    $item->prod->xProd,
                    $item->prod->cProd,
                    $request->empresa_id
                );
                // $produto = null;
                // if($produto == null){
                //     dd($item->prod->xProd);
                // }

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
                $configGeral = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
                $configGerenciaEstoque = 0;
                if($configGeral != null){
                    $configGerenciaEstoque = $configGeral->gerenciar_estoque;
                }

                $lucroPadraoProduto = 0;
                if($configGeral != null){
                    $lucroPadraoProduto = $configGeral->percentual_lucro_produto;
                }

                if($produto != null){
                    $lucroPadraoProduto = $produto->percentual_lucro;
                }

                $vCompra = (float)$item->prod->vUnCom + $vIpi + $vICMSST;
                // dd($vCompra);
                $vVenda = $vCompra + ($vCompra*($lucroPadraoProduto/100));
                $caixa = __isCaixaAberto();
                $local = $caixa->local_id;
                $local = "[$local]";

                $prod->id = $produto != null ? $produto->id : 0;
                $prod->codigo = $codigo;

                $prod->nomeXml = $nomeProduto;
                $prod->valorXml = $vCompra;
                $prod->cfopXml = (string)$item->prod->CFOP;

                $prod->xProd = $produto == null ? $nomeProduto : $produto->nome;
                $prod->ncm = $produto == null ? (string)$item->prod->NCM : $produto->ncm;
                $prod->cest = (string)$item->prod->CEST;
                $prod->cfop = (string)$item->prod->CFOP;
                $prod->unidade = $produto == null ? strtoupper((string)$item->prod->uCom) : $produto->unidade;
                $prod->valor_unitario = number_format((float)$item->prod->vUnCom + $vIpi + $vICMSST, 2, '.', '');

                $prod->valor_venda = $produto != null ? $produto->valor_unitario : 
                ($prod->valor_unitario + (($prod->valor_unitario * $lucroPadraoProduto)/100));

                $prod->quantidade = (float)$item->prod->qCom;
                $prod->sub_total = $prod->valor_unitario*$prod->quantidade;

                $barcode = (string)$item->prod->cEAN;
                if($barcode == 'SEM GTIN'){
                    $barcode = '';
                }
                $prod->codigo_barras = $produto == null ? $barcode : $produto->codigo_barras;
                // $prod->valor_venda = $produto == null ? $vVenda : $produto->valor_venda;
                $prod->valor_atacado = $produto == null ? 0 : $produto->valor_atacado;
                $prod->codigo_barras2 = $produto == null ? '' : $produto->codigo_barras2;

                $prod->quantidade_atacado = $produto == null ? '' : $produto->quantidade_atacado;
                $prod->valor_minimo_venda = $produto == null ? 0 : $produto->valor_minimo_venda;

                $prod->valor_compra = $produto == null ? $vCompra : $produto->valor_compra;
                $prod->margem = $lucroPadraoProduto;
                $prod->categoria_id = $produto == null ? 0 : $produto->categoria_id;
                $prod->sub_categoria_id = $produto == null ? 0 : $produto->sub_categoria_id;
                $prod->sub_categoria_nome = $produto != null && $produto->subcategoria ? $produto->subcategoria->nome : '';
                $prod->estoque_minimo = $produto == null ? '' : $produto->estoque_minimo;
                $prod->marca_id = $produto == null ? 0 : $produto->marca_id;
                $prod->gerenciar_estoque = $produto == null ? $configGerenciaEstoque : $produto->gerenciar_estoque;

                $prod->referencia = $produto == null ? '' : $produto->referencia;
                $prod->referencia_balanca = $produto == null ? '' : $produto->referencia_balanca;
                $prod->exportar_balanca = $produto == null ? 0 : $produto->exportar_balanca;
                $prod->observacao = $produto == null ? '' : $produto->observacao;
                $prod->observacao2 = $produto == null ? '' : $produto->observacao2;
                $prod->observacao3 = $produto == null ? '' : $produto->observacao3;
                $prod->observacao4 = $produto == null ? '' : $produto->observacao4;
                $prod->disponibilidade = $produto == null ? $local : json_encode($produto->locais->pluck('localizacao_id')->toArray());


                $arr = (array_values((array)$item->imposto->ICMS));
                $cst = (string)($arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN);
                $pICMS = (float)$arr[0]->pICMS ?? 0;

                $prod->perc_red_bc = 0;
                $prod->perc_icms = $pICMS;
                $prod->cst_csosn = $cst;

                $arr = (array_values((array)$item->imposto->PIS));

                $prod->cst_pis = (string)$arr[0]->CST;
                $prod->perc_pis = (float)$arr[0]->pPIS ?? 0;

                $arr = (array_values((array)$item->imposto->COFINS));
                $prod->cst_cofins = (string)$arr[0]->CST;
                $pCOFINS = $arr[0]->COFINS ?? 0;
                if ($pCOFINS == 0) {
                    $pCOFINS = $arr[0]->pCOFINS ?? 0;
                }
                $prod->perc_cofins = $arr[0]->pPIS ?? 0;

                $prod = $this->relacaoDadosFornecedor($prod);
                // dd($prod);
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
                } else {
                    $cst_ipi = '99';
                    $pIPI = 0;
                }

                $prod->perc_ipi = $pIPI;
                $prod->cst_ipi = $cst_ipi;

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
                'contSemRegistro' => $contSemRegistro,
                'data_emissao' => substr($xml->NFe->infNFe->ide->dhEmi[0], 0, 16),
                'itens' => $itens
            ];

            if (!is_dir(public_path('xml_entrada'))) {
                mkdir(public_path('xml_entrada'), 0777, true);
            }

            $fatura = [];
            $tPag = null;

            if (!empty($xml->NFe->infNFe->pag->detPag)) {
                $tPag = (string) $xml->NFe->infNFe->pag->detPag->tPag;
            }

            if (!empty($xml->NFe->infNFe->cobr->dup)) {
                foreach ($xml->NFe->infNFe->cobr->dup as $dup) {
                    $titulo = $dup->nDup;
                    $vencimento = $dup->dVenc;
                    $valor_parcela = number_format((float) $dup->vDup, 2, ".", "");
                    $parcela = [
                        'numero' => (int)$titulo,
                        'vencimento' => $vencimento,
                        'valor_parcela' => $valor_parcela,
                        'rand' => rand(0, 10000),
                        'tipo_pagamento' => $tPag
                    ];
                    array_push($fatura, $parcela);
                }
            } else {
                $vencimento = substr($xml->NFe->infNFe->ide->dhEmi[0], 0, 10);
                $parcela = [
                    'numero' => 1,
                    'vencimento' => $vencimento,
                    'valor_parcela' => (float)$xml->NFe->infNFe->total->ICMSTot->vProd,
                    'rand' => rand(0, 10000),
                    'tipo_pagamento' => $tPag
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

            $isCompra = 1;

            $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
            ->where('categoria_id', null)
            ->get();
            
            $marcas = Marca::where('empresa_id', request()->empresa_id)->get();

            $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
            ->where('status', 1)->get();
            $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();

            $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();

            return view('compras.import_xml', compact('dadosXml', 'transportadoras', 'cidades', 'naturezas', 'fornecedor', 'caixa', 
                'lucroPadraoProduto', 'isCompra', 'categorias', 'marcas', 'configGerenciaEstoque', 'unidades', 'config', 'padroes'));
        } else {
            session()->flash('flash_error', 'XML inválido!');
            return redirect()->back();
        }
    }

    private function getCfopEntrada($cfop)
    {

        $digito = substr($cfop, 0, 1);
        if ($digito == '5') {
            return '1' . substr($cfop, 1, 4);
        } else {
            return '2' . substr($cfop, 1, 4);
        }
    }

    private function relacaoDadosFornecedor($prod){
        // dd($prod);
        $item = relacaoDadosFornecedor::where('cst_csosn_entrada', $prod->cst_csosn)
        ->where('cfop_entrada', $prod->cfop)
        ->where('empresa_id', request()->empresa_id)
        ->first();
        if($item == null){
            $item = relacaoDadosFornecedor::where('cst_csosn_entrada', $prod->cst_csosn)
            ->where('empresa_id', request()->empresa_id)
            ->first();
        }

        if($item == null){
            $item = relacaoDadosFornecedor::where('cfop_entrada', $prod->cfop)
            ->where('empresa_id', request()->empresa_id)
            ->first();
        }

        if($item != null){
            if($item->cst_csosn_saida){
                $prod->cst_csosn = $item->cst_csosn_saida;
            }
            if($item->cfop_saida){
                $prod->cfop = $item->cfop_saida;
            }
        }
        return $prod;
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
                
                if (isset($request->fornecedor_id)) {
                    if ($request->fornecedor_id == null) {
                        // $fornecedor_id = $this->cadastrarFornecedor($request);
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

                $tipoPagamento = $request->tipo_pagamento;

                $local_id = $caixa->local_id;
                if(isset($request->local_id)){
                    $local_id = $request->local_id;
                }
                $request->merge([
                    'emissor_nome' => $config->nome,
                    'emissor_cpf_cnpj' => $config->cpf_cnpj,
                    'ambiente' => $config->ambiente,
                    'chave' => '',
                    'fornecedor_id' => $fornecedor_id,
                    'transportadora_id' => $transportadora_id,
                    'numero_serie' => $config->numero_serie_nfe ? $config->numero_serie_nfe : 1,
                    'numero' => $request->numero_nfe,
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
                // dd($request->all());
                // dd($request->tipo_pagamento[]);
                $nfe = Nfe::create($request->all());
                $nfe->data_emissao = $request->data_emissao;
                $nfe->save();
                // dd($request->all());
                for ($i = 0; $i < sizeof($request->cProd); $i++) {

                    if ($request->cadastrar_produto[$i] == 1) {
                        //cadastrar produto

                        $product = $this->cadastrarProduto($request, $i, $caixa->local_id);
                    } else {
                        //atualizar produto
                        $product = Produto::findOrFail($request->produto_id[$i]);
                        $product->padrao_id = $request->padrao_id;
                        if($request->padrao_item_id[$i]){
                            $product->padrao_id = $request->padrao_item_id[$i];
                        }
                        $product->referencia_xml = $request->cProd[$i];
                        $product->ncm = $request->ncm[$i];
                        $product->categoria_id = $request->_categoria_id[$i];
                        $product->sub_categoria_id = $request->_sub_categoria_id[$i];
                        $product->marca_id = $request->_marca_id[$i];
                        $product->codigo_barras = $request->codigo_barras[$i];
                        $product->codigo_barras2 = $request->_codigo_barras2[$i];
                        $product->gerenciar_estoque = $request->_gerenciar_estoque[$i];
                        $product->estoque_minimo = __convert_value_bd($request->_estoque_minimo[$i]);

                        $product->quantidade_atacado = __convert_value_bd($request->_quantidade_atacado[$i]);
                        $product->valor_minimo_venda = ($request->_valor_minimo_venda[$i]);

                        $product->nome = $request->nome_produto[$i];
                        $product->valor_compra = __convert_value_bd($request->valor_unitario[$i]);
                        $product->valor_unitario = __convert_value_bd($request->valor_venda[$i]);
                        $product->valor_atacado = ($request->_valor_atacado[$i]);
                        $product->percentual_lucro = __convert_value_bd($request->_margem[$i]);

                        $product->referencia = __convert_value_bd($request->_referencia[$i]);
                        $product->referencia_balanca = __convert_value_bd($request->_referencia_balanca[$i]);
                        $product->observacao = $request->_observacao[$i];
                        $product->observacao2 = $request->_observacao2[$i];
                        $product->observacao3 = $request->_observacao3[$i];
                        $product->observacao4 = $request->_observacao4[$i];

                        $disponibilidade = json_decode($request->_disponibilidade[$i]);

                        if(is_array($disponibilidade)){
                            foreach($disponibilidade as $d){
                                ProdutoLocalizacao::updateOrCreate([
                                    'produto_id' => $product->id, 
                                    'localizacao_id' => $d
                                ]);
                            }
                        }else{
                            ProdutoLocalizacao::updateOrCreate([
                                'produto_id' => $product->id, 
                                'localizacao_id' => $disponibilidade
                            ]);
                        }

                        $product->save();
                        //atualizar outros campos
                    }

                    $quantidade = __convert_value_bd($request->quantidade[$i]);
                    $quantidade = $quantidade * ((float)$request->conversao_estoque[$i] == 0 ? 1 : (float)$request->conversao_estoque[$i]);

                    $valorUnitario = __convert_value_bd($request->valor_unitario[$i]);
                    $valorUnitario = $valorUnitario / ((float)$request->conversao_estoque[$i] == 0 ? 1 : (float)$request->conversao_estoque[$i]);

                    $valorVenda = __convert_value_bd($request->valor_venda[$i]);
                    $valorVenda = $valorVenda / ((float)$request->conversao_estoque[$i] == 0 ? 1 : (float)$request->conversao_estoque[$i]);
                    
                    ItemNfe::create([
                        'nfe_id' => $nfe->id,
                        'produto_id' => $product->id,
                        'quantidade' => $quantidade,
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
                        'codigo_beneficio_fiscal' => $request->codigo_beneficio_fiscal[$i]
                    ]);

                    ProdutoFornecedor::updateOrCreate([
                        'produto_id' => $product->id,
                        'fornecedor_id' => $fornecedor_id
                    ]);

                    $product->valor_compra = $valorUnitario;
                    $product->valor_unitario = $valorVenda;
                    $product->save();

                    if ($product->gerenciar_estoque) {
                        $this->util->incrementaEstoque($product->id, $quantidade, null);
                        $tipo = 'incremento';
                        $codigo_transacao = $nfe->id;
                        $tipo_transacao = 'compra';

                        $this->util->movimentacaoProduto($product->id, $quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, null);
                    }
                }

                if(isset($request->chave_dfe)){
                    $dfe = ManifestoDfe::where('chave', $request->chave_dfe)->first();
                    if($dfe){
                        $dfe->compra_id = $nfe->id;
                        $dfe->tpNF = $request->tpNF_importada;
                        $dfe->save();
                    }
                }

                if($tipoPagamento){

                    for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                        if ($tipoPagamento[$i]) {
                            FaturaNfe::create([
                                'nfe_id' => $nfe->id,
                                'tipo_pagamento' => $tipoPagamento[$i],
                                'data_vencimento' => $request->data_vencimento[$i],
                                'valor' => __convert_value_bd($request->valor_fatura[$i])
                            ]);
                        }

                        if ($request->gerar_conta_pagar) {
                            ContaPagar::create([
                                'empresa_id' => $request->empresa_id,
                                'nfe_id' => $nfe->id,
                                'fornecedor_id' => $fornecedor_id,
                                'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                                'tipo_pagamento' => $tipoPagamento[$i],
                                'data_vencimento' => $request->data_vencimento[$i],
                                'local_id' => $caixa->local_id,
                            ]);
                        }
                    }
                }
                return $nfe;
            });
$descricaoLog = $nfe->fornecedor->info . " R$ " . __moeda($nfe->total);
__createLog($request->empresa_id, 'Importação XML', 'cadastrar', $descricaoLog);

session()->flash("flash_success", "Importação cadastrada!");

if ($nfe->isItemValidade()) {
    return redirect()->route('compras.info-validade', $nfe->id);
}
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>' . $e->getLine();
    die;
    __createLog(request()->empresa_id, 'Importação XML', 'erro', $e->getMessage());
    session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
}

return redirect()->route('compras.index');
}

private function cadastrarProduto($request, $i, $local_id)
{
    $disponibilidade = json_decode($request->_disponibilidade[$i]);

    $cfop = $request->cfop[$i];
    $cfopOutroEstado = '';
    $cfopEstado = '';
    $digito = substr($cfop, 0, 1);

    $cfopEstado = '5' . substr($cfop, 1, 4);
    $cfopOutroEstado = '6' . substr($cfop, 1, 4);

    $last = Produto::where('empresa_id', $request->empresa_id)
    ->orderBy('numero_sequencial', 'desc')
    ->where('numero_sequencial', '>', 0)->first();

    $padrao = null;
    if($request->padrao_id){
        $padrao = PadraoTributacaoProduto::findOrfail($request->padrao_id);
    }
    if($request->padrao_item_id[$i]){
        $padrao = PadraoTributacaoProduto::findOrfail($request->padrao_item_id[$i]);
    }

    $numeroSequencial = $last != null ? $last->numero_sequencial : 0;
    $numeroSequencial++;
    // dd($request->all());
    $p = Produto::create([
        'numero_sequencial' => $numeroSequencial,
        'empresa_id' => $request->empresa_id,
        'nome' => $request->nome_produto[$i],
        'ncm' => $request->ncm[$i],
        'padrao_id' => $request->padrao_id,
        'codigo_barras' => $request->codigo_barras[$i],
        'codigo_barras2' => $request->_codigo_barras2[$i],
        'gerenciar_estoque' => $request->_gerenciar_estoque[$i],
        'unidade' => $request->unidade[$i] ?? 'UN',
        'valor_unitario' => __convert_value_bd($request->valor_venda[$i]),
        'quantidade_atacado' => __convert_value_bd($request->_quantidade_atacado[$i]),
        'valor_minimo_venda' => ($request->_valor_minimo_venda[$i]),

        'valor_atacado' => ($request->_valor_atacado[$i]),
        'perc_red_bc' => __convert_value_bd($request->perc_red_bc[$i]),
        'cfop_estadual' => $cfopEstado,
        'cest' => $request->cest[$i],
        'cfop_outro_estado' => $cfopOutroEstado,

        'cfop_entrada_estadual' => $padrao ? $padrao->cfop_entrada_estadual : '',
        'cfop_entrada_outro_estado' => $padrao ? $padrao->cfop_entrada_outro_estado : '',

        'valor_compra' => __convert_value_bd($request->valor_unitario[$i]),

        'perc_red_bc' => __convert_value_bd($request->perc_red_bc[$i]),
        'cst_csosn' => $request->cst_csosn[$i],
        'cst_pis' => $request->cst_pis[$i],
        'cst_cofins' => $request->cst_cofins[$i],
        'cst_ipi' => $request->cst_ipi[$i],

        'perc_icms' => __convert_value_bd($request->perc_icms[$i]),
        'perc_pis' => __convert_value_bd($request->perc_pis[$i]),
        'perc_cofins' => __convert_value_bd($request->perc_cofins[$i]),
        'perc_ipi' => __convert_value_bd($request->perc_ipi[$i]),
        'estoque_minimo' => __convert_value_bd($request->_estoque_minimo[$i]),
        'referencia_xml' => $request->cProd[$i],
        'categoria_id' => $request->_categoria_id[$i] > 0 ? $request->_categoria_id[$i] : null,
        'sub_categoria_id' => $request->_sub_categoria_id[$i] > 0 ? $request->_sub_categoria_id[$i] : null,
        'marca_id' => $request->_marca_id[$i] > 0 ? $request->_marca_id[$i] : null,
        'percentual_lucro' => $request->_margem[$i] ?? 0,

        'referencia' => $request->_referencia[$i] ?? 0,
        'referencia_balanca' => $request->_referencia_balanca[$i] ?? 0,
        'exportar_balanca' => $request->_exportar_balanca[$i] ?? 0,
        'observacao' => $request->_observacao[$i] ?? '',
        'observacao2' => $request->_observacao2[$i] ?? '',
        'observacao3' => $request->_observacao3[$i] ?? '',
        'observacao4' => $request->_observacao4[$i] ?? '',
        'valor_prazo' => 0
    ]);

    if($disponibilidade == null){
        $caixa = __isCaixaAberto();
        ProdutoLocalizacao::updateOrCreate([
            'produto_id' => $p->id, 
            'localizacao_id' => $caixa->local_id
        ]);
    }else{
        if(is_array($disponibilidade)){
            foreach($disponibilidade as $d){
                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $p->id, 
                    'localizacao_id' => $d
                ]);
            }
        }else{
            ProdutoLocalizacao::updateOrCreate([
                'produto_id' => $p->id, 
                'localizacao_id' => $disponibilidade
            ]);
        }
    }

    return $p;
}

private function cadastrarTransportadora($request)
{
    if ($request->razao_social_transp) {
        $transportadora = Transportadora::create([
            'empresa_id' => $request->empresa_id,
            'razao_social' => $request->razao_social_transp,
            'nome_fantasia' => $request->nome_fantasia_transp,
            'cpf_cnpj' => $request->cpf_cnpj_transp,
            'ie' => $request->ie_transp,
            'antt' => $request->antt,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'cidade_id' => $request->cidade_id,
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
            'cidade_id' => $request->cidade_id,
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

public function infoValidade($id)
{
    $compra = Nfe::findOrFail($id);
    $produtos = [];
    foreach ($compra->itens as $i) {
        if ($i->produto->alerta_validade) {
            array_push($produtos, $i);
        }
    }
    return view('compras.info_validade', compact('produtos', 'compra'));
}

public function validade(Request $request)
{

    $produto = null;
    if($request->produto_id){
        $produto = Produto::findOrFail($request->produto_id);
    }

    $tipo_pesquisa = $request->get('tipo_pesquisa');
    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    $produto_id = $request->get('produto_id');

    $data = ItemNfe::where('nves.empresa_id', $request->empresa_id)
    ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
    ->when(!empty($start_date), function ($query) use ($start_date, $tipo_pesquisa) {
        return $query->whereDate('item_nves.'.$tipo_pesquisa, '>=', $start_date);
    })
    ->when(!empty($end_date), function ($query) use ($end_date, $tipo_pesquisa) {
        return $query->whereDate('item_nves.'.$tipo_pesquisa, '<=', $end_date);
    })
    ->when(!empty($produto_id), function ($query) use ($produto_id) {
        return $query->where('item_nves.produto_id', $produto_id);
    })
    ->where('item_nves.data_vencimento', '!=', null)
    ->orderBy('item_nves.id', 'desc')
    ->paginate(__itensPagina());
    return view('compras.validade', compact('data', 'produto'));
}

public function setarInfoValidade(Request $request)
{
    for ($i = 0; $i < sizeof($request->produto_id); $i++) {

        $item = ItemNfe::findOrFail($request->produto_id[$i]);
        $item->lote = $request->lote[$i];
        $item->data_vencimento = $request->data_vencimento[$i];
        $item->save();
    }
    session()->flash('flash_success', 'Validade definada com sucesso!');
    return redirect()->route('compras.index');
}

public function setCodigoUnico($id)
{
    $compra = Nfe::findOrFail($id);
    $produtos = [];
    foreach ($compra->itens as $i) {
        if ($i->produto->tipo_unico) {
            for ($x=0; $x<$i->quantidade; $x++) {
                array_push($produtos, $i);
            }
        }
    }
    return view('compras.set_codigo_unico', compact('produtos', 'compra'));
}

public function setarCodigoUnico(Request $request)
{
    for ($i = 0; $i < sizeof($request->produto_id); $i++) {

        ProdutoUnico::create([
            'nfe_id' => $request->nfe_id,
            'nfce_id' => null,
            'produto_id' => $request->produto_id[$i],
            'codigo' => $request->codigo[$i],
            'observacao' => $request->observacao[$i] ?? '',
            'tipo' => 'entrada',
            'em_estoque' => 1
        ]);
    }
    session()->flash('flash_success', 'Dados definidos com sucesso!');
    return redirect()->route('compras.index');
}

public function show($id){
    $data = Nfe::findOrFail($id);
    __validaObjetoEmpresa($data);

    return view('nfe.show', compact('data'));
}

public function etiqueta($id){
    $item = Nfe::findOrFail($id);
    __validaObjetoEmpresa($item);
    
    $modelos = ModeloEtiqueta::where('empresa_id', $item->empresa_id)->get();
    return view('compras.etiqueta', compact('item', 'modelos'));

}

public function etiquetaStore(Request $request, $id){
    if (!is_dir(public_path('barcode'))) {
        mkdir(public_path('barcode'), 0777, true);
    }
    $files = glob(public_path("barcode/*")); 

    foreach($files as $file){ 
        if(is_file($file)) {
            unlink($file); 
        }
    }
    $selecionados = [];
    if(!$request->produto){
        session()->flash('flash_error', "Selecione ao menos 1 produto!");
        return redirect()->back();
    }
    for($i=0; $i<sizeof($request->produto); $i++){
        $selecionados[] = $request->produto[$i];
    }

    $item = Nfe::findOrFail($id);
    $data = [];
    $cont = 0;
    foreach($item->itens as $i){
        if(in_array($i->produto_id, $selecionados)){
            $nome = $i->produto->nome;
            $codigo = $i->produto->codigo_barras;
            $valor = $i->valor_unitario;
            $unidade = $i->produto->unidade;

            if($codigo == "" || $codigo == "SEM GTIN" || $codigo == "sem gtin"){
                session()->flash('flash_error', "Produto $nome sem código de barras definido");
                return redirect()->back();
            }

            $rand = rand(1000, 9999);
            $obj = [
                'nome_empresa' => $request->nome_empresa ? true : false,
                'nome_produto' => $request->nome_produto ? true : false,
                'valor_produto' => $request->valor_produto ? true : false,
                'cod_produto' => $request->codigo_produto ? true : false,
                'tipo' => $request->tipo,
                'codigo_barras_numerico' => $request->codigo_barras_numerico ? true : false,
                'nome' => $nome,
                'codigo' => $i->produto->numero_sequencial,
                'valor' => $valor,
                'unidade' => $unidade,
                'rand' => $rand,
                'empresa' => $item->empresa->nome,
                'valor_atacado' => $i->produto->valor_atacado,
                'referencia' => $i->produto->referencia,
            ];

            $generatorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();

            $bar_code = $generatorPNG->getBarcode($codigo, $generatorPNG::TYPE_EAN_13);

            file_put_contents(public_path("barcode")."/$rand.png", $bar_code);
            $qtd = (int)$i->quantidade;
            for($i=0; $i<$qtd; $i++){
                array_push($data, $obj);
            }
        }
    }

    $quantidade_por_linhas = $request->etiquestas_por_linha;
    $quantidade = $request->quantidade_etiquetas;
    $altura = $request->altura;
    $largura = $request->largura;
    $distancia_topo = $request->distancia_etiquetas_topo;
    $distancia_lateral = $request->distancia_etiquetas_lateral;
    $tamanho_fonte = $request->tamanho_fonte;
    $tamanho_codigo = $request->tamanho_codigo_barras;
    $distancia_entre_linhas = $request->distancia_entre_linhas;
    $referencia = $request->referencia;
    $valor_atacado = $request->valor_atacado;
    // dd($data);
    return view('compras.etiqueta_print', compact('altura', 'largura', 'rand', 'codigo', 'quantidade', 'distancia_topo',
        'distancia_lateral', 'quantidade_por_linhas', 'tamanho_fonte', 'tamanho_codigo', 'data', 'distancia_entre_linhas', 'referencia', 
        'valor_atacado'));


}

public function downloadXmlImportado($id){
    $item = Nfe::findOrFail($id);
    __validaObjetoEmpresa($item);

    if (file_exists(public_path('xml_entrada/') . $item->chave_importada . '.xml')) {
        return response()->download(public_path('xml_entrada/') . $item->chave_importada . '.xml');
    }else{
        session()->flash("flash_error", "Arquivo não encontrado");
        return redirect()->back();
    }
}

}
