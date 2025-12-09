<?php

namespace App\Http\Controllers;

use App\Models\ConectaVendaConfig;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\CategoriaProduto;
use App\Models\ProdutoImagens;
use App\Models\UnidadeMedida;
use App\Models\TamanhoPizza;
use App\Models\PadraoTributacaoProduto;
use App\Models\ProdutoPizzaValor;
use App\Models\ProdutoCombo;
use App\Models\IfoodConfig;
use App\Models\CategoriaWoocommerce;
use App\Models\MovimentacaoProduto;
use App\Utils\ConectaVendaSincronizador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Utils\UploadUtil;
use App\Utils\IfoodUtil;
use App\Imports\ProdutoImport;
use App\Models\Marca;
use App\Models\ModeloEtiqueta;
use App\Models\ConfigGeral;
use App\Models\ProdutoUnico;
use App\Models\Estoque;
use App\Models\ProdutoLocalizacao;
use App\Models\GaleriaProduto;
use App\Models\ProdutoVariacao;
use App\Models\VariacaoModelo;
use App\Models\VariacaoModeloItem;
use App\Models\CategoriaProdutoIfood;
use App\Models\ListaPreco;
use App\Models\VendiZapConfig;
use App\Models\ItemListaPreco;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\MercadoLivreConfig;
use App\Utils\MercadoLivreUtil;
use App\Utils\NuvemShopUtil;
use App\Utils\EstoqueUtil;
use App\Utils\WoocommerceUtil;
use App\Rules\ValidaReferenciaBalanca;
use App\Rules\ValidaCodigoBarrasUnico;
use App\Exports\ProdutoExport;
use App\Services\IbptService;

class ProdutoController extends Controller
{

    protected $util;
    protected $utilMercadoLivre;
    protected $utilEstoque;
    protected $utilNuvemShop;
    protected $utilConectaVenda;
    protected $utilWocommerce;
    protected $utilIfood;

    public function __construct(UploadUtil $util, MercadoLivreUtil $utilMercadoLivre,
        EstoqueUtil $utilEstoque, NuvemShopUtil $utilNuvemShop, WoocommerceUtil $utilWocommerce, IfoodUtil $utilIfood, ConectaVendaSincronizador $utilConectaVenda)
    {
        $this->util = $util;
        $this->utilMercadoLivre = $utilMercadoLivre;
        $this->utilEstoque = $utilEstoque;
        $this->utilNuvemShop = $utilNuvemShop;
        $this->utilConectaVenda = $utilConectaVenda;
        $this->utilWocommerce = $utilWocommerce;
        $this->utilIfood = $utilIfood;

        $this->middleware('permission:produtos_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:produtos_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:produtos_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:produtos_delete', ['only' => ['destroy']]);
    }

    private function insertUnidadesMedida($empresa_id){
        $unidade = UnidadeMedida::where('empresa_id', $empresa_id)->first();
        if($unidade == null){
            foreach(UnidadeMedida::unidadesMedidaPadrao() as $u){
                UnidadeMedida::create([
                    'empresa_id' => $empresa_id,
                    'status' => 1,
                    'nome' => $u
                ]);
            }
        }
    }

    private function setNumeroSequencial(){
        $produtos = Produto::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->get();

        $numero = __getUltimoNumeroSequencial(request()->empresa_id, 'produtos');
        // $numero++;

        foreach($produtos as $produto){
            $numero++;
            $produto->numero_sequencial = $numero;
            $produto->save();
        }

        __setUltimoNumeroSequencial(request()->empresa_id, 'produtos', $numero);
    }

