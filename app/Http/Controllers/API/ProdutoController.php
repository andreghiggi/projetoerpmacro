<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use App\Models\ProdutoUnico;
use App\Models\Estoque;
use App\Models\Fornecedor;
use App\Models\Cliente;
use App\Models\ConfiguracaoCardapio;
use App\Models\ProdutoPizzaValor;
use App\Models\Empresa;
use App\Models\Marca;
use App\Models\Caixa;
use App\Models\ConfigGeral;
use App\Models\ItemNfe;
use App\Models\ItemListaPreco;
use App\Models\PadraoTributacaoProduto;
use App\Models\ProdutoVariacao;
use App\Models\Localizacao;
use App\Models\CategoriaAdicional;
use Illuminate\Http\Request;
use App\Utils\EstoqueUtil;

class ProdutoController extends Controller
{

    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;
    }

    public function getAdicionaisModal(Request $request){
        $item = Produto::findOrFail($request->produto_id);

        if(sizeof($item->adicionais) == 0){
            return response()->json("", 200);
        }

        $categoriasAdicional = CategoriaAdicional::where('categoria_adicionals.empresa_id', $item->empresa_id)
        ->where('categoria_adicionals.status', 1)
        ->select('categoria_adicionals.*')
        ->join('adicionals', 'adicionals.categoria_id', '=', 'categoria_adicionals.id')
        ->join('produto_adicionals', 'produto_adicionals.adicional_id', '=', 'adicionals.id')
        ->where('produto_adicionals.produto_id', $item->id)
        ->groupBy('categoria_adicionals.id')
        ->get();

        return view('produtos.partials.adicional_pdv', compact('item', 'categoriasAdicional'));
    }

    public function pesquisa(Request $request)
    {
        $lista_id = $request->lista_id;

        $local_id = null;
        if(isset($request->local_id) && $request->local_id != null){
            $local_id = $request->local_id;
        }else if(isset($request->usuario_id)){
            $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
            if($caixa != null){

                $locais = Localizacao::where('usuario_localizacaos.usuario_id', $request->usuario_id)
                ->select('localizacaos.*')
                ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
                ->where('localizacaos.status', 1)->get();
                $local_id = $caixa->local_id;
            }
        }

        $refDigito = substr($request->pesquisa, 0, 1);
        $data = Produto::orderBy('nome', 'desc')
        ->select('produtos.*')
        ->where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->when(!is_numeric($request->pesquisa) && $refDigito != '#', function ($q) use ($request) {
            // return $q->where('nome', 'LIKE', "%$request->pesquisa%");
            return $q->where(function($query) use ($request)
            {
                return $query->where('nome', 'LIKE', "%$request->pesquisa%")
                ->orWhere('referencia', 'LIKE', "%$request->pesquisa%");
            });
        })
        ->when(is_numeric($request->pesquisa) && $refDigito != '#', function ($q) use ($request) {
            return $q->where(function($query) use ($request)
            {
                return $query->where('codigo_barras', 'LIKE', "%$request->pesquisa%")
                ->orWhere('codigo_barras2', 'LIKE', "%$request->pesquisa%")
                ->orWhere('codigo_barras3', 'LIKE', "%$request->pesquisa%")
                ->orWhere('numero_sequencial', 'LIKE', "%$request->pesquisa%");
            });
        })
        ->when($refDigito == '#', function ($q) use ($request) {
            $pesquisa = substr($request->pesquisa, 1, strlen($request->pesquisa));
            return $q->where('referencia', 'LIKE', "%$pesquisa%");
        })
        ->when($local_id != null, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->distinct('produtos.id')
        ->get();

        if(is_numeric($request->pesquisa)){
            $dataAppend = ProdutoVariacao::where('produtos.empresa_id', $request->empresa_id)
            ->where('produto_variacaos.codigo_barras', $request->pesquisa)
            // ->where('produto_variacaos.codigo_barras', 'LIKE', "%$request->pesquisa%")
            ->join('produtos', 'produtos.id', '=', 'produto_variacaos.produto_id')
            ->select('produto_variacaos.*')
            ->get();

            foreach($dataAppend as $v){
                $v->valor_unitario = $v->valor;
                $v->valor_compra = $v->produto->valor_compra;
                $v->nome = $v->produto->nome . " - " . $v->descricao;
                $v->codigo_variacao = $v->id;
                $v->id = $v->produto_id;
                $data->push($v);
            }

            // $data->push($dataAppend);
        }

        if($lista_id){

            foreach($data as $i){
                $itemLista = ItemListaPreco::where('lista_id', $lista_id)
                ->where('produto_id', $i->id)
                ->first();
                if($itemLista != null){
                    $i->valor_unitario = $itemLista->valor;
                }
            }
        }
        $countLocais = Localizacao::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->count();
        foreach($data as $p){
            if($p->gerenciar_estoque){

                $estoque = Estoque::where('produto_id', $p->id)
                ->when($local_id != null, function ($query) use ($local_id) {
                    return $query->where('local_id', $local_id);
                })
                ->first();
                if($estoque){
                    $p->estoque_atual = number_format($estoque->quantidade, 3);
                    if($p->unidade == 'UN' || $p->unidade == 'UNID'){
                        $p->estoque_atual = number_format($estoque->quantidade, 0);
                    }
                }else{
                    $p->estoque_atual = 0;
                }
                
            }else{
                $p->estoque_atual = 0;
            }


            if($countLocais > 1){
                $p = __tributacaoProdutoLocalVenda($p, $local_id);
            }

            if($p instanceof Produto &&$p->precoComPromocao()){
                $p->valor_unitario = $p->precoComPromocao()->valor;
            }
        }

        return response()->json($data, 200);
    }

    public function modal($id){
        $item = Produto::findOrFail($id);
        return view('produtos.partials.modal_body', compact('item'));
    }

    public function codigoUnico(Request $request){

        $data = ProdutoUnico::
        where('produtos.empresa_id', $request->empresa_id)
        ->where('produto_unicos.em_estoque', 1)
        ->where('produtos.status', 1)
        ->select('produto_unicos.*')
        ->join('produtos', 'produtos.id', '=', 'produto_unicos.produto_id')
        ->when($request->pesquisa, function ($q) use ($request) {
            return $q->where('produto_unicos.codigo', 'LIKE', "%$request->pesquisa%");
        })
        ->get();
        return response()->json($data, 200);
    }

    public function descricao(Request $request){
        $item = Produto::findOrFail($request->produto_id);
        return response()->json($item->nome, 200);
    }

    public function tipoProducao(Request $request){

        $data = Produto::
        where('produtos.empresa_id', $request->empresa_id)
        ->where('tipo_producao', 1)
        ->where('status', 1)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->pesquisa%");
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('codigo_barras', 'LIKE', "%$request->pesquisa%");
        })
        ->get();
        return response()->json($data, 200);
    }

    public function pesquisaComEstoque(Request $request){
        $data = Produto::orderBy('nome', 'desc')
        ->select('produtos.*')
        ->with('estoque')
        ->where('empresa_id', $request->empresa_id)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->pesquisa%");
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('codigo_barras', 'LIKE', "%$request->pesquisa%");
        })
        ->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
        ->where('estoques.local_id', $request->local_saida_id)
        ->distinct('produtos.id')
        ->get();

        return response()->json($data, 200);

    }

    public function pesquisaFiltro(Request $request)
    {
        $data = Produto::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('nome', 'like', "%$request->pesquisa%")
        ->get();
        return response()->json($data, 200);
    }

    public function pesquisaCardapio(Request $request)
    {
        $data = Produto::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('cardapio', 1)
        ->where('nome', 'like', "%$request->pesquisa%")
        ->get();
        return response()->json($data, 200);
    }

    public function pesquisaDelivery(Request $request)
    {
        $data = Produto::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('delivery', 1)
        ->where('nome', 'like', "%$request->pesquisa%")
        ->get();
        return response()->json($data, 200);
    }

    public function pesquisaReserva(Request $request)
    {
        $data = Produto::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('reserva', 1)
        ->where('nome', 'like', "%$request->pesquisa%")
        ->get();
        return response()->json($data, 200);
    }

    public function find(Request $request)
    {
        $cliente = null;
        $fornecedor = null;
        $entrada = $request->entrada;
        $tributacao_cliente = $request->tributacao_cliente;
        $lista_id = $request->lista_id;
        $tributacao = null;
        $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();

        if (isset($request->cliente_id)) {
            $cliente = Cliente::find($request->cliente_id);
            $tributacao = $cliente->tributacao;
        }
        if (isset($request->fornecedor_id)) {
            $fornecedor = Fornecedor::find($request->fornecedor_id);
        }
        $item = Produto::where('id', $request->produto_id)
        ->first();

        if($lista_id){
            $itemLista = ItemListaPreco::where('lista_id', $lista_id)
            ->where('produto_id', $item->id)
            ->first();
            if($itemLista){
                $item->valor_unitario = $itemLista->valor;
            }
        }

        $item = __tributacaoProdutoLocalVenda($item, $caixa->local_id);

        if($entrada == 1){
            $item->cfop_atual = $item->cfop_entrada_estadual;
        }

        $empresa = Empresa::find($item->empresa_id);
        if($caixa){
            $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
        }

        if($tributacao != null){
            if($tributacao->perc_icms){
                $item->perc_icms = $tributacao->perc_icms;
            }
            if($tributacao->perc_pis){
                $item->perc_pis = $tributacao->perc_pis;
            }
            if($tributacao->perc_cofins){
                $item->perc_cofins = $tributacao->perc_cofins;
            }
            if($tributacao->perc_ipi){
                $item->perc_ipi = $tributacao->perc_ipi;
            }
            if($tributacao->perc_red_bc){
                $item->perc_red_bc = $tributacao->perc_red_bc;
            }
            if($tributacao->cst_csosn){
                $item->cst_csosn = $tributacao->cst_csosn;
            }
            if($tributacao->cst_pis){
                $item->cst_pis = $tributacao->cst_pis;
            }
            if($tributacao->cst_cofins){
                $item->cst_cofins = $tributacao->cst_cofins;
            }
            if($tributacao->cst_ipi){
                $item->cst_ipi = $tributacao->cst_ipi;
            }

            if($tributacao->cfop_estadual){
                $item->cfop_estadual = $tributacao->cfop_estadual;
            }
            if($tributacao->cfop_outro_estado){
                $item->cfop_outro_estado = $tributacao->cfop_outro_estado;
            }
            if($tributacao->cest){
                $item->cest = $tributacao->cest;
            }
            if($tributacao->ncm){
                $item->ncm = $tributacao->ncm;
            }
            if($tributacao->codigo_beneficio_fiscal){
                $item->codigo_beneficio_fiscal = $tributacaocodigo_beneficio_fiscalncm;
            }
        }

        if($item->precoComPromocao()){
            $item->valor_unitario = $item->precoComPromocao()->valor;
        }

        $item->cfop_atual = $item->cfop_estadual;

        if ($empresa != null) {
            if ($cliente != null) {
                if ($cliente->cidade && $empresa->cidade->uf != $cliente->cidade->uf) {
                    $item->cfop_atual = $item->cfop_outro_estado;
                }
            }

            if ($fornecedor != null) {
                if ($fornecedor->cidade && $empresa->cidade->uf != $fornecedor->cidade->uf) {
                    $item->cfop_atual = $item->cfop_entrada_outro_estado;
                }
            }
        }

        return response()->json($item, 200);
    }

    public function findId($id)
    {
        $item = Produto::where('id', $id)
        ->with(['categoria', 'adicionais', 'subcategoria', 'locais'])
        ->first();

        $item->disponibilidade = $item->locais->pluck('localizacao_id');
        return response()->json($item, 200);
    }

    public function findWithLista(Request $request)
    {
        $lista_id = $request->lista_id;
        $item = Produto::where('id', $request->produto_id)
        ->with(['categoria', 'adicionais'])
        ->first();

        $item = __tributacaoProdutoLocalVenda($item, $request->local_id);

        $itemLista = ItemListaPreco::where('lista_id', $lista_id)
        ->where('produto_id', $item->id)
        ->first();
        if($itemLista){
            $item->valor_unitario = $itemLista->valor;
        }

        if($item->precoComPromocao()){
            $item->valor_unitario = $item->precoComPromocao()->valor;
        }

        return response()->json($item, 200);
    }

    public function padrao(Request $request)
    {
        $item = PadraoTributacaoProduto::
        with('_ncm')
        ->findOrFail($request->padrao);
        return response()->json($item, 200);
    }

    public function findByCategory(Request $request)
    {
        $id = $request->id;
        $lista_id = $request->lista_id;

        $local_id = null;
        if(isset($request->usuario_id)){
            $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
            if($caixa != null){

                $locais = Localizacao::where('usuario_localizacaos.usuario_id', $request->usuario_id)
                ->select('localizacaos.*')
                ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
                ->where('localizacaos.status', 1)->get();
                $local_id = $caixa->local_id;
            }
        }

        $item = CategoriaProduto::findOrFail($id);
        // $produtos = $item->produtos;
        $produtos = Produto::where('empresa_id', $item->empresa_id)
        ->select('produtos.*')
        ->when($local_id != null, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        // ->where('categoria_id', $id)
        ->where(function($query) use ($id)
        {
            return $query->where('categoria_id', $id)
            ->orWhere('sub_categoria_id', $id);
        })
        ->where('status', 1)
        ->get();

        $countLocais = Localizacao::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->count();

        if($countLocais > 1){
            foreach($produtos as $p){
                $p = __tributacaoProdutoLocalVenda($p, $local_id);
            }
        }

        if($lista_id){
            foreach($produtos as $p){
                $itemLista = ItemListaPreco::where('lista_id', $lista_id)
                ->where('produto_id', $p->id)
                ->first();
                if($itemLista != null){
                    $p->valor_unitario = $itemLista->valor;
                }
            }
        }
        return view('produtos.cards', compact('produtos'));
    }

    public function all(Request $request)
    {
        $lista_id = $request->lista_id;

        $local_id = null;
        if(isset($request->usuario_id)){
            $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
            if($caixa != null){

                $locais = Localizacao::where('usuario_localizacaos.usuario_id', $request->usuario_id)
                ->select('localizacaos.*')
                ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
                ->where('localizacaos.status', 1)->get();
                $local_id = $caixa->local_id;
            }
        }

        $produtos = Produto::where('empresa_id', $request->empresa_id)
        ->select('produtos.*')
        ->when($local_id != null, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->where('status', 1)
        ->limit(50)->get();

        $countLocais = Localizacao::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->count();
        if($countLocais > 1){
            foreach($produtos as $p){
                $p = __tributacaoProdutoLocalVenda($p, $local_id);
            }
        }

        if($lista_id){
            foreach($produtos as $p){
                $itemLista = ItemListaPreco::where('lista_id', $lista_id)
                ->where('produto_id', $p->id)
                ->first();
                if($itemLista != null){
                    $p->valor_unitario = $itemLista->valor;
                }
            }
        }
        
        return view('produtos.cards', compact('produtos'));
    }


    public function getPizzas(Request $request){
        $produto_id = $request->produto_id;
        $tamanho_id = $request->tamanho_id;

        $data = Produto::where('produtos.empresa_id', $request->empresa_id)
        ->select('produtos.*')
        ->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
        ->where('categoria_produtos.tipo_pizza', 1)
        ->get();

        return view('produtos.pizzas', compact('data', 'produto_id', 'tamanho_id'));
    }

    public function calculoPizza(Request $request){
        $sabores = $request->sabores;
        $tamanho_id = $request->tamanho_id;

        $somaValor = 0;
        $maiorValor = 0;
        if($sabores == null){
            return response()->json(0, 200);
        }
        $qtdSabores = sizeof($sabores);
        
        foreach($sabores as $s){
            $item = ProdutoPizzaValor::where('produto_id', (int)$s)
            ->where('tamanho_id', $tamanho_id)
            ->first();

            if($item->valor > $maiorValor){
                $maiorValor = $item->valor;
            }

            $somaValor += $item->valor;

        }
        $config = ConfiguracaoCardapio::where('empresa_id', $request->empresa_id)
        ->first();

        if($config->valor_pizza == 'divide'){
            return response()->json((float)number_format($somaValor/$qtdSabores, 2), 200);
        }else{
            return response()->json((float)number_format($maiorValor, 2), 200);
        }
    }

    public function findByBarcode(Request $request)
    {
        $lista_id = $request->lista_id;

        $local_id = null;
        if(isset($request->usuario_id)){
            $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
            if($caixa != null){

                $locais = Localizacao::where('usuario_localizacaos.usuario_id', $request->usuario_id)
                ->select('localizacaos.*')
                ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
                ->where('localizacaos.status', 1)->get();
                $local_id = $caixa->local_id;
            }
        }

        $item = Produto::with('estoque')
        ->select('produtos.*')
        ->where(function($query) use ($request)
        {
            return $query->where('codigo_barras', $request->barcode)
            ->orWhere('codigo_barras2', $request->barcode)
            ->orWhere('codigo_barras3', $request->barcode);
        })
        ->where('empresa_id', $request->empresa_id)
        ->when($local_id != null, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->first();

        if($lista_id){
            $itemLista = ItemListaPreco::where('lista_id', $lista_id)
            ->where('produto_id', $item->id)
            ->first();

            if($itemLista != null){
                $item->valor_unitario = $itemLista->valor;
            }
        }


        if($item == null){
            $variacao = ProdutoVariacao::where('produto_variacaos.codigo_barras', $request->barcode)
            ->where('produtos.empresa_id', $request->empresa_id)
            ->join('produtos', 'produtos.id', '=', 'produto_variacaos.produto_id')
            ->select('produto_variacaos.*')
            ->first();

            $item = $variacao->produto;

            if($variacao){
                $item->codigo_variacao = $variacao->id;
                $item->valor_unitario = $variacao->valor;
                $item->nome = $variacao->produto->nome . " - " . $variacao->descricao;
                $item->id = $variacao->produto_id;
            }
        }

        if($item->precoComPromocao()){
            $item->valor_unitario = $item->precoComPromocao()->valor;
        }
        
        return response()->json($item, 200);
    }

    public function findByBarcodeReference(Request $request)
    {
        $config = ConfigGeral::where('empresa_id', request()->empresa_id)
        ->first();
        $balanca_valor_peso = $config != null ? $config->balanca_valor_peso : 'valor';
        $balanca_digito_verificador = $config != null ? $config->balanca_digito_verificador : 6;
        $barcode = $request->barcode;
        $ref = (int)substr($barcode, 1, $balanca_digito_verificador-1);

        // return response()->json($ref, 401);
        $valor = substr($barcode, 7, 7);

        $valor = (float)substr($valor, 0, strlen($valor)-1);
        $valor = $valor/100;

        $quantidade = 1;

        $local_id = null;
        if(isset($request->usuario_id)){
            $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
            if($caixa != null){

                $locais = Localizacao::where('usuario_localizacaos.usuario_id', $request->usuario_id)
                ->select('localizacaos.*')
                ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
                ->where('localizacaos.status', 1)->get();
                $local_id = $caixa->local_id;
            }
        }

        $item = Produto::with('estoque')
        ->select('produtos.*')
        ->where('referencia_balanca', $ref)
        ->where('empresa_id', $request->empresa_id)
        ->when($local_id != null, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->first();

        if($item == null){
            return response()->json("erro", 404);
        }

        if ($item->unidade == 'KG') {

            if ($balanca_valor_peso == 'valor') {
                $quantidade = $valor / $item->valor_unitario;
                $subtotal = $valor;
            } else {

                // $quantidade = $valor / $item->valor_unitario;
                $quantidade = $valor/10;
                $valor = $item->valor_unitario * number_format($quantidade, 3);
                $subtotal = $item->valor_unitario * number_format($quantidade, 3);
            }
        }else{
            $subtotal = $valor;
        }
        if ($item) {
            $item->valor = $valor;
            $item->quantidade = $quantidade;
        }

        return view('front_box.partials.row_produtos_referencia', compact('item', 'quantidade', 'valor', 'subtotal'));
    }

    public function findByBarcodeReference2(Request $request)
    {
        $config = ConfigGeral::where('empresa_id', request()->empresa_id)
        ->first();
        $balanca_valor_peso = $config != null ? $config->balanca_valor_peso : 'valor';
        $balanca_digito_verificador = $config != null ? $config->balanca_digito_verificador : 6;
        $barcode = $request->barcode;
        $ref = (int)substr($barcode, 1, $balanca_digito_verificador-1);

        // return response()->json($ref, 401);
        $valor = substr($barcode, 7, 7);

        $valor = (float)substr($valor, 0, strlen($valor)-1);
        $valor = $valor/100;

        $quantidade = 1;

        $local_id = null;
        if(isset($request->usuario_id)){
            $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
            if($caixa != null){

                $locais = Localizacao::where('usuario_localizacaos.usuario_id', $request->usuario_id)
                ->select('localizacaos.*')
                ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
                ->where('localizacaos.status', 1)->get();
                $local_id = $caixa->local_id;
            }
        }

        $item = Produto::with('estoque')
        ->select('produtos.*')
        ->where('referencia_balanca', $ref)
        ->where('empresa_id', $request->empresa_id)
        ->when($local_id != null, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->first();

        if($item == null){
            return response()->json("erro", 404);
        }

        if ($item->unidade == 'KG') {
            if ($balanca_valor_peso == 'valor') {
                $quantidade = $valor / $item->valor_unitario;
                $subtotal = $valor;
            } else {
                $quantidade = $valor / 10;
                $valor = $item->valor_unitario * number_format($quantidade, 3);
                $subtotal = $item->valor_unitario * number_format($quantidade, 3);
            }
        }else{
            $subtotal = $valor;
        }
        if ($item) {
            $item->valor = $valor;
            $item->quantidade = $quantidade;
        }
        $code = rand(0,9999999999);

        return view('front_box.partials_form2.row_produtos_referencia', compact('item', 'quantidade', 'valor', 'subtotal', 'code'));
    }

    public function infoVencimento($id)
    {
        $item = Produto::findOrFail($id);
        $itens = ItemNfe::where('produto_id', $item->id)->get();

        return view('produtos.partials.info_vencimento', compact('itens'));
    }

    public function validaEstoque(Request $request)
    {
        $produto = Produto::findOrFail($request->product_id);
        $qtd = $request->qtd;
        $local_id = $request->local_id;

        if($produto->gerenciar_estoque){

            if($produto->combo){
                $estoqueMsg = $this->util->verificaEstoqueCombo($produto, (float)$qtd);
                if($estoqueMsg != ""){
                    return response()->json($estoqueMsg, 401);
                }
            }else{

                $estoque = Estoque::where('produto_id', $request->product_id)
                ->when($local_id, function ($query) use ($local_id) {
                    return $query->where('local_id', $local_id);
                })
                ->first();
                if(!$estoque){
                    return response()->json("Produto sem estoque definido!", 401);
                }

                if($estoque->quantidade < $qtd){
                    return response()->json("Estoque insuficiente!", 401);
                }
            }
        }
        return response()->json($produto, 200);
    }

    public function pesquisaCompostos(Request $request)
    {
        $lista_id = $request->lista_id;
        $data = Produto::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('composto', 1)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->pesquisa%");
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('codigo_barras', 'LIKE', "%$request->pesquisa%");
        })
        ->get();

        if(is_numeric($request->pesquisa)){
            $dataAppend = ProdutoVariacao::where('produtos.empresa_id', $request->empresa_id)
            ->where('produto_variacaos.codigo_barras', 'LIKE', "%$request->pesquisa%")
            ->join('produtos', 'produtos.id', '=', 'produto_variacaos.produto_id')
            ->select('produto_variacaos.*')
            ->get();

            foreach($dataAppend as $v){
                $v->valor_unitario = $v->valor;
                $v->valor_compra = $v->produto->valor_compra;
                $v->nome = $v->produto->nome . " - " . $v->descricao;
                $v->codigo_variacao = $v->id;
                $v->id = $v->produto_id;
                $data->push($v);
            }

            // $data->push($dataAppend);
        }

        if($lista_id){

            foreach($data as $i){
                $itemLista = ItemListaPreco::where('lista_id', $lista_id)
                ->where('produto_id', $i->id)
                ->first();
                if($itemLista != null){
                    $i->valor_unitario = $itemLista->valor;
                }
            }
        }

        return response()->json($data, 200);
    }

    public function pesquisaCombo(Request $request)
    {
        $lista_id = $request->lista_id;
        $data = Produto::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('combo', 0)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->pesquisa%");
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('codigo_barras', 'LIKE', "%$request->pesquisa%");
        })
        ->get();

        return response()->json($data, 200);
    }

    public function marcaStore(Request $request){
        try{
            $item = Marca::create([
                'empresa_id' => $request->empresa_id,
                'nome' => $request->nome
            ]);
            return response()->json($item, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 200);
        }
    }

    public function categoriaStore(Request $request){
        try{
            $item = CategoriaProduto::create([
                'empresa_id' => $request->empresa_id,
                'nome' => $request->nome
            ]);
            return response()->json($item, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 200);
        }
    }

    public function validaAtacado(Request $request){
        $item = Produto::findOrFail($request->produto_id);

        $item = __tributacaoProdutoLocalVenda($item, $request->local_id);

        //valida estoque
        if($item->gerenciar_estoque){
            if(!$item->estoque){
                return response()->json("Estoque vazio!", 401);
            }
            if($item->estoque->quantidade < $request->quantidade){
                $qtd = $item->estoque->quantidade;
                if(!$item->unidadeDecimal()){
                    $qtd = number_format($item->estoque->quantidade, 0, '.', '');
                }
                return response()->json("Estoque insuficiente, o estoque atual é: " . $qtd, 401);
            }
        }

        if($item->quantidade_atacado > 0 && $request->quantidade >= $item->quantidade_atacado){
            if($item->valor_atacado > 0){
                return response()->json($item->valor_atacado, 200);
            }
        }
        return response()->json($item->valor_unitario, 200);
    }

    // public function validaEstoque(Request $request){
    //     $quantidade = __convert_value_bd($request->$quantidade);
    //     $item = Produto::findOrFail($request->produto_id);
    //     if($item->gerenciar_estoque){
            // if(!$item->estoque || $item->estoque->quantidade < $quantidade){
            //     return response()->json("Estoque insuficiente!", 401);
            // }
    //     }
    //     return response()->json("estoque ok", 200);
    // }

    public function dadosProdutoUnico($id){
        $item = ProdutoUnico::findOrFail($id);

        $nfeSaida = ProdutoUnico::where('codigo', $item->codigo)
        ->where('tipo', 'saida')->first();
        $nfeEntrada = ProdutoUnico::where('codigo', $item->codigo)
        ->where('tipo', 'entrada')->first();
        return view('produtos.partials.dados_produto_unico', compact('item', 'nfeSaida', 'nfeEntrada'));

    }

    public function info(Request $request){
        $item = Produto::findOrFail($request->produto_id);

        return view('produtos.partials.info', compact('item'));
    }

    public function linhaDimensao(Request $request){
        return view('produtos.partials.linha_dimensao', compact('request'));
    }

    public function getDimensaoEdit(Request $request){
        $item = ItemNfe::findOrFail($request->id);
        $view = view('produtos.partials.edit_dimensao', compact('item'))->render();

        return response()->json([
            'view' => $view,
            'produto' => $item->produto,
            'data' => $item->itensDimensao
        ], 200);
    }

    public function alterarGerenciamentoEstoque(Request $request){
        try{
            Produto::where('empresa_id', $request->empresa_id)
            ->update([
                'gerenciar_estoque' => $request->gerenciar_estoque
            ]);

            return response()->json("ok", 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 404);
        }
    }
}
