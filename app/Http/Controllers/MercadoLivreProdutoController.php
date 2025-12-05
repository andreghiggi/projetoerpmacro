<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MercadoLivreConfig;
use App\Models\Produto;
use App\Models\CategoriaMercadoLivre;
use App\Models\UnidadeMedida;
use App\Models\PadraoTributacaoProduto;
use App\Models\Empresa;
use App\Models\VariacaoMercadoLivre;
use Illuminate\Support\Facades\DB;
use App\Models\CategoriaProduto;
use App\Utils\EstoqueUtil;
use App\Utils\MercadoLivreUtil;
use App\Utils\UploadUtil;
use App\Models\ProdutoLocalizacao;

class MercadoLivreProdutoController extends Controller
{

    protected $util;
    protected $utilMercadoLivre;
    protected $uploadUtil;
    public function __construct(Request $request, EstoqueUtil $util, 
        MercadoLivreUtil $utilMercadoLivre, UploadUtil $uploadUtil)
    {
        $this->util = $util;
        $this->utilMercadoLivre = $utilMercadoLivre;
        $this->uploadUtil = $uploadUtil;

    }

    private function __validaToken(){
        $retorno = $this->utilMercadoLivre->refreshToken(request()->empresa_id);
        if($retorno != 'token valido!'){
            if($retorno == 0){
                return 0;
            }
            if(!isset($retorno->access_token)){
                dd($retorno);
            }
        }
    }

    public function index(Request $request){
        $token = $this->__validaToken();

        if($token == 0){
            return redirect()->route('mercado-livre-config.index');
        }
        $this->validaCategorias();
        $data = Produto::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->where('mercado_livre_id', '!=', null)
        ->paginate(__itensPagina());
        return view('mercado_livre_produtos.index', compact('data'));

    }

    private function validaCategorias(){
        $this->__validaToken();

        $categorias = CategoriaMercadoLivre::count();

        if($categorias == 0){
            $config = MercadoLivreConfig::where('empresa_id', request()->empresa_id)
            ->first();

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/sites/MLB/categories/all");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 0);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $config->access_token,
                'Content-Type: application/json'
            ]);