    public function index(Request $request)
    {   
        $this->setNumeroSequencial();
        $this->insertUnidadesMedida($request->empresa_id);
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $tipo = $request->tipo;
        $local_id = $request->get('local_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $com_variacao = $request->get('com_variacao');
        $ordem = $request->get('ordem');
        $com_imagem = $request->get('com_imagem');
        $status = $request->get('status');
        $gerenciar_estoque = $request->get('gerenciar_estoque');

        $data = Produto::where('empresa_id', request()->empresa_id)
        ->select('produtos.*')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->when(!empty($request->codigo_barras), function ($q) use ($request) {
            return $q->where('codigo_barras', 'LIKE', "%$request->codigo_barras%");
        })
        ->when(!empty($request->marca_id), function ($q) use ($request) {
            return $q->where('marca_id', $request->marca_id);
        })
        ->when(!empty($request->categoria_id), function ($q) use ($request) {
            return $q->where(function($t) use ($request) 
            {
                $t->where('categoria_id', $request->categoria_id)->orWhere('sub_categoria_id', $request->categoria_id);
            });
        })
        ->when(!empty($com_variacao), function ($q) use ($com_variacao) {
            return $q->where('variacao_modelo_id', $com_variacao);
        })
        ->when(!empty($status), function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->when(!empty($gerenciar_estoque), function ($q) use ($gerenciar_estoque) {
            return $q->where('gerenciar_estoque', $gerenciar_estoque == 1 ? 1 : 0);
        })
        ->when($com_imagem != '', function ($q) use ($com_imagem) {
            if($com_imagem == 1){
                return $q->where('imagem', '!=', '');
            }else{
                // return $q->where('imagem', '')->orWhere('imagem', null);
                return $q->where(function($t)
                {
                    $t->where('imagem', '')->orWhere('imagem', null);
                });
            }
        })
        ->when(!empty($tipo), function ($q) use ($tipo) {
            if($tipo == 'composto'){
                return $q->where('composto', 1);
            }
            if($tipo == 'variavel'){
                return $q->where('variacao_modelo_id', '!=', null);
            }
            if($tipo == 'combo'){
                return $q->where('combo', 1);
            }
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })
        ->when(!$ordem, function ($query) {
            return $query->orderBy('nome');
        })
        ->when($ordem, function ($query) use ($ordem) {
            return $query->orderBy($ordem, $ordem == 'created_at' ? 'desc' : 'asc');
        })
        ->distinct('produtos.id')
        ->paginate(__itensPagina());

        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
        ->orderBy('nome')
        ->where('status', 1)->get();

        $marcas = Marca::where('empresa_id', request()->empresa_id)
        ->orderBy('nome')->get();

        $empresa = Empresa::findOrFail(request()->empresa_id);

        $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();
        $tipoExibe = $configGeral && $configGeral->produtos_exibe_tabela == 0 
        ? 'card' 
        : 'tabela';

        return view('produtos.index', compact('data', 'categorias', 'empresa', 'tipoExibe', 'marcas'));
    }

    public function create(Request $request)
    {
        $this->insertUnidadesMedida($request->empresa_id);
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }

        $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
        ->where('categoria_id', null)
        ->where('status', 1)->get();
        $cardapio = 0;
        if (isset($request->cardapio)) {
            $cardapio = 1;
        }

        $delivery = 0;
        if (isset($request->delivery)) {
            $delivery = 1;
        }

        $ecommerce = 0;
        if (isset($request->ecommerce)) {
            $ecommerce = 1;
        }

        $mercadolivre = 0;
        if (isset($request->mercadolivre)) {
            $mercadolivre = 1;
        }

        $nuvemshop = 0;
        if (isset($request->nuvemshop)) {
            $nuvemshop = 1;
        }
        
        $conectavenda = 0;
        if (isset($request->conectavenda)) {
            $conectavenda = 1;
        }

        $reserva = 0;
        if (isset($request->reserva)) {
            $reserva = 1;
        }

        $woocommerce = 0;
        if (isset($request->woocommerce)) {
            $woocommerce = 1;
        }

        $ifood = 0;
        if (isset($request->ifood)) {
            $ifood = 1;
        }

        $vendizap = 0;
        if (isset($request->vendizap)) {
            $vendizap = 1;
        }

        $marcas = Marca::where('empresa_id', request()->empresa_id)->get();

        $variacoes = VariacaoModelo::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->get();

        $padraoTributacao = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->where('padrao', 1)
        ->first();

        $configMercadoLivre = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();

        $categoriasWoocommerce = CategoriaWoocommerce::where('empresa_id', request()->empresa_id)->get();
        $categoriasProdutoIfood = CategoriaProdutoIfood::where('empresa_id', request()->empresa_id)->get();

        $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        return view('produtos.create', 
            compact('listaCTSCSOSN', 'padroes', 'categorias', 'cardapio', 'marcas', 'delivery', 'variacoes', 
                'padraoTributacao', 'ecommerce', 'configMercadoLivre', 'mercadolivre', 'configGeral', 'nuvemshop',
                'reserva', 'woocommerce', 'categoriasWoocommerce', 'unidades', 'ifood', 'categoriasProdutoIfood', 'vendizap'));
    }

    public function edit(Request $request, $id)
    {
        $item = Produto::with('variacoes')->findOrFail($id);
        
        $variacoesIds = $item->variacoes->pluck('id')->toArray();
        $estoques = Estoque::whereIn('produto_variacao_id', $variacoesIds)->groupBy('produto_variacao_id')->get();
        foreach ($item->variacoes as $variacao) {
            $estoqueVariacao = $estoques[$variacao->id] ?? collect();
            $variacao->estoque_total = $estoqueVariacao->sum('quantidade');
        }
        
        __validaObjetoEmpresa($item);
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
        ->where('categoria_id', null)
        ->where('status', 1)->get();
        $cardapio = 0;
        if (isset($request->cardapio)) {
            $cardapio = 1;
        }

        $delivery = 0;
        if (isset($request->delivery)) {
            $delivery = 1;
        }

        $ecommerce = 0;
        if (isset($request->ecommerce)) {
            $ecommerce = 1;
        }

        $mercadolivre = 0;
        if (isset($request->mercadolivre)) {
            $mercadolivre = 1;
        }
        
        $conectavenda = 0;
        if (isset($request->conectavenda)) {
            $conectavenda = 1;
        }

        $marcas = Marca::where('empresa_id', request()->empresa_id)->get();
        $variacoes = VariacaoModelo::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->get();

        $configMercadoLivre = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $categoriasWoocommerce = CategoriaWoocommerce::where('empresa_id', request()->empresa_id)->get();

        $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();
        $categoriasProdutoIfood = CategoriaProdutoIfood::where('empresa_id', request()->empresa_id)->get();

        return view('produtos.edit', 
            compact('item', 'listaCTSCSOSN', 'padroes', 'categorias', 'cardapio', 'marcas', 'delivery', 'variacoes',
                'ecommerce', 'mercadolivre', 'configMercadoLivre', 'categoriasWoocommerce', 'unidades', 'configGeral', 'conectavenda' , 
                'categoriasProdutoIfood'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        $produto = null;
        try {
            $produto_imagens = [];
            if ($request->hasFile('image')) {
                $produto_imagens = $this->util->uploadFile($request->image, '/produtos');
            }
            $categorias_woocommerce = [];
            if($request->categorias_woocommerce){
                for($i=0; $i<sizeof($request->categorias_woocommerce); $i++){
                    array_push($categorias_woocommerce, $request->categorias_woocommerce[$i]);
                }
            }

            // $last = Produto::where('empresa_id', $request->empresa_id)
            // ->orderBy('numero_sequencial', 'desc')
            // ->where('numero_sequencial', '>', 0)->first();
            // $numeroSequencial = $last != null ? $last->numero_sequencial : 0;
            $numeroSequencial = __getUltimoNumeroSequencial($request->empresa_id, 'produtos');
            $numeroSequencial++;
            $request->merge([
                'woocommerce_valor' => $request->woocommerce_valor > 0 ? __convert_value_bd($request->woocommerce_valor) : __convert_value_bd($request->valor_unitario),
                'valor_unitario' => __convert_value_bd($request->valor_unitario),
                'valor_prazo' => __convert_value_bd($request->valor_prazo),
                'valor_compra' => $request->valor_compra ? __convert_value_bd($request->valor_compra) : 0,
                'valor_minimo_venda' => $request->valor_minimo_venda ? __convert_value_bd($request->valor_minimo_venda) : 0,
                // 'imagem' => $file_name,
                'codigo_anp' => $request->codigo_anp ?? '',
                'perc_glp' => $request->perc_glp ? __convert_value_bd($request->perc_glp) : 0,
                'perc_gnn' => $request->perc_gnn ? __convert_value_bd($request->perc_gnn) : 0,
                'perc_gni' => $request->perc_gni ? __convert_value_bd($request->perc_gni) : 0,
                'valor_partida' => $request->valor_partida ? __convert_value_bd($request->valor_partida) : 0,
                'unidade_tributavel' => $request->unidade_tributavel ?? '',
                'quantidade_tributavel' => $request->quantidade_tributavel ? __convert_value_bd($request->quantidade_tributavel) : 0,
                'adRemICMSRet' => $request->adRemICMSRet ? __convert_value_bd($request->adRemICMSRet) : 0,
                'pBio' => $request->pBio ? __convert_value_bd($request->pBio) : 0,
                'pOrig' => $request->pOrig ? __convert_value_bd($request->pOrig) : 0,
                'estoque_inicial' => $request->estoque_inicial ? __convert_value_bd($request->estoque_inicial) : 0,
                'indImport' => $request->indImport ?? '',
                'cUFOrig' => $request->cUFOrig ?? '',
                'cardapio' => $request->cardapio ? 1 : 0,
                'delivery' => $request->delivery ? 1 : 0,
                'ecommerce' => $request->ecommerce ? 1 : 0,
                'reserva' => $request->reserva ? 1 : 0,
                'texto_delivery' => $request->texto_delivery ?? '',
                'texto_nuvem_shop' => $request->texto_nuvem_shop ?? '',
                'mercado_livre_descricao' => $request->mercado_livre_descricao ?? '',
                'estoque_minimo' => $request->estoque_minimo ? __convert_value_bd($request->estoque_minimo) : 0,
                'mercado_livre_valor' => $request->mercado_livre_valor ? __convert_value_bd($request->mercado_livre_valor) : 0,
                'perc_icms' => $request->perc_icms ?? 0,
                'perc_pis' => $request->perc_pis ?? 0,
                'perc_cofins' => $request->perc_cofins ?? 0,
                'perc_ipi' => $request->perc_ipi ?? 0,
                'perc_ibs_uf' => $request->perc_ibs_uf ?? 0,
                'perc_ibs_mun' => $request->perc_ibs_mun ?? 0,
                'perc_cbs' => $request->perc_cbs ?? 0,
                'perc_dif' => $request->perc_dif ?? 0,
                
                'cfop_estadual' => $request->cfop_estadual ?? '',
                'cfop_outro_estado' => $request->cfop_outro_estado ?? '',
                'sub_categoria_id' => $request->sub_categoria_id ?? null,
                'valor_combo' => $request->valor_combo ? __convert_value_bd($request->valor_combo) : 0,
                'margem_combo' => $request->margem_combo ? __convert_value_bd($request->margem_combo) : 0,
                'valor_atacado' => $request->valor_atacado ? __convert_value_bd($request->valor_atacado) : 0,
                'categorias_woocommerce' => json_encode($categorias_woocommerce),

                'woocommerce_descricao' => $request->woocommerce_descricao ?? '',
                'numero_sequencial' => $numeroSequencial
            ]);

            __setUltimoNumeroSequencial($request->empresa_id, 'produtos', $numeroSequencial);

            if ($request->cardapio) {
                $request->merge([
                    'valor_cardapio' => $request->valor_cardapio ? __convert_value_bd($request->valor_cardapio) :
                    $request->valor_unitario
                ]);
            }

            if ($request->delivery) {
                $request->merge([
                    'valor_delivery' => $request->valor_delivery ? __convert_value_bd($request->valor_delivery) :
                    $request->valor_unitario,
                    'hash_delivery' => Str::random(50),
                ]);
            }

            if ($request->ecommerce) {
                $request->merge([
                    'valor_ecommerce' => $request->valor_ecommerce ? __convert_value_bd($request->valor_ecommerce) :
                    $request->valor_unitario,
                    'hash_ecommerce' => Str::random(50),
                    'texto_ecommerce' => $request->texto_ecommerce ?? ''
                ]);
                // dd($request->all());
            }else{
                $request->merge([
                    'texto_ecommerce' => ''
                ]);
            }

            $locais = isset($request->locais) ? $request->locais : [];

            $produto = DB::transaction(function () use ($request, $locais, $produto_imagens) {
                $produto        = Produto::create($request->all());
                $produto_imagem = $request['imagem'] ?? '';
                if($request->combo == 1 && $request->produto_combo_id){
                    for($i=0; $i<sizeof($request->produto_combo_id); $i++){
                        ProdutoCombo::create([
                            'produto_id' => $produto->id,
                            'item_id' => $request->produto_combo_id[$i],
                            'quantidade' => $request->quantidade_combo[$i],
                            'valor_compra' => __convert_value_bd($request->valor_compra_combo[$i]),
                            'sub_total' => __convert_value_bd($request->subtotal_combo[$i])
                        ]);
                    }
                }
                // dd($request->all());
                if($request->variavel){
                    for($i=0; $i<sizeof($request->valor_venda_variacao); $i++){
                        $produto_variacao_imagens = [];
                        if(isset($request->image_variacao[$i])){
                            $imagem                   = $request->image_variacao[$i];
                            $produto_variacao_imagens = $this->util->uploadFile($imagem, '/produtos' );
                        }

                        $dataVariacao = [
                            'produto_id' => $produto->id,
                            'descricao' => $request->descricao_variacao[$i],
                            'valor' => __convert_value_bd($request->valor_venda_variacao[$i]),
                            'codigo_barras' => $request->codigo_barras_variacao[$i],
                            'referencia' => $request->referencia_variacao[$i],
                            // 'imagem' => $file_name,
                            'variacao_modelo_item_id' => $request->variacao_modelo_item_id[$i]
                        ];
                        $variacao = ProdutoVariacao::create($dataVariacao);

                        $variacoes_imagens_this_var = [];
                        foreach($produto_variacao_imagens as $imagem) {
                            $variacoes_imagens_this_var[] = [
                                'variacao_id' => $variacao->id,
                                'imagem'      => $imagem,
                            ];
                        }
                        if($variacoes_imagens_this_var) {
                            $variacoes_imagens[] = $variacoes_imagens_this_var;
                        }
                        
                        // if($request->estoque_variacao[$i] && sizeof($locais) <= 1){
                        if( sizeof($locais) <= 1){
                            $qtd = __convert_value_bd($request->estoque_variacao[$i]);
                            $this->utilEstoque->incrementaEstoque($produto->id, $qtd, $variacao->id);
                            $transacao          = Estoque::where('produto_id', $produto->id)->first();
                            $tipo               = 'incremento';
                            $codigo_transacao   = $transacao->id;
                            $tipo_transacao     = 'alteracao_estoque';
                            $ignorar_integracao = true;
                            $this->utilEstoque->movimentacaoProduto($produto->id, $qtd, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao->id, $ignorar_integracao);
                        }
                    }
                }else{

                    if($request->estoque_inicial && sizeof($locais) <= 1){

                        $this->utilEstoque->incrementaEstoque($produto->id, $request->estoque_inicial, null);
                        $transacao = Estoque::where('produto_id', $produto->id)->orderBy('id', 'desc')->first();

                        $tipo = 'incremento';
                        if($transacao != null){
                            $codigo_transacao   = $transacao->id;
                            $tipo_transacao     = 'alteracao_estoque';
                            $ignorar_integracao = true;
                            $this->utilEstoque->movimentacaoProduto($produto->id, $request->estoque_inicial ?? 0, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $ignorar_integracao);
                        }else{
                            // combo
                            foreach($produto->itensDoCombo as $c){
                                $transacao          = Estoque::where('produto_id', $c->item_id)->first();
                                $codigo_transacao   = $transacao->id;
                                $tipo_transacao     = 'alteracao_estoque';
                                $ignorar_integracao = true;
                                $this->utilEstoque->movimentacaoProduto($c->item_id, $request->estoque_inicial ?? 0, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $ignorar_integracao);
                            }
                        }
                    }
                }

                $this->insereEmListaDePrecos($produto);

                if($request->mercadolivre){
                    $resp = $this->criaAnuncio($request, $produto);
                    if(isset($resp['erro'])){
                        DB::rollBack();
                        return [
                            'erro' => 1,
                            'msg' => $resp['msg']
                        ];
                    }else{
                        $resp = $resp['retorno'];
                        $produto->mercado_livre_link = $resp->permalink;
                        $produto->mercado_livre_id = $resp->id;
                        $produto->save();
                    }
                }

                if($request->woocommerce){
                    $resp = $this->criaProdutoWoocommerce($request, $produto);
                    if(isset($resp['erro'])){
                        DB::rollBack();
                        return [
                            'erro' => 1,
                            'msg' => $resp['msg']
                        ];
                    }else{
                        $produto->woocommerce_id = $resp['product_id'];
                        $produto->save();
                    }
                }

                if($request->nuvemshop){
                    $resp = $this->utilNuvemShop->create($request, $produto);
                }
                
                if($request->conectavenda){
                    $produto->conecta_venda_qtd_minima    = $request->conecta_venda_qtd_minima;
                    $produto->conecta_venda_multiplicador = $request->conecta_venda_multiplicador;
                    $produto->solicita_observacao         = $request->solicita_observacao;
                    $emp                                  = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();
                    if(!$emp){
                        session()->flash('flash_error', 'Conecta Venda não configurado!');
                        return $produto;
                    }
                    try {
                        $retornoConecta = $this->utilConectaVenda->create($emp, $produto);
                        if (isset($retornoConecta['produtos_ids'])) {
                            $produto->conecta_venda_id              = $produto->id;
                            $produto->conecta_venda_status          = 1;
                            $produto->conecta_venda_data_publicacao = $request->created_at;
                            $produto->save();
                        } else {
                            \Log::warning('Produto integrado, mas sem ID retornado pelo Conecta Venda.', $retornoConecta);
                            session()->flash('flash_warning', 'Produto integrado ao Conecta Venda, mas não retornou ID.');
                        }
                        
                    } catch (\Exception $e) {
                        \Log::error('Erro ao integrar com Conecta Venda: ' . $e->getMessage());
                        session()->flash('flash_error', 'Erro ao integrar com Conecta Venda: ' . $e->getMessage());
                    }
                }
                
                // Produto Imagens
                
                if( $produto_imagens ) {
                    $produto_imagens_create = [];
                    foreach($produto_imagens as $index => $img) {
                        $produto_imagens_create[] = [
                            'produto_id'          => $produto->id,
                            'produto_variacao_id' => 0,
                            'imagem'              => $img,
                            'ordem'               => $index,
                        ];
                    }
                    ProdutoImagens::create_all( $produto_imagens_create );
                }

                if( $variacoes_imagens ) {
                    $produto_imagens_create = [];

                    foreach( $variacoes_imagens as $index => $imginfo_list ) {
                        foreach( $imginfo_list as $index => $info ) {
                            $variacao_id = $info['variacao_id'];
                            $imagem      = $info['imagem'];

                            $produto_imagens_create[] = [
                                'produto_id'          => $produto->id,
                                'produto_variacao_id' => $variacao_id,
                                'imagem'              => $imagem,
                                'ordem'               => $index,
                            ];
                        }
                    }
                    ProdutoImagens::create_all( $produto_imagens_create );
                }


                return $produto;
            });



if(isset($produto['erro'])){
    session()->flash("flash_error", $produto['msg']);
    return redirect()->back();
}
session()->flash("flash_success", "Produto cadastrado!");

if(sizeof($locais) >= 2){
    for($i=0; $i<sizeof($locais); $i++){
        ProdutoLocalizacao::updateOrCreate([
            'produto_id' => $produto->id, 
            'localizacao_id' => $locais[$i]
        ]);
    }
    session()->flash("flash_success", "Produto cadastrado, informe o estoque de cada localização!");
    return redirect()->route('estoque-localizacao.define', [$produto->id]);
}else{

    if(sizeof($locais) == 1){
        ProdutoLocalizacao::updateOrCreate([
            'produto_id' => $produto->id, 
            'localizacao_id' => $locais[0]
        ]);
    }else{
        ProdutoLocalizacao::updateOrCreate([
            'produto_id' => $produto->id, 
            'localizacao_id' => $request->local_id
        ]);
    }
}

__createLog($request->empresa_id, 'Produto', 'cadastrar', $produto->nome);
if ($request->composto == true) {
    session()->flash("flash_success", "Produto cadastrado, informe a composição!");
    return redirect()->route('produto-composto.create', [$produto->id]);
}
} catch (\Exception $e) {
    // echo $e->getMessage() . "<br>";
    // echo $e->getLine() . "<br>";
    // die;
    __createLog($request->empresa_id, 'Produto', 'erro', $e->getMessage());
    session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
    return redirect()->back();
}

if ($produto->categoria && $produto->categoria->tipo_pizza) {
    return redirect()->route('produtos.tamanho-pizza', [$produto->id]);
}

if (isset($request->redirect_cardapio)) {
    return redirect()->route('produtos-cardapio.index');
}

if (isset($request->redirect_delivery)) {
    return redirect()->route('produtos-delivery.index');
}
if (isset($request->redirect_reserva)) {
    return redirect()->route('produtos-reserva.index');
}

if (isset($request->redirect_nuvemshop)) {
    return redirect()->route('nuvem-shop-produtos.index');
}

if (isset($request->redirect_ecommerce)) {
    return redirect()->route('produtos-ecommerce.index');
}
if (isset($request->redirect_mercadolivre)) {
    return redirect()->route('mercado-livre-produtos.index');
}

if (isset($request->redirect_mercadolivre)) {
    return redirect()->route('mercado-livre-produtos.index');
}

if (isset($request->redirect_vendizap) && $produto->vendizap_id != null) {
    session()->flash("swal_success", "Produto cadastrado na plataforma, você pode continuar preenchendo os campos restantes!");

    return redirect()->route('vendizap-produtos.edit', [$produto->vendizap_id]);
}

if($request->adicionar_outro){
    return redirect()->route('produtos.duplicar',[$produto->id]);
}

return redirect()->route('produtos.index');
}

private function insereEmListaDePrecos($produto){
    $listas = ListaPreco::where('empresa_id', $produto->empresa_id)
    ->get();
    foreach($listas as $l){
        $valor = 0;
        if($l->ajuste_sobre == 'valor_venda'){
            if($l->tipo == 'incremento'){
                if($l->percentual_alteracao > 0){
                    $valor = $produto->valor_unitario + ($produto->valor_unitario*($l->percentual_alteracao/100));
                }else{
                    $valor = $produto->valor_unitario + $l->valor_alteracao;
                }
            }else{
                if($l->percentual_alteracao > 0){
                    $valor = $produto->valor_unitario - ($produto->valor_unitario*($l->percentual_alteracao/100));
                }else{
                    $valor = $produto->valor_unitario - $l->valor_alteracao;
                }
            }

        }else{
            if($l->tipo == 'incremento'){
                if($l->percentual_alteracao > 0){
                    $valor = $produto->valor_compra + ($produto->valor_compra*($l->percentual_alteracao/100));
                }else{
                    $valor = $produto->valor_compra + $l->valor_alteracao;
                }
            }else{
                if($l->percentual_alteracao > 0){
                    $valor = $produto->valor_compra - ($produto->valor_compra*($l->percentual_alteracao/100));
                }else{
                    $valor = $produto->valor_compra - $l->valor_alteracao;
                }
            }
        }
        ItemListaPreco::create([
            'lista_id' => $l->id,
            'produto_id' => $produto->id,
            'valor' => $valor,
            'percentual_lucro' => $l->percentual_alteracao
        ]);
    }
}

public function update(Request $request, $id)
{
    
    try {    
        $produto = Produto::findOrFail($id);
        __validaObjetoEmpresa($produto);
        $locais = $request->locais ?? [];

        $produto_imagens = [];
        if ($request->hasFile('image')) {
                $produto_imagens = $this->util->uploadFile($request->image, '/produtos');
        }

        $categorias_woocommerce = [];
        if($request->categorias_woocommerce){
            for($i=0; $i<sizeof($request->categorias_woocommerce); $i++){
                array_push($categorias_woocommerce, $request->categorias_woocommerce[$i]);
            }
        }
        $request->merge([
            'valor_unitario'     => __convert_value_bd($request->valor_unitario),
            'valor_prazo'        => __convert_value_bd($request->valor_prazo),
            'valor_compra'       => $request->valor_compra ? __convert_value_bd($request->valor_compra) : 0,
            'valor_minimo_venda' => $request->valor_minimo_venda ? __convert_value_bd($request->valor_minimo_venda) : 0,
            //'imagem' => $file_name,
            'codigo_anp'              => $request->codigo_anp ?? '',
            'perc_glp'                => $request->perc_glp ? __convert_value_bd($request->perc_glp) : 0,
            'perc_gnn'                => $request->perc_gnn ? __convert_value_bd($request->perc_gnn) : 0,
            'perc_gni'                => $request->perc_gni ? __convert_value_bd($request->perc_gni) : 0,
            'valor_partida'           => $request->valor_partida ? __convert_value_bd($request->valor_partida) : 0,
            'unidade_tributavel'      => $request->unidade_tributavel ?? '',
            'quantidade_tributavel'   => $request->quantidade_tributavel ? __convert_value_bd($request->quantidade_tributavel) : 0,
            'adRemICMSRet'            => $request->adRemICMSRet ? __convert_value_bd($request->adRemICMSRet) : 0,
            'pBio'                    => $request->pBio ? __convert_value_bd($request->pBio) : 0,
            'pOrig'                   => $request->pOrig ? __convert_value_bd($request->pOrig) : 0,
            'indImport'               => $request->indImport ?? '',
            'cUFOrig'                 => $request->cUFOrig ?? '',
            'cardapio'                => $request->cardapio ? 1 : 0,
            'delivery'                => $request->delivery ? 1 : 0,
            'ecommerce'               => $request->ecommerce ? 1 : 0,
            'reserva'                 => $request->reserva ? 1 : 0,
            'texto_delivery'          => $request->texto_delivery ?? '',
            'texto_nuvem_shop'        => $request->texto_nuvem_shop ?? '',
            'mercado_livre_descricao' => $request->mercado_livre_descricao ?? '',
            'estoque_minimo'          => $request->estoque_minimo ? __convert_value_bd($request->estoque_minimo) : 0,
            'mercado_livre_valor'     => $request->mercado_livre_valor ? __convert_value_bd($request->mercado_livre_valor) : 0,

            'perc_icms'              => $request->perc_icms ?? 0,
            'perc_pis'               => $request->perc_pis ?? 0,
            'perc_cofins'            => $request->perc_cofins ?? 0,
            'perc_ipi'               => $request->perc_ipi ?? 0,
            'cfop_estadual'          => $request->cfop_estadual ?? '',
            'cfop_outro_estado'      => $request->cfop_outro_estado ?? '',
            'valor_combo'            => $request->valor_combo ? __convert_value_bd($request->valor_combo) : 0,
            'margem_combo'           => $request->margem_combo ? __convert_value_bd($request->margem_combo) : 0,
            'valor_atacado'          => $request->valor_atacado ? __convert_value_bd($request->valor_atacado) : 0,
            'categorias_woocommerce' => json_encode($categorias_woocommerce),
            'woocommerce_descricao'  => $request->woocommerce_descricao ?? '',

        ]);

        if ($request->cardapio) {
            $request->merge([
                'valor_cardapio' => $request->valor_cardapio ? __convert_value_bd($request->valor_cardapio) :
                $request->valor_unitario,
            ]);
        }

        if ($request->delivery) {
            $request->merge([
                'valor_delivery' => $request->valor_delivery ? __convert_value_bd($request->valor_delivery) :
                $request->valor_unitario,
                'hash_delivery' => $produto->hash_delivery != null ? $produto->hash_delivery : Str::random(50),
            ]);
        }

        if ($request->ecommerce) {
            $request->merge([
                'valor_ecommerce' => $request->valor_ecommerce ? __convert_value_bd($request->valor_ecommerce) :
                $request->valor_ecommerce,
                'hash_ecommerce' => $produto->hash_ecommerce != null ? $produto->hash_ecommerce : Str::random(50),
                'texto_ecommerce' => $request->texto_ecommerce ?? ''
            ]);
        } else{
            $request->merge([
                'texto_ecommerce' => ''
            ]);
        }
            
        $produto->fill($request->all())->save();
        
        
        if(isset($locais)){
            $locais = __getLocaisAtivoUsuario();
            $locais = $locais->pluck(['id'])->toArray();
            
            foreach($produto->locais as $l){
                if(in_array($l->localizacao_id, $locais)){
                    $l->delete();
                }
            }
            for($i=0; $i<sizeof($locais); $i++){
                ProdutoLocalizacao::updateOrCreate([
                    'produto_id'     => $produto->id,
                    'localizacao_id' => $locais[$i]
                ]);
            }
        }
            
        $variacoes_presentes = [];
        $variacoes_imagens   = [];

        if($request->variavel){
            for($i=0; $i<sizeof($request->valor_venda_variacao); $i++){
                $id_variacao = $request->variacao_id[$i] ?? 0;
                $dataVariacao = [
                    'produto_id'    => $produto->id,
                    'descricao'     => $request->descricao_variacao[$i],
                    'valor'         => __convert_value_bd($request->valor_venda_variacao[$i]),
                    'codigo_barras' => $request->codigo_barras_variacao[$i],
                    'referencia'    => $request->referencia_variacao[$i],
                ];

                $produto_variacao_imagens = [];
                if(isset($request->image_variacao[$i])){
                    $imagem                   = $request->image_variacao[$i];
                    $produto_variacao_imagens = $this->util->uploadFile($imagem, '/produtos' );
                }

                foreach($produto_variacao_imagens as $imagem) {
                    $variacoes_imagens[] = [
                        'variacao_id' => $id_variacao,
                        'imagem'      => $imagem,
                    ];
                }
                
                $variacao = ProdutoVariacao::updateOrCreate(
                    ['id' => $id_variacao],
                    $dataVariacao);
                    
                    $variacoes_presentes[] = $variacao->id;
                    
                    // if( $id_variacao == 0 && $request->estoque_variacao[$i] && isset($locais) && sizeof($locais) <= 1){
                    if( $id_variacao == 0 && isset($locais) && sizeof($locais) <= 1){
                        $qtd = __convert_value_bd($request->estoque_variacao[$i] ?? 0);
                        $this->utilEstoque->incrementaEstoque($produto->id, $qtd, $variacao->id);
                        $transacao = Estoque::where('produto_id', $produto->id)->orderBy('id', 'desc')->first();
                        $tipo = 'incremento';
                        $codigo_transacao = $transacao->id;
                        $tipo_transacao = 'alteracao_estoque';
                        $this->utilEstoque->movimentacaoProduto($produto->id, $qtd, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao->id);      
                    }
                }
            }else{
                if($request->gerenciar_estoque && isset($locais) && sizeof($locais) <= 1){
                        
                    $this->utilEstoque->incrementaEstoque($produto->id, $request->estoque_inicial ?? 0, null);
                    $transacao = Estoque::where('produto_id', $produto->id)->first();
                    
                    $tipo = 'incremento';
                    if($transacao != null){
                        $codigo_transacao = $transacao->id;
                        $tipo_transacao = 'alteracao_estoque';
                        $this->utilEstoque->movimentacaoProduto($produto->id, $request->estoque_inicial ?? 0, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id);
                    }else{
                        session()->flash("flash_error", "Esta variação $v->descricao já possui vendas ou compras não é possivel remover");
                        return redirect()->back();
                    }
                }
            }
        
                
            // Remove variações que não foram enviadas pelo request 
            // (portanto que foram deletadas no front)
            ProdutoVariacao::removerVariacoesNaoPresentes( $produto->id, $variacoes_presentes );
            $this->insereEmListaDePrecos($produto);

            // Produto Imagens

            $image_list = $request->image_list;
            $image_variacao_list = $request->image_variacao_list;

            if( $image_list ) {
                $produto_imagens_index = 0;
                $produto_imagens_create = [];
                // $produto_imagens_current = ProdutoImagens::all_by_name($produto->id);
                foreach($image_list as $index => $image) {
                    if( $image == null ) {
                        $imagem = $produto_imagens[ $produto_imagens_index ];
                        $produto_imagens_index+=1;

                        $produto_imagens_create[] = [
                            'produto_id'          => $produto->id,
                            'produto_variacao_id' => 0,
                            'imagem'              => $imagem,
                            'ordem'               => $index,
                        ];  

                    } else {
                        $img_parts = explode('/', $image);
                        $imagem  = end($img_parts);
                        $produto_imagens_create[] = [
                            'produto_id'          => $produto->id,
                            'produto_variacao_id' => 0,
                            'imagem'              => $imagem,
                            'ordem'               => $index,
                        ];  
                    }
                }
                // dd($produto_imagens_create);
                ProdutoImagens::replace_all( $produto_imagens_create, $produto->id );
            }

            if( $image_variacao_list ) {
                $produto_variacao_ids   = [];
                $produto_imagens_create = [];
                $variacoes_imagem_index = 0;

                // dd([$image_variacao_list, $variacoes_imagens ]);

                foreach( $image_variacao_list as $var_index => $variacao_list ) {
                    foreach($variacao_list as $index => $variacao) {
                        if($variacao == null) {
                            $info                    = $variacoes_imagens[ $variacoes_imagem_index ];
                            $variacoes_imagem_index += 1;
                            $imagem                  = $info['imagem'];
                            $produto_variacao_id     = $info['variacao_id'];
                            $produto_variacao_ids[$produto_variacao_id]  = true;
                            $produto_imagens_create[]                    = [
                                'produto_id'          => $produto->id,
                                'produto_variacao_id' => $produto_variacao_id,
                                'imagem'              => $imagem,
                                'ordem'               => $index,
                            ];  

                        } else {
                            
                            list( $produto_variacao_id, $image_path ) = explode("|", $variacao);
                            $img_parts = explode('/', $image_path);
                            $imagem  = end($img_parts);
                            $produto_variacao_ids[$produto_variacao_id]  = true;
                            $produto_imagens_create[] = [
                                'produto_id'          => $produto->id,
                                'produto_variacao_id' => $produto_variacao_id,
                                'imagem'              => $imagem,
                                'ordem'               => $index,
                            ];  
                        }               
                    }
                }
                ProdutoImagens::replace_all( $produto_imagens_create, $produto->id, array_keys($produto_variacao_ids) );
            }

        if($request->conectavenda){
            $produto->conecta_venda_qtd_minima    = $request->conecta_venda_qtd_minima;
            $produto->conecta_venda_multiplicador = $request->conecta_venda_multiplicador;
            $produto->solicita_observacao         = $request->solicita_observacao;
            
            $emp = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();
            if(!$emp){
                session()->flash('flash_error', 'Conecta Venda não configurado!');
                return $produto;
            }
            try {
                $retornoConecta = $this->utilConectaVenda->create($emp, $produto);
                if (isset($retornoConecta['produtos_ids'])) {
                    $produto->conecta_venda_id = $produto->id;
                    $produto->conecta_venda_status = 1;
                    $produto->conecta_venda_data_publicacao = $request->created_at;
                    $produto->save();
                } else {
                    \Log::warning('Produto integrado, mas sem ID retornado pelo Conecta Venda.', $retornoConecta);
                    session()->flash('flash_warning', 'Produto integrado ao Conecta Venda, mas não retornou ID.');
                }
                
            } catch (\Exception $e) {
                \Log::error('Erro ao integrar com Conecta Venda: ' . $e->getMessage());
                session()->flash('flash_error', 'Erro ao integrar com Conecta Venda: ' . $e->getMessage());
            }
        } 

        if(isset($request->locais)){

            $locais = __getLocaisAtivoUsuario();
            $locais = $locais->pluck(['id'])->toArray();

            foreach($produto->locais as $l){
                if(in_array($l->localizacao_id, $locais)){
                    $l->delete();
                }
            }
            for($i=0; $i<sizeof($request->locais); $i++){
                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $produto->id, 
                    'localizacao_id' => $request->locais[$i]
                ]);
            }
        }

        __createLog($request->empresa_id, 'Produto', 'editar', $produto->nome);
        if ($request->composto == true) {
            session()->flash("flash_success", "Produto atualizado, informe a composição!");
            return redirect()->route('produto-composto.create', [$produto->id]);
        }
    } catch (\Exception $e) {
        __createLog($request->empresa_id, 'Produto', 'erro', $e->getMessage());
        session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
    }

    if (isset($request->redirect_cardapio)) {
        return redirect()->route('produtos-cardapio.index');
    }
    if (isset($request->redirect_delivery)) {
        return redirect()->route('produtos-delivery.index');
    }
    if (isset($request->redirect_reserva) || $produto->reserva) {
        return redirect()->route('produtos-reserva.index');
    }
    if (isset($request->redirect_ecommerce)) {
        return redirect()->route('produtos-ecommerce.index');
    }
    if (isset($request->redirect_mercadolivre)) {
        return redirect()->route('mercado-livre-produtos.index');
    }
    return redirect()->route('produtos.index');
}

public function destroy($id)
{
    $item = Produto::findOrFail($id);
    __validaObjetoEmpresa($item);
            
            if(plano_ativo("Conecta Venda") && $item->conecta_venda_id){
                $desativar      = true;
                $empresa_id     = \Auth::user()->empresa->empresa_id;
                $conecta_config = ConectaVendaConfig::where('empresa_id', $empresa_id)->first();
                $this->utilConectaVenda->create( $conecta_config, $item, $desativar);
            }
            
    try {
        $descricaoLog = $item->nome;

        $itemNfe = \App\Models\ItemNfe::where('produto_id', $item->id)
        ->first();

        if($itemNfe){
            session()->flash("flash_warning", "Não é possivel remover produto que foi comprado ou vendido!");
            return redirect()->back();
        }
        DB::transaction(function () use ($item) {

            if($item->estoque){
                $item->estoque->delete();
            }
            $item->movimentacoes()->delete();
            $item->variacoes()->delete();
            $item->variacoesMercadoLivre()->delete();
            $item->itemLista()->delete();
            $item->ibpt()->delete();
            // $item->itemNfe()->delete();
            // $item->estoque()->delete();
            $item->galeria()->delete();
            
            // $item->itemNfce()->delete();
            $item->itemCarrinhos()->delete();
            $item->composicao()->delete();
            $item->itemPreVenda()->delete();
            $item->itensDoCombo()->delete();
            $item->fornecedores()->delete();
            $item->locais()->delete();

            \App\Models\EstoqueAtualProduto::where('produto_id', $item->id)->delete();
            \App\Models\ImpressoraPedidoProduto::where('produto_id', $item->id)->delete();

            if($item->woocommerce_id){
                $woocommerceClient = $this->utilWocommerce->getConfig($item->empresa_id);
                $woocommerceClient->delete("products/$item->woocommerce_id", ['force' => true]);
            }

            $item->delete();
        });

        $this->util->unlinkImage($item, '/produtos');
        __createLog(request()->empresa_id, 'Produto', 'excluir', $descricaoLog);
        session()->flash("flash_success", "Produto removido!");
    } catch (\Exception $e) {
        __createLog(request()->empresa_id, 'Produto', 'erro', $e->getMessage());
        session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
    }
    return redirect()->back();
}

public function destroySelecet(Request $request)
{
    $removidos = 0;
    for($i=0; $i<sizeof($request->item_delete); $i++){
        $item = Produto::findOrFail($request->item_delete[$i]);
        $woocommerceClient = $this->utilWocommerce->getConfig($item->empresa_id);
        $descricaoLog = $item->nome;

        try {
            $item->variacoes()->delete();
            $item->variacoesMercadoLivre()->delete();
            $item->itemLista()->delete();
            $item->itemNfe()->delete();
            $item->itemNfce()->delete();
            $item->movimentacoes()->delete();
            $item->composicao()->delete();
            $item->itemPreVenda()->delete();
            $item->locais()->delete();
            \App\Models\ImpressoraPedidoProduto::where('produto_id', $item->id)->delete();
            \App\Models\EstoqueAtualProduto::where('produto_id', $item->id)->delete();
            
            if($item->estoque){
                $item->estoque->delete();
            }

            if($item->woocommerce_id){
                $woocommerceClient->delete("products/$item->woocommerce_id", ['force' => true]);
            }
            $item->delete();
            $this->util->unlinkImage($item, '/produtos');

            $removidos++;
            __createLog(request()->empresa_id, 'Produto', 'excluir', $descricaoLog);

        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->route('produtos.index');
        }
    }

    session()->flash("flash_success", "Total de itens removidos: $removidos!");
    return redirect()->route('produtos.index');
}

public function desactiveSelecet(Request $request)
{
    $alterados = 0;
    for($i=0; $i<sizeof($request->item_delete); $i++){
        $item = Produto::findOrFail($request->item_delete[$i]);
        $descricaoLog = $item->nome;

        try {
            $item->status = 0;
            $item->save();
            $alterados++;
            __createLog(request()->empresa_id, 'Produto', 'editar', $descricaoLog);

        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
    }

    session()->flash("flash_success", "Total de itens alterados: $alterados");
    return redirect()->back();
}

private function __validate(Request $request)
{
    $rules = [
        'nome' => 'required',
            // 'codigo_barras' => 'required',
            // 'ncm' => 'required',
        'descricao' => 'max:255',
        'descricao_en' => 'max:255',
        'descricao_es' => 'max:255',
        'unidade' => 'required',
        'cst_csosn' => 'required',
        'cst_pis' => 'required',
        'cst_cofins' => 'required',
        'cst_ipi' => 'required',
        'valor_unitario' => 'required',
        'codigo_barras' => [new ValidaCodigoBarrasUnico($request->empresa_id)],
        'referencia_balanca' => [new ValidaReferenciaBalanca($request->empresa_id)],

    ];

    $messages = [
        'nome.required' => 'Campo Obrigatório',
        'codigo_barras.required' => 'Campo Obrigatório',
        'ncm.required' => 'Campo Obrigatório',
        'cest.required' => 'Campo Obrigatório',
        'unidade.required' => 'Campo Obrigatório',
        'perc_icms.required' => 'Campo Obrigatório',
        'perc_pis.required' => 'Campo Obrigatório',
        'perc_cofins.required' => 'Campo Obrigatório',
        'perc_ipi.required' => 'Campo Obrigatório',
        'cst_csosn.required' => 'Campo Obrigatório',
        'cst_pis.required' => 'Campo Obrigatório',
        'cst_cofins.required' => 'Campo Obrigatório',
        'cst_ipi.required' => 'Campo Obrigatório',
        'valor_unitario.required' => 'Campo Obrigatório',
        'descricao.max' => 'Máximo de 255 caracteres',
        'descricao_es.max' => 'Máximo de 255 caracteres',
        'descricao_en.max' => 'Máximo de 255 caracteres',
    ];

    $this->validate($request, $rules, $messages);
}

public function import()
{
    return view('produtos.import');
}

public function export(){
    $data = Produto::where('empresa_id', request()->empresa_id)
    ->orderBy('nome')
    ->get();

    $file = new ProdutoExport($data);
    return Excel::download($file, 'produtos.xlsx');

}

public function downloadModelo()
{
    return response()->download(public_path('files/') . 'import_products_csv_template.xlsx');
}

public function storeModelo(Request $request)
{
    if ($request->hasFile('file')) {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', -1);

        $rows = Excel::toArray(new ProdutoImport, $request->file);
        $retornoErro = $this->validaArquivo($rows);
        $cont = 0;
        $contDuplicados = 0;
        if(isset($request->locais)){
            $locais = isset($request->locais) ? $request->locais : [];
        }

        if ($retornoErro == "") {
            foreach ($rows as $row) {
                foreach ($row as $key => $r) {

                    if ($r[0] != 'NOME' && isset($r[0])) {

                        try {
                            $data = $this->preparaObjeto($r, $request->empresa_id);

                            $produtoDuplicado = Produto::where('empresa_id', $request->empresa_id)
                            ->where('nome', $data['nome'])->first();

                            if($produtoDuplicado == null){
                                $item = Produto::create($data);

                                if(isset($request->locais)){
                                    foreach($locais as $l){
                                        $local = ProdutoLocalizacao::updateOrCreate([
                                            'produto_id' => $item->id, 
                                            'localizacao_id' => $l
                                        ]);

                                        if($data['estoque']){
                                            $this->utilEstoque->incrementaEstoque($item->id, $data['estoque'], null, $local->localizacao_id);

                                            $transacao = Estoque::where('produto_id', $item->id)->orderBy('id', 'desc')->first();
                                            $tipo = 'incremento';
                                            $codigo_transacao = $transacao->id;
                                            $tipo_transacao = 'alteracao_estoque';
                                            $this->utilEstoque->movimentacaoProduto($item->id, $data['estoque'], $tipo, 
                                                $codigo_transacao, $tipo_transacao, \Auth::user()->id);
                                        }
                                    }
                                }else{
                                    $local = ProdutoLocalizacao::updateOrCreate([
                                        'produto_id' => $item->id, 
                                        'localizacao_id' => $request->local_id
                                    ]);

                                    if($data['estoque']){
                                        $this->utilEstoque->incrementaEstoque($item->id, $data['estoque'], null, $local->localizacao_id);

                                        $transacao = Estoque::where('produto_id', $item->id)->orderBy('id', 'desc')->first();
                                        $tipo = 'incremento';
                                        $codigo_transacao = $transacao->id;
                                        $tipo_transacao = 'alteracao_estoque';
                                        $this->utilEstoque->movimentacaoProduto($item->id, $data['estoque'], $tipo, 
                                            $codigo_transacao, $tipo_transacao, \Auth::user()->id);
                                    }
                                }

                                $cont++;
                            }else{
                                // dd($data);
                                $produtoDuplicado->codigo_barras = $data['codigo_barras'];
                                $produtoDuplicado->ncm = $data['ncm'];
                                $produtoDuplicado->unidade = $data['unidade'];
                                $produtoDuplicado->cest = $data['cest'];
                                $produtoDuplicado->perc_icms = $data['perc_icms'];
                                $produtoDuplicado->perc_pis = $data['perc_pis'];
                                $produtoDuplicado->perc_cofins = $data['perc_cofins'];
                                $produtoDuplicado->perc_ipi = $data['perc_ipi'];
                                $produtoDuplicado->perc_red_bc = $data['perc_red_bc'];
                                $produtoDuplicado->cst_csosn = $data['cst_csosn'];
                                $produtoDuplicado->cst_pis = $data['cst_pis'];
                                $produtoDuplicado->cst_cofins = $data['cst_cofins'];
                                $produtoDuplicado->cst_ipi = $data['cst_ipi'];
                                $produtoDuplicado->valor_unitario = $data['valor_unitario'];
                                $produtoDuplicado->cfop_estadual = $data['cfop_estadual'];
                                $produtoDuplicado->cfop_outro_estado = $data['cfop_outro_estado'];
                                $produtoDuplicado->cEnq = $data['cEnq'];
                                $produtoDuplicado->categoria_id = $data['categoria_id'];
                                $produtoDuplicado->gerenciar_estoque = $data['gerenciar_estoque'];
                                $produtoDuplicado->codigo_beneficio_fiscal = $data['codigo_beneficio_fiscal'];
                                $produtoDuplicado->valor_compra = $data['valor_compra'];
                                $produtoDuplicado->cfop_entrada_estadual = $data['cfop_entrada_estadual'];
                                $produtoDuplicado->cfop_entrada_outro_estado = $data['cfop_entrada_outro_estado'];
                                $produtoDuplicado->estoque_minimo = $data['estoque_minimo'];

                                $produtoDuplicado->save();
                                if(isset($request->locais)){
                                    foreach($locais as $l){
                                        $local = ProdutoLocalizacao::updateOrCreate([
                                            'produto_id' => $produtoDuplicado->id, 
                                            'localizacao_id' => $l
                                        ]);
                                    }
                                }
                                $contDuplicados++;
                            }
                        } catch (\Exception $e) {
                            session()->flash('flash_error', __getError($e));
                        }
                    }
                }
            }
            session()->flash('flash_success', 'Total de produtos importados: ' . $cont);
            if($contDuplicados > 0){
                session()->flash('flash_warning', 'Total de produtos duplicados: ' . $contDuplicados);
            }
            return redirect()->back();
        } else {
            session()->flash('flash_error', $retornoErro);
            return redirect()->back();
        }
    } else {
        session()->flash('flash_error', 'Selecione o arquivo modelo para importar!!');
        return redirect()->back();
    }
}

private function mask($val, $mask)
{
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
        if ($mask[$i] == '#') {
            if (isset($val[$k])) {
                $maskared .= $val[$k++];
            }
        } else {
            if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
        }
    }

    return $maskared;
}

private function preparaObjeto($linha, $empresa_id)
{
    $ncm = trim((string)$linha[4]);

    if(!str_contains($ncm, ".")){
        $ncm = __mask($ncm, '####.##.##');
    }
    // dd($ncm);

    $categoria = CategoriaProduto::where('empresa_id', $empresa_id)
    ->where('status', 1)
    ->where('nome', $linha[1])->first();

    $valorUnitario = str_replace("R$", "", $linha[2]);
    $valorCompra = str_replace("R$", "", $linha[3]);
    $valorUnitario = __convert_value_bd($valorUnitario);
    $valorCompra = $valorCompra ? __convert_value_bd($valorCompra) : 1;
    $percentualLucro = 0;

    if($valorUnitario > 0 && $valorCompra > 0){
        $percentualLucro = ($valorUnitario/$valorCompra)*100;
    }

    $data = [
        'empresa_id' => $empresa_id,
        'nome' => $linha[0],
        'codigo_barras' => $linha[5],
        'ncm' => $ncm,
        'cest' => $linha[6],
        'unidade' => $linha[17] != '' ? $linha[17] : 'UN',
        'perc_icms' => $linha[20] != '' ? __convert_value_bd($linha[20]) : 0,
        'perc_pis' => $linha[21] != '' ? __convert_value_bd($linha[21]) : 0,

        'perc_cofins' => $linha[22] != '' ? __convert_value_bd($linha[22]) : 0,
        'perc_ipi' => $linha[23] != '' ? __convert_value_bd($linha[23]) : 0,
        'cst_csosn' => $linha[7],
        'cst_pis' => $linha[8],
        'cst_cofins' => $linha[9],
        'cst_ipi' => $linha[10],
        'valor_unitario' => $valorUnitario,
        'origem' => $linha[12] != '' ? $linha[12] : 1,
        'perc_red_bc' => $linha[11] != '' ? __convert_value_bd($linha[11]) : 0,
        'cfop_estadual' => $linha[14],
        'cfop_outro_estado' => $linha[15],
        'cEnq' => $linha[13],
        'categoria_id' => $categoria != null ? $categoria->id : null,
        'gerenciar_estoque' => $linha[19] != '' ? $linha[19] : 0,
        'codigo_beneficio_fiscal' => $linha[16],
        'valor_compra' => $valorCompra,

        'cfop_entrada_estadual' => $linha[24],
        'cfop_entrada_outro_estado' => $linha[25],
        'estoque' => $linha[26],
        'estoque_minimo' => $linha[27] ?? 0,
        'referencia' => $linha[28] ?? '',
        'percentual_lucro' => $percentualLucro,
        'valor_prazo' => 0
    ];
    return $data;
}

private function validaArquivo($rows)
{
    $cont = 1;
    $msgErro = "";
    foreach ($rows as $row) {
        foreach ($row as $key => $r) {
            if(isset($r[0])){

                $nome = $r[0];
                $valorVenda = $r[2];
                $ncm = $r[4];
                $cstCsosn = $r[7];
                $cstPis = $r[8];
                $cstCofins = $r[9];
                $cstIpi = $r[10];
                $cfopEstado = $r[14];
                $cfopOutroEstado = $r[15];

                // dd($r);

                if ($r[27] == null && $key == 0) {
                    $msgErro .= "O arquivo deve conter 28 colunas";
                }

                if (strlen($nome) == 0) {
                    $msgErro .= "Coluna nome em branco na linha: $cont | ";
                }

                if (strlen($valorVenda) == 0) {
                    $msgErro .= "Coluna valor venda em branco na linha: $cont | ";
                }

                if (strlen($ncm) == 0) {
                    $msgErro .= "Coluna NCM em branco na linha: $cont | ";
                }

                if (strlen($ncm) < 8 && $key > 0) {
                    $msgErro .= "Coluna NCM deve conter 8 caracteres linha: $cont | ";
                }

                if (strlen($cstCsosn) == 0) {
                    $msgErro .= "Coluna CST/CSOSN em branco na linha: $cont | ";
                }
                if (strlen($cstPis) == 0) {
                    $msgErro .= "Coluna CST/PIS em branco na linha: $cont | ";
                }
                if (strlen($cstCofins) == 0) {
                    $msgErro .= "Coluna CST/COFINS em branco na linha: $cont | ";
                }
                if (strlen($cstIpi) == 0) {
                    $msgErro .= "Coluna CST/IPI em branco na linha: $cont | ";
                }

                if (strlen($cfopEstado) == 0) {
                    $msgErro .= "Coluna CFOP estado em branco na linha: $cont | ";
                }
                if (strlen($cfopOutroEstado) == 0) {
                    $msgErro .= "Coluna CFOP outro estado em branco na linha: $cont | ";
                }

                if ($msgErro != "") {
                    return $msgErro;
                }
                $cont++;
            }
        }

    }

    return $msgErro;
}

public function gerarCodigoEan()
{
    try {
        $rand = rand(11111, 99999);
        $code = $this->incluiDigito('7891000' . $rand);
        return response()->json($code, 200);
    } catch (\Exception $e) {
        return response()->json($e->getMessage(), 401);
    }
}

private function incluiDigito($code)
{
    $weightflag = true;
    $sum = 0;
    for ($i = strlen($code) - 1; $i >= 0; $i--) {
        $sum += (int)$code[$i] * ($weightflag ? 3 : 1);
        $weightflag = !$weightflag;
    }
    return $code . (10 - ($sum % 10)) % 10;
}


public function show($id){
    $item = Produto::findOrFail($id);
    __validaObjetoEmpresa($item);
    $data = MovimentacaoProduto::where('produto_id', $id)
    ->orderBy('id', 'desc')
    ->get();

    $produtoUnico = ProdutoUnico::where('produto_id', $id)
    ->get();
    return view('produtos.show', compact('item', 'data', 'produtoUnico'));
}

public function movimentacao($id){
    $item = MovimentacaoProduto::findOrFail($id);
    __validaObjetoEmpresa($item);
    if($item->tipo_transacao == 'venda_nfe'){
        return redirect()->route('nfe.show', [$item->codigo_transacao]);
    }
    if($item->tipo_transacao == 'venda_nfce'){
        return redirect()->route('nfce.show', [$item->codigo_transacao]);
    }
    if($item->tipo_transacao == 'alteracao_estoque'){
        return redirect()->route('estoque.index', ['produto='.$item->produto->nome]);
    }
    if($item->tipo_transacao == 'compra'){
        return redirect()->route('compras.show', [$item->codigo_transacao]);
    }
}

public function removeImagem($id){
    $item = Produto::findOrFail($id);
    try{
        $this->util->unlinkImage($item, '/produtos');
        $item->imagem = '';
        $item->save();
        session()->flash("flash_success", "Imagem removida");
    } catch (\Exception $e) {
        session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
    }
    return redirect()->back();
}

public function galeria($id){
    $item = Produto::findOrFail($id);
    __validaObjetoEmpresa($item);
    return view('produtos.galeria', compact('item'));
}

public function storeImage(Request $request, $id){
    $item = Produto::findOrFail($id);
    __validaObjetoEmpresa($item);
    if ($request->hasFile('image')) {

        $file_name = $this->util->uploadImage($request, '/produtos');
        GaleriaProduto::create([
            'produto_id' => $id,
            'imagem' => $file_name
        ]);
        session()->flash("flash_success", "Imagem cadastrada!");
    }else{
        session()->flash("flash_error", "Selecione o arquivo!");
    }
    return redirect()->back();
}

public function destroyImage($id){
    $item = GaleriaProduto::findOrFail($id);
    try {
        $item->delete();

        $this->util->unlinkImage($item, '/produtos');

        session()->flash("flash_success", "Imagem removida!");
    } catch (\Exception $e) {
        session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
    }
    return redirect()->back();
}

public function duplicar(Request $request, $id)
{
    $item = Produto::findOrFail($id);
    __validaObjetoEmpresa($item);
    $empresa = Empresa::findOrFail(request()->empresa_id);

    $listaCTSCSOSN = Produto::listaCSOSN();
    if ($empresa->tributacao == 'Regime Normal') {
        $listaCTSCSOSN = Produto::listaCST();
    }
    $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();
    $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
    ->where('status', 1)->get();
    $cardapio = 0;
    if (isset($request->cardapio)) {
        $cardapio = 1;
    }

    $delivery = 0;
    if (isset($request->delivery)) {
        $delivery = 1;
    }

    $ecommerce = 0;
    if (isset($request->ecommerce)) {
        $ecommerce = 1;
    }
    $marcas = Marca::where('empresa_id', request()->empresa_id)->get();
    $variacoes = VariacaoModelo::where('empresa_id', $request->empresa_id)
    ->where('status', 1)->get();

    $configMercadoLivre = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
    ->first();

    $categoriasWoocommerce = CategoriaWoocommerce::where('empresa_id', request()->empresa_id)->get();
    $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
    ->where('status', 1)->get();

    $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
    ->first();
    $categoriasProdutoIfood = CategoriaProdutoIfood::where('empresa_id', request()->empresa_id)->get();

    return view('produtos.duplicar', 
        compact('item', 'listaCTSCSOSN', 'padroes', 'categorias', 'cardapio', 'marcas', 'delivery', 'variacoes', 
            'ecommerce', 'configMercadoLivre', 'categoriasWoocommerce', 'unidades', 'configGeral', 'categoriasProdutoIfood'));
}

private function __validaToken(){
    $retorno = $this->utilMercadoLivre->refreshToken(request()->empresa_id);
    if($retorno != 'token valido!'){
        if(!isset($retorno->access_token)){
            dd($retorno);
        }
    }
}

private function criaProdutoIfood($request, $produto){

    $data = [
        'name' => $request->nome,
        'serving' => 'SERVES_1',
    ];

    if($request->ifood_descricao){
        $data['description'] = $request->ifood_descricao;
    }
    if($request->codigo_barras){
        $data['ean'] = $request->codigo_barras;
    }

    if($produto->imagem){
        $pathImage = public_path('uploads/produtos/'.$produto->imagem);
        if(file_exists($pathImage)){
            $mimeType = explode(".", $produto->imagem);
            $image = "data:image/$mimeType[1];base64,".base64_encode(file_get_contents($pathImage));
            $data['image'] = $image;
        }
    }

    // dd($data);

    $config = IfoodConfig::
    where('empresa_id', $request->empresa_id)
    ->first();

    $productIfood = $this->utilIfood->storeProduct($config, $data);

    if(isset($productIfood->error)){
        session()->flash("flash_error", $productIfood->error->details[0]->message);
        return redirect()->back();
    }

    $categoria = CategoriaProdutoIfood::findOrFail($request->ifood_categoria_id);

    $dataAssociation = [
        'status' => 'AVAILABLE',
        'price' => [
            'value' => $request->ifood_valor ? __convert_value_bd($request->ifood_valor) : __convert_value_bd($request->valor_unitario)
        ]
    ];

    if($request->estoque_inicial){
        $dataEstoque = [
            "productId" => $productIfood->id,
            'amount' => (float)$request->estoque_inicial
        ];

        $stock = $this->utilIfood->addStockProduct($config, $dataEstoque);
        // dd($stock);
    }

    return $this->utilIfood->associationProductCategory($config, $categoria->ifood_id, $productIfood->id, $dataAssociation);

}

private function criaProdutoVendiZap($request, $produto){
    $config = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();
    $url = "https://app.vendizap.com/api";

    $ch = curl_init();
    $headers = [
        "X-Auth-Id: " . $config->auth_id,
        "X-Auth-Secret: " . $config->auth_secret,
        'Content-Type: application/json'
    ];

    $data = [
        'descricao' => $request->nome,
        'preco' => $request->vendizap_valor ? __convert_value_bd($request->vendizap_valor) : __convert_value_bd($request->valor_unitario)
    ];

    if(sizeof($produto->variacoes) > 0){
        if($data['preco'] <= 0){
            $data['preco'] = $produto->variacoes[0]->valor;
        }

        $variacoes = $this->montaVariaveisVendiZap($produto, $data['preco']);
        $data['variacoes'] = $variacoes;
    }

    if($produto->imagem){
        $image = env('APP_URL') . '/uploads/produtos/'.$produto->imagem;
        $data['imagens'] = [$image];
    }

    // dd($data);

    curl_setopt($ch, CURLOPT_URL, $url . '/produtos');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");


    $data = json_decode(curl_exec($ch));
    curl_close($ch);
    // dd($data);
    if(!isset($data->erros)){
        $produto->vendizap_id = $data->id;
        $produto->save();

        $this->setarEstoqueVendiZap($produto, $config);
    }else{
        session()->flash('flash_error', $data->erros[0]);
    }

}

private function setarEstoqueVendiZap($produto, $config){

    if($produto->gerenciar_estoque){
        if($produto->sub_variacao_modelo_id){
            $key1 = [];
            $key2 = [];
            foreach($produto->variacoes as $v){
                if($v->estoque){
                    $nome = explode(" ", $v->descricao);
                    
                    // dd($nome);

                    $variacao_id = null;
                    $variacao_id2 = null;
                    $variavel_id = null;
                    $variavel_id2 = null;
                    $quantidade = 0;
                    foreach($nome as $key => $k){
                        $varicaoModeloItem = VariacaoModeloItem::select('variacao_modelo_items.*')
                        ->join('variacao_modelos', 'variacao_modelos.id', '=', 'variacao_modelo_items.variacao_modelo_id')
                        ->where('variacao_modelo_items.nome', $k)->first();


                        if($key == 0){
                            $variacao_id = $varicaoModeloItem->variacaoModelo->vendizap_id;
                            $variavel_id = $varicaoModeloItem->vendizap_id;
                        }else{
                            $variacao_id2 = $varicaoModeloItem->variacaoModelo->vendizap_id;
                            $variavel_id2 = $varicaoModeloItem->vendizap_id;
                        }
                    }


                    $ch = curl_init();
                    $headers = [
                        "X-Auth-Id: " . $config->auth_id,
                        "X-Auth-Secret: " . $config->auth_secret,
                        'Content-Type: application/json'
                    ];

                    $data = [
                        'quantidade' => $v->estoque->quantidade,
                        'combinacao' => [
                            $variacao_id => $variavel_id,
                            $variacao_id2 => $variavel_id2,
                        ]
                    ];

                    $url = "https://app.vendizap.com/api";

                    curl_setopt($ch, CURLOPT_URL, $url . '/estoque/'.$produto->vendizap_id);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

                    $data = json_decode(curl_exec($ch));
                    curl_close($ch);
                    // dd($data);
                }
            }
        }else{
            // estoque variacão simples
            foreach($produto->variacoes as $v){
                if($v->estoque){

                    $varicaoModeloItem = VariacaoModeloItem::select('variacao_modelo_items.*')
                    ->join('variacao_modelos', 'variacao_modelos.id', '=', 'variacao_modelo_items.variacao_modelo_id')
                    ->where('variacao_modelo_items.nome', $v->descricao)->first();

                    $variacao_id = $varicaoModeloItem->variacaoModelo->vendizap_id;
                    $variavel_id = $varicaoModeloItem->vendizap_id;

                    $ch = curl_init();
                    $headers = [
                        "X-Auth-Id: " . $config->auth_id,
                        "X-Auth-Secret: " . $config->auth_secret,
                        'Content-Type: application/json'
                    ];

                    $data = [
                        'quantidade' => $v->estoque->quantidade,
                        'combinacao' => [
                            $variacao_id => $variavel_id,
                        ]
                    ];
                    // dd($data);

                    $url = "https://app.vendizap.com/api";

                    curl_setopt($ch, CURLOPT_URL, $url . '/estoque/'.$produto->vendizap_id);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

                    $data = json_decode(curl_exec($ch));
                    curl_close($ch);

                }
            }
        }
    }
}

private function montaVariaveisVendiZap($produto, $preco){

    $variacoes = [];
    $v = $produto->variacoes[0]->variacao_modelo_item_id;
    $varicaoModeloItem = VariacaoModeloItem::findOrFail($v);
    $vTemp = [];
    $indice = 0;
    if($varicaoModeloItem->variacaoModelo && $varicaoModeloItem->variacaoModelo->vendizap_id){
        $v = $varicaoModeloItem->variacaoModelo->vendizap_id;
        if(!$produto->sub_variacao_modelo_id){

            for($i=0; $i<sizeof($produto->variacoes); $i++){
                if($i == 0){
                    $variacoes[$indice]['id'] = $v;
                }

                if(isset($produto->variacoes[$i])){

                    $v = $produto->variacoes[$i]->variacao_modelo_item_id;
                    $varicaoModeloItem1 = VariacaoModeloItem::findOrFail($v)->variacaoModelo;
                    $v2 = isset($produto->variacoes[$i+1]) ? $produto->variacoes[$i+1]->variacao_modelo_item_id : null;
                    if($v2){
                        $varicaoModeloItem2 = VariacaoModeloItem::findOrFail($v2)->variacaoModelo;
                    }

                    // dd($varicaoModeloItem1);
                    // die;
                    if($varicaoModeloItem2 && $varicaoModeloItem1->vendizap_id != $varicaoModeloItem2->vendizap_id){
                        $variacoes[$indice]['variaveis'] = $vTemp;
                        $indice++;
                    }else{

                        $valor = $produto->variacoes[$i]->valor;
                        if($produto->variacoes[$i]->valor > 0){
                            $valor = $produto->variacoes[$i]->valor - $preco;
                        }

                        $v = $produto->variacoes[$i]->variacao_modelo_item_id;
                        $varicaoModeloItem = VariacaoModeloItem::findOrFail($v);
                        $vTemp[] = [
                            'id' => $varicaoModeloItem->vendizap_id,
                            'preco' => $valor
                        ];
                    }

                    if(!isset($produto->variacoes[$i+1])){
                        $variacoes[$indice]['variaveis'] = $vTemp;
                        $vTemp = [];
                    }
                }
            }
        }else{
            // multi variação
            $variacoes = $this->preparaVariacoesMulti($produto, $preco);
        }
    }

    return $variacoes;
}

private function preparaVariacoesMulti($produto, $preco){
    $key1 = [];
    $key2 = [];
    // $request = request();
    // for($i=0; $i<sizeof($request->variacao_modelo_item_id); $i++){
    //     echo $request->variacao_modelo_item_id[$i] . "<br>";
    // }
    foreach($produto->variacoes as $v){
        $nome = explode(" ", $v->descricao);
        // echo $nome[0];
        if(!in_array($nome[0], $key1)){
            $key1[] = $nome[0];
        }

        if(!in_array($nome[1], $key2)){
            $key2[] = $nome[1];
        }
    }

    $variacoes = [];
    $indice = 0;
    $cont = 0;

    $vTemp = [];
    foreach($key1 as $k){
        $varicaoModeloItem = VariacaoModeloItem::select('variacao_modelo_items.*')
        ->join('variacao_modelos', 'variacao_modelos.id', '=', 'variacao_modelo_items.variacao_modelo_id')
        ->where('variacao_modelo_items.nome', $k)->first();
        // dd($varicaoModeloItem->variacaoModelo);
        $vTemp[] = [
            'id' => $varicaoModeloItem->vendizap_id,
            'preco' => $produto->variacoes[$cont]->valor - $preco
        ];
        $cont++;
    }
    $variacoes[$indice]['id'] = $varicaoModeloItem->variacaoModelo->vendizap_id;
    $variacoes[$indice]['variaveis'] = $vTemp;

    $vTemp = [];
    $indice++;
    foreach($key2 as $k){
        $varicaoModeloItem = VariacaoModeloItem::select('variacao_modelo_items.*')
        ->join('variacao_modelos', 'variacao_modelos.id', '=', 'variacao_modelo_items.variacao_modelo_id')
        ->where('variacao_modelo_items.nome', $k)->first();
        // dd($varicaoModeloItem->variacaoModelo);
        $vTemp[] = [
            'id' => $varicaoModeloItem->vendizap_id,
            'preco' => $produto->variacoes[$cont]->valor - $preco
        ];
        $cont++;
    }

    $variacoes[$indice]['id'] = $varicaoModeloItem->variacaoModelo->vendizap_id;
    $variacoes[$indice]['variaveis'] = $vTemp;

    return $variacoes;
}

private function criaProdutoWoocommerce($request, $produto){
    $woocommerceClient = $this->utilWocommerce->getConfig($request->empresa_id);
    $categorias_woocommerce = json_decode($produto->categorias_woocommerce);
    $categorias = [];

    try{
        $type = 'simple';
        foreach($categorias_woocommerce as $id){
            $c = CategoriaWoocommerce::findOrFail($id);
            $categorias[] = ['id'=> $c->_id];
        }

        if(sizeof($produto->variacoes) > 0){
            $type = 'variable';
        }
        $data = [
            'name' => $request->nome,
            'type' => $type,
            'slug' => $request->woocommerce_slug,
            'status' => $request->woocommerce_status,
            'stock_status' => $request->woocommerce_stock_status,
            'price' => __convert_value_bd($request->woocommerce_valor),
            'description' => $request->woocommerce_descricao,
            'categories' => $categorias,
            'weight' => $request->peso
        ];

        if($request->comprimento){
            $data['dimensions']['length'] = $request->comprimento;
        }
        if($request->largura){
            $data['dimensions']['width'] = $request->largura;
        }
        if($request->altura){
            $data['dimensions']['height'] = $request->altura;
        }

        if($produto->imagem){
            $data['images'][] = 
            [   
                'src' => env('APP_URL') . '/uploads/produtos/'.$produto->imagem
            ];
        }

        if(sizeof($produto->variacoes) > 0){
            $data['attributes'] = [
                [
                    'name' => 'Variação',
                    'position' => 0,
                    'visible' => true,
                    'variation' => true,
                ]
            ];
            foreach($produto->variacoes as $v){
                $data['attributes'][0]['options'][] = $v->descricao;
            }
            // dd($data);

            $data['default_attributes'] = [

                [
                    'name' => 'Variação',
                    'option' => $produto->variacoes[0]->descricao
                ]
            ];

        }
        
        // dd($data);

        $product = $woocommerceClient->post("products", $data);
        // dd($product);
        if(sizeof($produto->variacoes) > 0){
            //salva variações
            foreach($produto->variacoes as $v){

                $dataVariacao = [
                    'regular_price' => $v->valor,
                    'attributes' => [[
                        'name' => 'Variação',
                        'option' => $v->descricao
                    ]]
                ];

                $product_id = $product->id;
                $variation = $woocommerceClient->post("products/$product_id/variations", $dataVariacao);
            }
        }
        // dd($variation);

        // $product = $woocommerceClient->post("products", $data);

        if($product){
            return [
                'sucesso' => 1,
                'product_id' => $product->id
            ];
        }
    }catch(\Exception $e){
        echo $e->getMessage();
        die;
        return [
            'erro' => 1,
            'msg' => $e->getMessage()
        ];
    }

}

private function atualizaProdutoWoocommerce($request, $item){
    $woocommerceClient = $this->utilWocommerce->getConfig($request->empresa_id);
    try{
        $data = [
            'name' => $request->nome,
            'slug' => $request->woocommerce_slug,
            'stock_status' => $request->woocommerce_stock_status,
            'status' => $request->woocommerce_status,
            'price' => __convert_value_bd($request->woocommerce_valor),
            'description' => $request->woocommerce_descricao,
            'weight' => $request->peso
        ];
        if($request->comprimento){
            $data['dimensions']['length'] = $request->comprimento;
        }
        if($request->largura){
            $data['dimensions']['width'] = $request->largura;
        }
        if($request->altura){
            $data['dimensions']['height'] = $request->altura;
        }

        $endPoint = 'products';
        $product = $woocommerceClient->put($endPoint."/$item->woocommerce_id", $data);
            // dd($product);
        if($product){
            return [
                'sucesso' => 1,
                'product_id' => $product->id
            ];
        }
    }catch(\Exception $e){
        // echo $e->getMessage();
        // die;
        return [
            'erro' => 1,
            'msg' => $e->getMessage()
        ];
    }
}

private function criaAnuncio($request, $produto){
    $this->__validaToken();

    $dataMercadoLivre = [
        'title' => $produto->nome,
        'category_id' => $request->mercado_livre_categoria,
        'price' => __convert_value_bd($request->mercado_livre_valor),
        'available_quantity' => __convert_value_bd($request->quantidade_mercado_livre),
        'currency_id' => 'BRL',
        'condition' => $request->condicao_mercado_livre,
        'buying_mode' => 'buy_it_now',
        'listing_type_id' => $request->mercado_livre_tipo_publicacao,
        'video_id' => $request->mercado_livre_youtube,
    ];

    if($request->quantidade_mercado_livre){
        $qtd = __convert_value_bd($request->quantidade_mercado_livre);
        $this->utilEstoque->incrementaEstoque($produto->id, $qtd, null);

        $transacao = Estoque::where('produto_id', $produto->id)->orderBy('id', 'desc')->first();
        $tipo = 'incremento';
        $codigo_transacao = $transacao->id;
        $tipo_transacao = 'alteracao_estoque';

        $this->utilEstoque->movimentacaoProduto($produto->id, $qtd, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id);
    }

    if($request->marca_id){
        $marca = Marca::findOrFail($request->marca_id);
        $dataMercadoLivre['attributes'][] = [
            'id' => 'BRAND',
            'value_name' => $marca->nome
        ];
    }

    if($request->mercado_livre_modelo){
        $dataMercadoLivre['attributes'][] = [
            'id' => 'MODEL',
            'value_name' => $request->mercado_livre_modelo
        ];
    }
        // dd($dataMercadoLivre);

    $configMercadoLivre = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
    ->first();

    if($produto->img){
        $dataMercadoLivre['pictures'][0]['source'] = $configMercadoLivre->url . $produto->img;
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataMercadoLivre));

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $configMercadoLivre->access_token,
        'Content-Type: application/json'
    ]);

    $res = curl_exec($curl);
    $retorno = json_decode($res);
    if($retorno->status == 400){
        $msg = $this->trataErros($retorno);
        return [
            'erro' => 1,
            'msg' => $msg
        ];
    }
        // incluir descricao

    if($request->mercado_livre_descricao){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$retorno->id/description");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(
            ['plain_text' => $request->mercado_livre_descricao]
        ));

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $configMercadoLivre->access_token,
            'Content-Type: application/json'
        ]);

        $res = curl_exec($curl);
    }

    return [
        'sucesso' => 1,
        'retorno' => $retorno
    ];

}

