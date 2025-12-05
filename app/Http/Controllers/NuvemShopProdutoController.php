<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\Estoque;
use App\Models\UnidadeMedida;
use App\Models\PadraoTributacaoProduto;
use App\Models\CategoriaNuvemShop;
use App\Models\CategoriaProduto;
use App\Models\ProdutoLocalizacao;
use App\Models\ProdutoVariacao;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Utils\NuvemShopUtil;
use App\Utils\EstoqueUtil;

class NuvemShopProdutoController extends Controller
{

    protected $util;
    protected $utilEstoque;

    public function __construct(NuvemShopUtil $util, EstoqueUtil $utilEstoque)
    {
        $this->util = $util;
        $this->utilEstoque = $utilEstoque;
    }

    private function validaCategorias($empresa_id){
        $store_info = session('store_info');

        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
        try{
            $categorias = (array)$api->get("categories");
            $data = $categorias['body'];
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        CategoriaNuvemShop::where('empresa_id', $empresa_id)->delete();
        foreach($data as $c){
            $categoria = [
                'empresa_id' => $empresa_id,
                'nome' => $c->name->pt,
                '_id' => $c->id
            ];
            CategoriaNuvemShop::create($categoria);
        }
    }

    public function index(Request $request){

        $page = $request->page ? $request->page : 1;
        $search = $request->search;
        $store_info = session('store_info');
        if(!$store_info){
            return redirect()->route('nuvem-shop-auth.index');
        }

        $this->validaCategorias($request->empresa_id);
        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');

        if($search != ""){
            $produtos = (array)$api->get("products?q='".$search."'&per_page=21");
        }else{
            try{
                $produtos = (array)$api->get("products?page=".$page."&per_page=21");
            }catch(\Exception $e){
                session()->flash("flash_error", $e->getMessage());
                return redirect()->route('nuvem-shop-produtos.index', ['page='.$page-1]);
            }
        }
        $data = $produtos['body'];

        $produtosIsert = [];
        foreach($data as $p){
            $res = $this->validaProdutoCadastrado($p, $request->empresa_id);
            if(is_array($res)){
                $produtosIsert[] = $res;
            }

            $produto = Produto::where('empresa_id', $request->empresa_id)
            ->where('nuvem_shop_id', $p->id)
            ->first();
            if($produto){
                $p->variacoes = $produto->variacoes;
            }

        }

        if(sizeof($produtosIsert) > 0){

            $empresa = Empresa::findOrFail(request()->empresa_id);
            $listaCTSCSOSN = Produto::listaCSOSN();
            if ($empresa->tributacao == 'Regime Normal') {
                $listaCTSCSOSN = Produto::listaCST();
            }
            $padraoTributacao = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->where('padrao', 1)
            ->first();
            $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();
            $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
            ->where('categoria_id', null)
            ->get();
            $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
            ->where('status', 1)->get();
            return view('nuvem_shop_produtos.create_produtos', 
                compact('produtosIsert', 'padraoTributacao', 'listaCTSCSOSN', 'padroes', 'categorias', 'unidades'));
        }

        return view('nuvem_shop_produtos.index', compact('data', 'page', 'search'));
    }


    // private function atualizaProduto($nuvemShopProduto, $produto){
    //     dd($nuvemShopProduto);
    //     $produto->mercado_livre_valor = $nuvemShopProduto->price;
    //     $produto->nome = $nuvemShopProduto->title;
    //     $produto->save();
    // }

    private function validaProdutoCadastrado($nuvemShopProduto, $empresa_id){

        $produto = Produto::where('empresa_id', $empresa_id)
        ->where('nuvem_shop_id', $nuvemShopProduto->id)
        ->first();

        if($produto != null){
            // $this->atualizaProduto($nuvemShopProduto, $produto);
            return true;
        }
        $valorVenda = __convert_value_bd($nuvemShopProduto->variants[0]->price);
        $dataProduto = [
            'empresa_id' => $empresa_id,
            'nome' => $nuvemShopProduto->name->pt,
            'valor_venda' => $valorVenda,
            'codigo_barras' => $nuvemShopProduto->variants[0]->barcode,
            'nuvem_shop_id' => $nuvemShopProduto->id,
            'nuvem_shop_valor' => $valorVenda,
            'estoque' => $nuvemShopProduto->variants[0]->stock ? __convert_value_bd($nuvemShopProduto->variants[0]->stock) : 0,
            'status' => 1,
        ];
        // dd($dataProduto);

        if(sizeof($nuvemShopProduto->variants) > 0){
            $variacoes = [];
            foreach($nuvemShopProduto->variants as $v){
                $variacaoNome = isset($v->values[0]) ? $v->values[0]->pt : '';
                $variacaoNome .= isset($v->values[1]) ? ' ' . $v->values[1]->pt : '';
                $dataVariacao = [
                    '_id' => $v->id,
                    'quantidade' => $v->stock,
                    'valor' => $v->price,
                    'nome' => $variacaoNome,
                    'valor_nome' => ''
                ];
                // dd($v);
                // dd($dataVariacao);
                array_push($variacoes, $dataVariacao);
            }

            $dataProduto['variacoes'] = $variacoes;
        }
        // dd($dataProduto);
        return $dataProduto;
    }

    public function store(Request $request){

        DB::transaction(function () use ($request) {
            $contInserts = 0;
            $contUpdates = 0;
            try{
                for($i=0; $i<sizeof($request->nuvem_shop_id); $i++){

                    if($request->produto_vinculacao_id[$i] == -1){
                        $data = [
                            'nuvem_shop_id' => $request->nuvem_shop_id[$i],
                            'nome' => $request->nome[$i],
                            'valor_unitario' => __convert_value_bd($request->valor_venda[$i]),
                            'nuvem_shop_valor' => __convert_value_bd($request->nuvem_shop_valor[$i]),
                            'valor_compra' => $request->valor_compra[$i] ? __convert_value_bd($request->valor_compra[$i]) : 0,
                            'codigo_barras' => $request->codigo_barras[$i],
                            'ncm' => $request->ncm[$i],
                            'unidade' => $request->unidade[$i] ? $request->unidade[$i] : 'UN',
                            'gerenciar_estoque' => $request->gerenciar_estoque[$i],
                            'categoria_id' => $request->categoria_id[$i],
                            'cest' => $request->cest[$i],
                            'cfop_estadual' => $request->cfop_estadual[$i],
                            'cfop_outro_estado' => $request->cfop_outro_estado[$i],
                            'perc_icms' => __convert_value_bd($request->perc_icms[$i]),
                            'perc_pis' => __convert_value_bd($request->perc_pis[$i]),
                            'perc_cofins' => __convert_value_bd($request->perc_cofins[$i]),
                            'perc_ipi' => __convert_value_bd($request->perc_ipi[$i]),
                            'perc_red_bc' => $request->perc_red_bc[$i] ? __convert_value_bd($request->perc_red_bc[$i]) : 0,
                            'cst_csosn' => $request->cst_csosn[$i],
                            'cst_pis' => $request->cst_pis[$i],
                            'cst_cofins' => $request->cst_cofins[$i],
                            'cst_ipi' => $request->cst_ipi[$i],
                            'cEnq' => $request->cEnq[$i],
                            'empresa_id' => $request->empresa_id,
                            'valor_prazo' => 0
                        ];
                        $produto = Produto::create($data);

                        if(isset($request->local_id)){
                            ProdutoLocalizacao::updateOrCreate([
                                'produto_id' => $produto->id, 
                                'localizacao_id' => $request->local_id
                            ]);
                        }else{
                            $locais = isset($request->locais) ? $request->locais : [];
                            for($j=0; $j<sizeof($locais); $j++){
                                ProdutoLocalizacao::updateOrCreate([
                                    'produto_id' => $produto->id, 
                                    'localizacao_id' => $locais[$j]
                                ]);
                            }
                        }

                        if($request->nuvem_shop_id_row){
                            for($j=0; $j<sizeof($request->nuvem_shop_id_row); $j++){
                                if($request->nuvem_shop_id[$i] == $request->nuvem_shop_id_row[$j]){
                                    $dataVariacao = [
                                        'produto_id' => $produto->id,
                                        'valor' => __convert_value_bd($request->variacao_valor[$j]),
                                        'codigo_barras' => '',
                                        'referencia' => '',
                                        'descricao' => $request->variacao_nome[$j]
                                    ];
                                    ProdutoVariacao::create($dataVariacao);
                                }
                            }
                        }
                        if($request->estoque[$i]){
                            $this->utilEstoque->incrementaEstoque($produto->id, $request->estoque[$i], null);
                        }
                        $contInserts++;
                    }else{

                        $produto = Produto::findOrFail($request->produto_vinculacao_id[$i]);
                        $produto->nuvem_shop_id = $request->nuvem_shop_id[$i];
                        $produto->save();
                        $contUpdates++;

                    }
                }
                session()->flash("flash_success", "Total de produtos inseridos: $contInserts, atualizados: " .$contUpdates);

            }catch(\Exception $e){
                // echo __getError($e);
                // die;
                session()->flash("flash_error", $e->getMessage());
            }
        });
return redirect()->route('nuvem-shop-produtos.index');
}

public function edit($id){
    $item = Produto::where('nuvem_shop_id', $id)->first();
    $store_info = session('store_info');
    $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
    $produto = (array)$api->get("products/".$id);
    $produto = $produto['body'];
    $this->validaCategorias($item->empresa_id);
    // dd($produto);
    $categoria = null;
    if($produto->categories){
        $categoria = [$produto->categories[0]->id => $produto->categories[0]->name->pt];
    }
    return view('nuvem_shop_produtos.edit', compact('item', 'produto', 'categoria'));
}

public function destroy($id){
    $item = Produto::where('nuvem_shop_id', $id)->first();
    $store_info = session('store_info');
    $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
    try{
        $response = $api->delete("products/$id");
        if(isset($response->status_code) && $response->status_code == 200){
            session()->flash("flash_success", "Produto removido!");
            $item->variacoes()->delete();
            $item->variacoesMercadoLivre()->delete();
            $item->itemLista()->delete();
            $item->itemNfe()->delete();
            $item->itemNfce()->delete();
            $item->itemCarrinhos()->delete();
            $item->movimentacoes()->delete();
            $item->composicao()->delete();
            $item->itemPreVenda()->delete();
            $item->locais()->delete();
            if($item->estoque){
                $item->estoque->delete();
            }
            $item->delete();
        }else{
            session()->flash("flash_error", "Erro ao remover produto!");
        }

    }catch(\Exception $e){
        session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
    }
    return redirect()->route('nuvem-shop-produtos.index');
}

public function update(Request $request, $id){
    $item = Produto::findOrFail($id);
    $categoria_nuvem_shop = $request->categoria_nuvem_shop;
    try{
        $item->nome = $request->nome;
        $item->codigo_barras = $request->codigo_barras;
        $item->save();

        $store_info = session('store_info');
        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
        $response = $api->put("products/$item->nuvem_shop_id", [
            'name' => $request->nome,
            'description' => $request->texto_nuvem_shop,
            'categories' => $categoria_nuvem_shop ? [$categoria_nuvem_shop] : []
        ]);

        $produto = (array)$api->get("products/$item->nuvem_shop_id");
        $produto = $produto['body'];

        if(isset($request->variacao_id)){

            for($i=0; $i<sizeof($request->variacao_id); $i++){
                $variation_id = $request->variacao_id[$i];
                $price = $request->variacao_valor[$i];
                $estoque = $request->variacao_quantidade[$i];


                $dataVariacao = [
                    'produto_id' => $item->id,
                    'valor' => __convert_value_bd($request->variacao_valor[$i]),
                    'codigo_barras' => '',
                    'referencia' => '',
                    'descricao' => $request->variacao_nome[$i]
                ];
                ProdutoVariacao::create($dataVariacao);

                $dataProduto = [
                    'price' => __convert_value_bd($price),
                    'barcode' => $request->codigo_barras,
                    "weight" => $request->peso_nuvem_shop,
                    "width" => $request->largura_nuvem_shop,
                    "height" => $request->altura_nuvem_shop,
                    "depth" => $request->comprimento_nuvem_shop,
                ];
                if($estoque){

                    $dataProduto['stock'] = $estoque;
                }
                $api->put("products/$item->nuvem_shop_id/variants/".$variation_id, $dataProduto);
            }

        }else{
            if(sizeof($produto->variants) == 1){
                $dataProduto = [
                    'price' => __convert_value_bd($request->nuvem_shop_valor),
                    'promotional_price' => __convert_value_bd($request->nuvem_shop_valor_promocional),
                    'barcode' => $request->codigo_barras,
                    "weight" => $request->peso_nuvem_shop,
                    "width" => $request->largura_nuvem_shop,
                    "height" => $request->altura_nuvem_shop,
                    "depth" => $request->comprimento_nuvem_shop,
                ];

                if($request->estoque){
                    $dataProduto['stock'] = $request->estoque;

                    $estoque = $item->estoque;
                    if(!$estoque){
                        $this->utilEstoque->incrementaEstoque($item->id, $request->estoque, null);
                    }else{
                        $estoque->quantidade = $request->estoque;
                        $estoque->save();
                    }

                    $transacao = Estoque::where('produto_id', $item->id)->orderBy('id', 'desc')->first();
                    $tipo = 'incremento';
                    $codigo_transacao = $transacao->id;
                    $tipo_transacao = 'alteracao_estoque';
                    $this->utilEstoque->movimentacaoProduto($item->id, $request->estoque, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id);
                }

                $api->put("products/$item->nuvem_shop_id/variants/".$produto->variants[0]->id, $dataProduto);
            }
        }
        session()->flash("flash_success", "Produto atualizado!");
    }catch(\Exception $e){
        session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
    }
    return redirect()->route('nuvem-shop-produtos.index');
}

public function galery($id){
    $item = Produto::where('nuvem_shop_id', $id)->first();
    $store_info = session('store_info');
    $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
    $produto = (array)$api->get("products/".$id);
    $produto = $produto['body'];
    return view('nuvem_shop_produtos.galery', compact('item', 'produto'));

}

public function galeryDelete(Request $request){
    $item = Produto::findOrFail($request->produto_id);
    $store_info = session('store_info');
    $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');
    try{
        $response = $api->delete("products/$item->nuvem_shop_id/images/$request->imagem_id");
        session()->flash("flash_success", "Imagem removida!");

    }catch(\Exception $e){
        session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());

    }
    return redirect()->back();
}

public function galeryStore(Request $request){
    $item = Produto::findOrFail($request->produto_id);
    if ($request->hasFile('image')) {
        $store_info = session('store_info');
        $api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');

        $image = base64_encode(file_get_contents($request->file('image')->path()));

        $ext = $request->file('image')->getClientOriginalExtension();
        $response = $api->post("products/$item->nuvem_shop_id/images",[
            "filename" => Str::random(20).".".$ext,
            "attachment" => $image
        ]);
        session()->flash("flash_success", "Imagem salva!");

    }else{
        session()->flash("flash_error", "Selecione uma imagem!");
    }

    return redirect()->back();
}

}