            $res = curl_exec($curl);
            $retorno = json_decode($res);
            foreach($retorno as $r){
                $cat = [
                    '_id' => $r->id,
                    'nome' => $r->name
                ];
                CategoriaMercadoLivre::create($cat);
            }
        }
    }

    public function edit($id){
        $item = Produto::findOrFail($id);

        $configMercadoLivre = MercadoLivreConfig::where('empresa_id',request()->empresa_id)
        ->first();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$item->mercado_livre_id");
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
        $prodML = json_decode($res);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$item->mercado_livre_id/description");
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
        $descricaoML = json_decode($res);

        return view('mercado_livre_produtos.edit', compact('item', 'prodML', 'descricaoML'));
    }

    public function update(Request $request, $id){
        $item = Produto::findOrFail($id);
        $configMercadoLivre = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$item->mercado_livre_id");
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

        $dataMercadoLivre = [
            'title' => $item->nome,
            // 'category_id' => $request->mercado_livre_categoria,
            // 'price' => __convert_value_bd($request->mercado_livre_valor),
            // 'available_quantity' => __convert_value_bd($request->quantidade_mercado_livre),
            'currency_id' => 'BRL',
            // 'condition' => $request->condicao_mercado_livre,
            // 'buying_mode' => 'buy_it_now',
            'video_id' => $request->mercado_livre_youtube,
        ];

        if(sizeof($prod->variations) > 0){
            for($i=0; $i<sizeof($request->variacao_id); $i++){
                $dataMercadoLivre['variations'][$i]['price'] = __convert_value_bd($request->variacao_valor[$i]);
                $dataMercadoLivre['variations'][$i]['available_quantity'] = __convert_value_bd($request->variacao_quantidade[$i]);
                $dataMercadoLivre['variations'][$i]['id'] = __convert_value_bd($request->variacao_id[$i]);
            }
        }else{
            $dataMercadoLivre['price'] = __convert_value_bd($request->mercado_livre_valor);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$item->mercado_livre_id");
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

            session()->flash("flash_error", $msg);
            return redirect()->back();
        }

        if($request->mercado_livre_descricao){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$item->mercado_livre_id/description");
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
            $retorno = json_decode($res);

        }

        if(sizeof($prod->variations) > 0){
            for($i=0; $i<sizeof($request->variacao_id); $i++){
                $variacao = VariacaoMercadoLivre::where('_id', $request->variacao_id[$i])
                ->first();

                $variacao->valor = __convert_value_bd($request->variacao_valor[$i]);
                $variacao->quantidade = __convert_value_bd($request->variacao_quantidade[$i]);
                $variacao->valor_nome = $request->variacao_valor_nome[$i];
                $variacao->nome = $request->variacao_nome[$i];
                $variacao->save();
            }
        }

        session()->flash("flash_success", "Produto atualizado!");
        return redirect()->route('mercado-livre-produtos.index');

    }

    private function trataErros($retorno){
        $msg = "";
        foreach($retorno->cause as $c){
            $msg .= $c->message;
        }
        return $msg;
    }

    public function produtosNew(Request $request){
        $token = $this->__validaToken();

        if($token == 0){
            return redirect()->route('mercado-livre-config.index');
        }

        $config = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
        ->first();
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/users/$config->user_id/items/search/?offset=0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ]);

        $res = curl_exec($curl);
        $retorno = json_decode($res);
        if(!isset($retorno->results)){
            session()->flash("flash_error", $retorno->message);
            return redirect()->route('mercado-livre-config.index');
        }
        $results = $retorno->results;
        $produtosIsert = [];
        foreach($results as $rcode){
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$rcode");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 0);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $config->access_token,
                'Content-Type: application/json'
            ]);

            $res = curl_exec($curl);
            $retorno = json_decode($res);

            $res = $this->validaProdutoCadastrado($retorno, $request->empresa_id);

            if(is_array($res)){
                $produtosIsert[] = $res;
            }
        }

        // dd($produtosIsert);

        if(sizeof($produtosIsert) > 0){
            $empresa = Empresa::findOrFail(request()->empresa_id);
            $listaCTSCSOSN = Produto::listaCSOSN();
            if ($empresa->tributacao == 'Regime Normal') {
                $listaCTSCSOSN = Produto::listaCST();
            }
            $padraoTributacao = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->where('padrao', 1)
            ->first();
            $padroes = PadraoTributacaoProduto::where('empresa_id', request()->empresa_id)->get();
            $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
            ->where('categoria_id', null)
            ->get();

            $unidades = UnidadeMedida::where('empresa_id', request()->empresa_id)
            ->where('status', 1)->get();

            return view('mercado_livre_produtos.create_produtos', 
                compact('produtosIsert', 'padraoTributacao', 'listaCTSCSOSN', 'padroes', 'categorias', 'unidades'));
        }else{
            return redirect()->route('mercado-livre-produtos.index');
        }
    }

    private function validaProdutoCadastrado($mlProduto, $empresa_id){

        $produto = Produto::where('empresa_id', $empresa_id)
        ->where('mercado_livre_id', $mlProduto->id)
        ->first();
        if($produto != null){
            $this->atualizaProduto($mlProduto, $produto);
            return true;
        }
        // dd($mlProduto);

        $dataProduto = [
            'empresa_id' => $empresa_id,
            'nome' => $mlProduto->title,
            'valor_venda' => $mlProduto->price,
            'mercado_livre_id' => $mlProduto->id,
            'mercado_livre_valor' => $mlProduto->price,
            'mercado_livre_link' => $mlProduto->permalink,
            'estoque' => $mlProduto->available_quantity,
            'status' => $mlProduto->status,
            'mercado_livre_categoria' => $mlProduto->category_id
        ];

        if(sizeof($mlProduto->variations) > 0){
            $variacoes = [];
            foreach($mlProduto->variations as $v){
                $dataVariacao = [
                    '_id' => $v->id,
                    'quantidade' => $v->available_quantity,
                    'valor' => $v->price,
                    'nome' => $v->attribute_combinations[0]->name,
                    'valor_nome' => $v->attribute_combinations[0]->value_name
                ];
                array_push($variacoes, $dataVariacao);
            }

            $dataProduto['variacoes'] = $variacoes;
        }
        // dd($dataProduto);
        return $dataProduto;
    }

    private function atualizaProduto($mlProduto, $produto){

        $produto->mercado_livre_status = $mlProduto->status;
        $produto->mercado_livre_valor = $mlProduto->price;
        $produto->nome = $mlProduto->title;
        $produto->save();
    }

    public function store(Request $request){

        DB::transaction(function () use ($request) {
            $contInserts = 0;
            try{
                for($i=0; $i<sizeof($request->mercado_livre_id); $i++){

                    $last = Produto::where('empresa_id', $request->empresa_id)
                    ->orderBy('numero_sequencial', 'desc')
                    ->where('numero_sequencial', '>', 0)->first();
                    $numeroSequencial = $last != null ? $last->numero_sequencial : 0;
                    $numeroSequencial++;
                    $data = [
                        'numero_sequencial' => $numeroSequencial,
                        'mercado_livre_id' => $request->mercado_livre_id[$i],
                        'nome' => $request->nome[$i],
                        'valor_unitario' => __convert_value_bd($request->valor_venda[$i]),
                        'mercado_livre_valor' => __convert_value_bd($request->mercado_livre_valor[$i]),
                        'valor_compra' => $request->valor_compra[$i] ? __convert_value_bd($request->valor_compra[$i]) : 0,
                        'codigo_barras' => $request->codigo_barras[$i],
                        'ncm' => $request->ncm[$i],
                        'unidade' => $request->unidade[$i],
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
                        'mercado_livre_status' => $request->mercado_livre_status[$i],
                        'mercado_livre_categoria' => $request->mercado_livre_categoria[$i],
                        'valor_prazo' => 0
                    ];
                    
                    $produto = Produto::create($data);
                    ProdutoLocalizacao::updateOrCreate([
                        'produto_id' => $produto->id, 
                        'localizacao_id' => $request->local_id
                    ]);
                    if($request->mercado_livre_id_row){
                        for($j=0; $j<sizeof($request->mercado_livre_id_row); $j++){
                            if($request->mercado_livre_id[$i] == $request->mercado_livre_id_row[$j]){
                                $dataVariacao = [
                                    'produto_id' => $produto->id,
                                    '_id' => $request->variacao_id[$j],
                                    'quantidade' => __convert_value_bd($request->variacao_quantidade[$j]),
                                    'valor' => __convert_value_bd($request->variacao_valor[$j]),
                                    'nome' => $request->variacao_nome[$j],
                                    'valor_nome' => $request->variacao_valor_nome[$j]
                                ];
                                VariacaoMercadoLivre::create($dataVariacao);
                            }
                        }
                    }
                    if($request->estoque[$i]){
                        $this->util->incrementaEstoque($produto->id, $request->estoque[$i], null);
                    }
                    $contInserts++;
                }
                session()->flash("flash_success", "Total de produtos inseridos: $contInserts");

            }catch(\Exception $e){
                session()->flash("flash_error", $e->getMessage());
            }

        });
return redirect()->route('mercado-livre-produtos.index');
}