private function trataErros($retorno){
    $msg = "";
    foreach($retorno->cause as $c){
        $msg .= $c->message;
    }
    return $msg;
}

private function atualizaAnuncio($request, $produto){

    $configMercadoLivre = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
    ->first();

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$produto->mercado_livre_id");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $configMercadoLivre->access_token,
        'Content-Type: application/json'
    ]);

    $res = curl_exec($curl);
    $prod = json_decode($res);
        // dd($prod);

    $dataMercadoLivre = [
        'title' => $produto->nome,
            // 'category_id' => $request->mercado_livre_categoria,
            // 'price' => __convert_value_bd($request->mercado_livre_valor),
            // 'available_quantity' => __convert_value_bd($request->quantidade_mercado_livre),
        'currency_id' => 'BRL',
            // 'condition' => $request->condicao_mercado_livre,
            // 'buying_mode' => 'buy_it_now',
        'video_id' => $request->mercado_livre_youtube,
    ];

    if(sizeof($prod->variations) > 0){
        $dataMercadoLivre['variations'][0]['price'] = __convert_value_bd($request->mercado_livre_valor);
    }else{
        $dataMercadoLivre['price'] = __convert_value_bd($request->mercado_livre_valor);
    }

        // dd($dataMercadoLivre);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$produto->mercado_livre_id");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataMercadoLivre));

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $configMercadoLivre->access_token,
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    $res = curl_exec($curl);
    $retorno = json_decode($res);

    if($retorno->status == 400){
        $msg = $this->trataErros($retorno);
        return [
            'erro' => 1,
            'msg' => $msg
        ];
    }

    if($request->mercado_livre_descricao){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$produto->mercado_livre_id/description");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(
            ['plain_text' => $request->mercado_livre_descricao]
        ));

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $configMercadoLivre->access_token,
            'Content-Type: application/json'
        ]);

        $res = curl_exec($curl);
    }

    return [
        'sucesso' => 1,
        'retorno' => $retorno
    ];
}

public function etiqueta(Request $request, $id)
{
    $item = Produto::findOrFail($id);
    $modelos = ModeloEtiqueta::where('empresa_id', $item->empresa_id)->get();
    return view('produtos.etiqueta', compact('item', 'modelos'));
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

    $item = Produto::findOrFail($id);

    $nome = $item->nome;
    $codigo = $item->codigo_barras;
    $valor = $item->valor_unitario;
    $unidade = $item->unidade;

    if($codigo == "" || $codigo == "SEM GTIN" || $codigo == "sem gtin"){
        session()->flash('flash_error', 'Produto sem código de barras definido');
        return redirect()->back();
    }

    $data = [
        'nome_empresa' => $request->nome_empresa ? true : false,
        'nome_produto' => $request->nome_produto ? true : false,
        'valor_produto' => $request->valor_produto ? true : false,
        'cod_produto' => $request->codigo_produto ? true : false,
        'tipo' => $request->tipo,
        'codigo_barras_numerico' => $request->codigo_barras_numerico ? true : false,
        'nome' => $nome,
        'codigo' => $item->numero_sequencial,
        'valor' => $valor,
        'unidade' => $unidade,
        'valor_atacado' => $item->valor_atacado,
        'referencia' => $item->referencia,
        'empresa' => $item->empresa->nome
    ];
    $generatorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();

    $bar_code = $generatorPNG->getBarcode($codigo, $generatorPNG::TYPE_EAN_13);

    $rand = rand(1000, 9999);
    file_put_contents(public_path("barcode")."/$rand.png", $bar_code);
    $quantidade_por_linhas = $request->etiquestas_por_linha;
    $quantidade = $request->quantidade_etiquetas;
    $altura = $request->altura;
    $largura = $request->largura;
    $distancia_topo = $request->distancia_etiquetas_topo;
    $distancia_lateral = $request->distancia_etiquetas_lateral;
    $tamanho_fonte = $request->tamanho_fonte;
    $distancia_entre_linhas = $request->distancia_entre_linhas;
    $referencia = $request->referencia;
    $valor_atacado = $request->valor_atacado;

    $tamanho_codigo = $request->tamanho_codigo_barras;

    return view('produtos.etiqueta_print', compact('altura', 'largura', 'rand', 'codigo', 'quantidade', 'distancia_topo',
        'distancia_lateral', 'quantidade_por_linhas', 'tamanho_fonte', 'tamanho_codigo', 'data', 'distancia_entre_linhas', 'referencia', 
        'valor_atacado'));

}