public function galery($id){
    $this->__validaToken();

    $item = Produto::findOrFail($id);
    $curl = curl_init();
    $config = MercadoLivreConfig::where('empresa_id', $item->empresa_id)
    ->first();
    curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$item->mercado_livre_id");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config->access_token,
        'Content-Type: application/json'
    ]);

    $res = curl_exec($curl);
    $retorno = json_decode($res);
    return view('mercado_livre_produtos.galery', compact('item', 'retorno'));
}

public function galeryStore(Request $request){
    $item = Produto::findOrFail($request->produto_id);
    if ($request->hasFile('image')) {
        $file_name = $this->uploadUtil->uploadImage($request, '/temp-ml');
    }else{
        session()->flash("flash_error", "Selecione uma imagem!");
        return redirect()->back();
    }

    $config = MercadoLivreConfig::where('empresa_id', $item->empresa_id)
    ->first();

    $urlImage = $config->url . "/uploads/temp-ml/$file_name";

    $dataMercadoLivre = [];
    $cont = 0;
    for($i=0; $i<sizeof($request->picture); $i++){
        $dataMercadoLivre['pictures'][$i]['source'] = $request->picture[$i];
        $cont++;
    }
    $dataMercadoLivre['pictures'][$cont]['source'] = $urlImage;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$item->mercado_livre_id");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataMercadoLivre));

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config->access_token,
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    $res = curl_exec($curl);
    $retorno = json_decode($res);

    if(isset($retorno->id)){
        $files = glob('uploads/temp-ml/*');
        foreach($files as $file){ 
            if(is_file($file)) {
                unlink($file); 
            }
        }
        session()->flash("flash_success", "Imagem adicionada!");
    }else{
        session()->flash("flash_error", $retorno->message);
    }
    return redirect()->back();

}

public function galeryDelete(Request $request){
    $item = Produto::findOrFail($request->produto_id);
    $config = MercadoLivreConfig::where('empresa_id', $item->empresa_id)
    ->first();

    $dataMercadoLivre = [];
    for($i=0; $i<sizeof($request->picture); $i++){
        $dataMercadoLivre['pictures'][$i]['source'] = $request->picture[$i];
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/items/$item->mercado_livre_id");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataMercadoLivre));

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config->access_token,
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    $res = curl_exec($curl);
    $retorno = json_decode($res);

    if(isset($retorno->id)){
        session()->flash("flash_success", "Imagem removida!");
    }else{
        session()->flash("flash_error", $retorno->message);
    }
    return redirect()->back();

}

}