public function reajuste(Request $request){
    $nome = $request->nome;
    $categoria_id = $request->categoria_id;
    $marca_id = $request->marca_id;
    $cst_csosn = $request->cst_csosn;
    $local_id = $request->local_id;
    $pendentes = $request->pendentes;

    $data = [];
    $locais = __getLocaisAtivoUsuario();
    $locais = $locais->pluck(['id']);

    if($nome || $categoria_id || $cst_csosn || $marca_id || $local_id || $pendentes){
        $data = Produto::where('empresa_id', $request->empresa_id)
        ->select('produtos.*')
        ->when($nome, function ($query) use ($nome) {
            $query->where('nome', 'like', "%$nome%");
        })
        ->when($cst_csosn, function ($query) use ($cst_csosn) {
            $query->where('cst_csosn', $cst_csosn);
        })
        ->when($marca_id, function ($query) use ($marca_id) {
            $query->where('marca_id', $marca_id);
        })
        ->when($categoria_id, function ($query) use ($categoria_id) {
            return $query->where(function($q) use ($categoria_id)
            {
                $q->where('categoria_id', $categoria_id)
                ->orWhere('sub_categoria_id', $categoria_id);
            });
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })
        ->when($pendentes, function ($query) {
            return $query->where(function($q)
            {
                $q->where('cst_csosn', null)
                ->orWhere('cst_csosn', '')
                ->orWhere('cst_pis', null)
                ->orWhere('cst_pis', '')
                ->orWhere('cst_cofins', null)
                ->orWhere('cst_cofins', '')
                ->orWhere('cst_ipi', null)
                ->orWhere('cst_ipi', '')
                ->orWhere('cfop_estadual', null)
                ->orWhere('cfop_estadual', '')
                ->orWhere('cfop_outro_estado', null)
                ->orWhere('cfop_outro_estado', '')
                ->orWhere('cfop_entrada_estadual', null)
                ->orWhere('cfop_entrada_estadual', '')
                ->orWhere('cfop_entrada_outro_estado', null)
                ->orWhere('cfop_entrada_outro_estado', '');
            });
        })
        ->get();
    }

    $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
    ->where('status', 1)->get();
    $marcas = Marca::where('empresa_id', request()->empresa_id)->get();
    $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();

    return view('produtos.reajuste', compact('data', 'categorias', 'marcas', 'padroes'));
}

public function reajusteUpdate(Request $request){
    try{
        for($i=0; $i<sizeof($request->produto_id); $i++){
            $item = Produto::findOrFail($request->produto_id[$i]);

            if(isset($request->locais)){
                $item->locais()->delete();
                for($j=0; $j<sizeof($request->locais); $j++){
                    ProdutoLocalizacao::updateOrCreate([
                        'produto_id' => $item->id, 
                        'localizacao_id' => $request->locais[$j]
                    ]);
                }
            }

            $item->padrao_id = $request->padrao_id;

            $item->valor_unitario = __convert_value_bd($request->valor_unitario[$i]);
            $item->valor_compra = __convert_value_bd($request->valor_compra[$i]);
            $item->cst_csosn = $request->cst_csosn[$i];
            $item->cst_pis = $request->cst_pis[$i];
            $item->cst_cofins = $request->cst_cofins[$i];
            $item->cst_ipi = $request->cst_ipi[$i];

            $item->perc_icms = $request->perc_icms[$i];
            $item->perc_pis = $request->perc_pis[$i];
            $item->perc_cofins = $request->perc_cofins[$i];
            $item->perc_ipi = $request->perc_ipi[$i];
            $item->perc_red_bc = $request->perc_red_bc[$i] ?? '';
            $item->cfop_estadual = $request->cfop_estadual[$i];
            $item->cfop_outro_estado = $request->cfop_outro_estado[$i];

            $item->cfop_entrada_estadual = $request->cfop_entrada_estadual[$i];
            $item->cfop_entrada_outro_estado = $request->cfop_entrada_outro_estado[$i];
            $item->status = $request->status[$i];

            $item->cst_ibscbs = $request->cst_ibscbs[$i];
            $item->cclass_trib = $request->cclass_trib[$i];
            $item->perc_ibs_uf = $request->perc_ibs_uf[$i];
            $item->perc_ibs_mun = $request->perc_ibs_mun[$i];
            $item->perc_cbs = $request->perc_cbs[$i];
            $item->perc_dif = $request->perc_dif[$i];

            $item->codigo_beneficio_fiscal = $request->codigo_beneficio_fiscal[$i];
            $item->modBCST = $request->modBCST[$i];
            $item->pICMSST = $request->pICMSST[$i];
            $item->pMVAST = $request->pMVAST[$i];
            $item->redBCST = $request->redBCST[$i];
            $item->pICMSEfet = $request->pICMSEfet[$i];
            $item->pRedBCEfet = $request->pRedBCEfet[$i];
            
            $item->save();
        }

        session()->flash("flash_success", "Produtos alterados!");
        return redirect()->route('produtos.index');

    } catch (\Exception $e) {
        session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        return redirect()->back();
    }
}

public function uploadImagens(){
    return view('produtos.seleciona_imagens');
}

public function uploadMultiple(Request $request){
    if (!is_dir(public_path('upload_temp'))) {
        mkdir(public_path('upload_temp'), 0777, true);
    }
    $this->clearFolder(public_path('upload_temp'));

    if ($request->hasfile('imagens')) {
        foreach ($request->file('imagens') as $file) {
            $ext = $file->getClientOriginalExtension();
            $filename = Str::random(20) .'.'. $ext;
            $file->move(public_path('upload_temp/'), $filename);
        }
    }

    return redirect()->route('produtos.vincula-imagens');
    
}

public function vinculaImagens(){
    $files = glob(public_path('upload_temp/*'));
    $data = [];

    foreach($files as $file){
        $img = explode("upload_temp", $file);
        $item = [
            'diretorio' => $file,
            'img' => "/upload_temp".$img[1]
        ];
        $data[] = $item;
    }
    return view('produtos.vincula_imagens', compact('data'));

}

public function vincularImagens(Request $request){
    try{
        for($i=0; $i<sizeof($request->produto_id); $i++){
            $produto = Produto::findOrFail($request->produto_id[$i]);
            $img = explode("upload_temp", $request->diretorio[$i]);
            $filename = str_replace("/", "", $img[1]);
            $produto->imagem = $filename;
            $produto->save();
            copy($request->diretorio[$i], public_path("/uploads/produtos/").$filename);
        }

        $this->clearFolder(public_path('upload_temp'));
        session()->flash("flash_success", "Imagens cadastradas!");
        return redirect()->route('produtos.index');
    }catch(\Exception $e){
        session()->flash("flash_error", $e->getMessage());
        return redirect()->back();
    }

}

private function clearFolder($destino){
    $files = glob($destino."/*");
    foreach($files as $file){ 
        if(is_file($file)) unlink($file); 
    }
}

public function ibpt(Request $request){
    $empresa = Empresa::findOrFail($request->empresa_id);
    $ibptService = new IbptService($empresa->token_ibpt, preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj));
    $produtos = Produto::where('empresa_id', $request->empresa_id)
    ->limit(10)
    ->get();

    $contIbpt = 0;
    $totalDeProdutos = sizeof($produtos);
    foreach($produtos as $p){
        if($p->ncm){
            $data = [
                'ncm' => preg_replace('/[^0-9]/', '', $p->ncm),
                'uf' => $empresa->cidade->uf,
                'extarif' => 0,
                'descricao' => $p->nome,
                'unidadeMedida' => $p->unidade,
                'valor' => number_format(0, 2),
                'gtin' => $p->codBarras,
                'codigoInterno' => 0
            ];
            $resp = $ibptService->consulta($data);

            try{
                if($p->ibpt){
                    $ibpt = $p->ibpt;
                    $ibpt->codigo = $resp->Codigo;
                    $ibpt->uf = $resp->UF;
                    $ibpt->descricao = $resp->Descricao;
                    $ibpt->nacional = $resp->Nacional;
                    $ibpt->estadual = $resp->Estadual;
                    $ibpt->importado = $resp->Importado;
                    $ibpt->municipal = $resp->Municipal;
                    $ibpt->vigencia_inicio = $resp->VigenciaInicio;
                    $ibpt->vigencia_fim = $resp->VigenciaFim;
                    $ibpt->chave = $resp->Chave;
                    $ibpt->versao = $resp->Versao;
                    $ibpt->fonte = $resp->Fonte;
                    $ibpt->save();
                }else{
                    $dataIbpt = [
                        'produto_id' => $p->id,
                        'codigo' => $resp->Codigo,
                        'uf' => $resp->UF, 
                        'descricao' => $resp->Descricao,
                        'nacional' => $resp->Nacional,
                        'estadual' => $resp->Estadual,
                        'importado' => $resp->Importado,
                        'municipal' => $resp->Municipal,
                        'vigencia_inicio' => $resp->VigenciaInicio,
                        'vigencia_fim' => $resp->VigenciaFim,
                        'chave' => $resp->Chave,
                        'versao' => $resp->Versao,
                        'fonte' => $resp->Fonte
                    ];

                    ProdutoIbpt::create($dataIbpt);
                }
                $contIbpt++;
            }catch(\Exception $e){


            }
        }
    }

    $msg = "Total de produtos cadastrados: $totalDeProdutos, total de produtos com IBPT: $contIbpt";
    session()->flash("flash_success", $msg);
    return redirect()->back();
}

public function alterarValorEstoque(){
    $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
    ->orderBy('nome')
    ->where('status', 1)->get();

    $marcas = Marca::where('empresa_id', request()->empresa_id)
    ->orderBy('nome')->get();

    return view('produtos.alterar_valor_estoque', compact('categorias', 'marcas'));
}

public function buscarAjuste(Request $request){

    $local_id = $request->local_id;
    $query = Produto::query();

    if ($request->nome) {
        $query->where('nome', 'like', "%{$request->nome}%");
    }

    if ($request->codigo_barras) {
        $query->where('codigo_barras', $request->codigo_barras);
    }

    if ($request->categoria_id) {
        $query->where('categoria_id', $request->categoria_id);
    }

    if ($request->marca_id) {
        $query->where('marca_id', $request->marca_id);
    }

    if ($request->local_id) {
        $query->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
        ->where('local_id', $request->local_id);
    }

    $produtos = $query->select('produtos.*')->where('empresa_id', $request->empresa_id)->take(50)->get();

    return view('produtos.partials.tabela_ajuste', compact('produtos', 'local_id'));
}

public function alterarCampo(Request $request)
{
    $produto = Produto::find($request->id);

    if (!$produto) {
        return response()->json(['erro' => 'Produto não encontrado'], 404);
    }

    $campo = $request->campo;
    $valor = $request->valor;
    $local_id = $request->local_id;

    // atualizar
    if($campo == 'valor_venda'){
        $produto->valor_unitario = __convert_value_bd($valor);
    }

    if($campo == 'valor_compra'){
        $produto->valor_compra = __convert_value_bd($valor);
    }

    if($produto->valor_compra > 0 && $produto->valor_unitario > 0){
        $produto->percentual_lucro = ($produto->valor_unitario / $produto->valor_compra)*100;
    }else{
        $produto->percentual_lucro = 100;
    }
    $produto->save();

    if($campo == 'quantidade_estoque'){
        $estoque = $produto->estoque;
        if($estoque == null){

            $this->utilEstoque->incrementaEstoque($produto->id, $valor, null, $local_id);
            $transacao = Estoque::where('produto_id', $produto->id)->orderBy('id', 'desc')->first();
            $tipo = 'incremento';
            $codigo_transacao = $transacao->id;
            $tipo_transacao = 'alteracao_estoque';
            $this->utilEstoque->movimentacaoProduto($produto->id, $valor, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $local_id);  
        }else{
            if($local_id){
                $estoque = Estoque::where('produto_id', $produto->id)->where('local_id', $local_id)->first();
            }

            if($estoque){
                // return response()->json($estoque, 401);

                $estoque->quantidade = $valor;
                $estoque->save();
            }
        }
    }


    return response()->json(['sucesso' => true]);
}

}
