<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendiZapConfig;
use App\Models\Produto;
use App\Models\CategoriaVendiZap;
use App\Models\Localizacao;
use App\Models\ProdutoLocalizacao;
use Illuminate\Support\Str;
use App\Models\VariacaoModelo;
use App\Models\VariacaoModeloItem;
use App\Models\ProdutoVariacao;
use App\Utils\EstoqueUtil;
use App\Models\Estoque;

class VendiZapProdutoController extends Controller
{
    protected $url = "https://app.vendizap.com/api";
    protected $utilEstoque;

    public function __construct(EstoqueUtil $utilEstoque)
    {
        $this->utilEstoque = $utilEstoque;
    }

    private function getVariations($config){
        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];

        curl_setopt($ch, CURLOPT_URL, $this->url . '/variacoes');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = json_decode(curl_exec($ch));
        curl_close($ch);

        if(!is_array($data)){
            session()->flash("flash_error", $data);
            return redirect()->route('vendizap-config.index');
        }
        $this->cadastrarVariacoesVendiZap($data);
    }

    private function cadastrarVariacoesVendiZap($data){
        // dd($data);
        foreach($data as $variacao){
            $dataVariacao = [
                'descricao' => $variacao->nome,
                'status' => 1,
                'empresa_id' => request()->empresa_id,
                'vendizap_id' => $variacao->id
            ];

            $item = VariacaoModelo::updateOrCreate($dataVariacao);

            foreach($variacao->variaveis as $va){
                $variacaoModeloItem = [
                    'variacao_modelo_id' => $item->id,
                    'nome' => $va->nome,
                    'vendizap_id' => $va->id
                ];
                VariacaoModeloItem::updateOrCreate($variacaoModeloItem);
            }
        }
    }

    public function index(Request $request){

        $config = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();
        if($config == null){
            session()->flash("flash_error", "Configure as credenciais!");
            return redirect()->route('vendizap-config.index');
        }

        $limite = env("PAGINACAO");
        $skip = $request->skip;
        if(isset($request->proxima_pagina)){
            $skip += $limite;
        }

        if(isset($request->pagina_anterior)){
            $skip -= $limite;
        }
        // dd($skip);

        $this->getVariations($config);

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];
        $nome = $request->nome;

        $urlFilter = $this->url . "/produtos?limit=$limite";
        if($nome){
            $urlFilter .= "&descricao=$nome";
        }

        $urlFilter .= "&skip=$skip";

        curl_setopt($ch, CURLOPT_URL, $urlFilter);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        // dd($data);
        if(!is_array($data)){
            session()->flash("flash_error", $data);
            return redirect()->route('vendizap-config.index');
        }
        foreach($data as $c){
            $item = Produto::where('empresa_id', $request->empresa_id)
            ->where('vendizap_id', $c->id)->first();

            if($item == null){
                $item = $this->cadastrarProduto($c);
            }

            $c->data_cadastro = __data_pt($item->created_at);
        }
        return view('vendizap_produtos.index', compact('data', 'skip', 'limite'));

    }

    private function cadastrarProduto($p){
        $produto = Produto::create([
            'vendizap_id' => $p->id,
            'empresa_id' => request()->empresa_id,
            'nome' => $p->descricao,
            'valor_unitario' => isset($p->preco) ? $p->preco : 0,
        ]);

        $config = VendiZapConfig::where('empresa_id', request()->empresa_id)->first();
        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];

        curl_setopt($ch, CURLOPT_URL, $this->url . '/produtos/'.$p->id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        // dd($data);

        // cadastrar variacao

        $locais = Localizacao::where('empresa_id', request()->empresa_id)->get();
        foreach($locais as $l){
            ProdutoLocalizacao::updateOrCreate([
                'produto_id' => $produto->id, 
                'localizacao_id' => $l->id
            ]);
        }
        return $produto;
    }

    public function edit($id){

        $item = Produto::where('vendizap_id', $id)->first();
        $config = VendiZapConfig::where('empresa_id', request()->empresa_id)->first();

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
        ];

        curl_setopt($ch, CURLOPT_URL, $this->url . '/produtos/'.$id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = json_decode(curl_exec($ch));
        curl_close($ch);

        if($item == null){
            //cadastrar item
            $item = $this->cadastrarProduto($data);

            //cadastrar variação
        }else{
            $this->sincronizaEstoque($data);
        }

        $variacoes = [];

        if(sizeof($data->variacoes) > 0 && $item != null){
            // dd($data);

            $variacaoModelo = VariacaoModelo::where('vendizap_id', $data->variacoes[0]->id)
            ->first();
            if($variacaoModelo){
                $item->variacao_modelo_id = $variacaoModelo->id;
                $item->save();
            }

            $this->defineVariacoes($data, $item);

            if(sizeof($data->variacoes) == 1){
                foreach($data->variacoes[0]->variaveis as $var){
                    $qtd = $this->getEstoqueCombinacao($data->variacoes[0]->nome, $var->nome, $data);
                    $variacoes[] = [
                        'variacao_id' => $data->variacoes[0]->id,
                        'variavel_id' => $var->id,
                        'variacao' => $data->variacoes[0]->nome,
                        'descricao' => $var->nome,
                        'valor' => $data->preco + $var->preco,
                        'quantidade' => $qtd,
                        'sku' => ''
                    ];
                }
            }
            if(sizeof($data->variacoes) == 2){
                // dd($data);
                $combinacoes = $this->criaCombinacoes($data);
                foreach($combinacoes as $nomeCombinacao){

                    $qtd = $this->getEstoqueCombinacaoMulti($nomeCombinacao, $data);
                    $idsCombinacao = $this->getIdsCombinacao($nomeCombinacao, $data);
                    $valorCombinacao = $this->getValorCombinacao($nomeCombinacao, $data);
                    $variacoes[] = [
                        'variacao_id' => $idsCombinacao['variacaoId1'],
                        'variavel_id' => $idsCombinacao['variavelId1'],
                        'variacao_id2' => $idsCombinacao['variacaoId2'],
                        'variavel_id2' => $idsCombinacao['variavelId2'],
                        'variacao' => '',
                        'descricao' => $nomeCombinacao,
                        'valor' => $data->preco + $valorCombinacao, //acrescentar valor
                        'quantidade' => $qtd,
                    ];
                }

            }

        }
        // dd($variacoes);

        $item->estoque = '';
        $item->promocao = '';
        if(isset($data->estoque->_produto)){
            $item->estoque = $data->estoque->_produto;
        }

        if(isset($data->promocao->precoPromocional)){
            $item->promocao = $data->promocao->precoPromocional;
        }

        if($data->imagens && sizeof($data->imagens) > 0){
            $item->imagemUrl = $data->imagens[0];
        }

        $categorias = CategoriaVendiZap::where('empresa_id', request()->empresa_id)->get();

        $categoriaSelect = [];
        foreach($data->categorias as $c){
            $categoriaSelect[] = $c->id;
        }

        return view('vendizap_produtos.edit', compact('item', 'data', 'categorias', 'categoriaSelect', 'variacoes'));
    }

    private function getIdsCombinacao($nome, $data){

        $arrayNome = explode(" ", $nome);
        $variacaoId1 = $data->variacoes[0]->id;
        $variacaoId2 = $data->variacoes[1]->id;
        $variavelId1 = null;
        $variavelId2 = null;
        // dd($data->variacoes);
        foreach($data->variacoes as $key => $variacao){
            foreach($variacao->variaveis as $variavel){
                if($variavelId1 == null){
                    if($arrayNome[0] == $variavel->nome){
                        $variavelId1 = $variavel->id;
                    }
                }else{
                    if($arrayNome[1] == $variavel->nome){
                        $variavelId2 = $variavel->id;
                    }
                }
            }
        }
        $retorno = [
            'variacaoId1' => $variacaoId1,
            'variacaoId2' => $variacaoId2,
            'variavelId1' => $variavelId1,
            'variavelId2' => $variavelId2,
        ];
        return $retorno;

    }

    private function getValorCombinacao($nome, $data){
        // dd($data);
        $arrayNome = explode(" ", $nome);
        $valor = 0;
        $achou1 = 0;
        $achou2 = 0;
        // dd($data->variacoes);
        foreach($data->variacoes as $key => $variacao){
            foreach($variacao->variaveis as $variavel){
                if($achou1 == 0){
                    if($arrayNome[0] == $variavel->nome){
                        $achou1 = 1;
                        $valor += $variavel->preco;
                    }
                }else{
                    if($arrayNome[1] == $variavel->nome){
                        $valor += $variavel->preco;
                    }
                }
            }
        }

        return $valor;
    }

    private function defineVariacoes($data, $item){

        if(sizeof($data->variacoes) == 1){
            // dd($data);
            foreach($data->variacoes as $v){
                $variacaoModelo = VariacaoModelo::where('empresa_id', request()->empresa_id)
                ->where('descricao', $v->nome)->first();
                if($variacaoModelo == null){
                    $variacaoModelo = VariacaoModelo::create([
                        'descricao' => $v->nome,
                        'status' => 1,
                        'empresa_id' => request()->empresa_id,
                        'vendizap_id' => $v->id
                    ]);
                }

                foreach($v->variaveis as $va){

                    VariacaoModeloItem::updateOrCreate([
                        'variacao_modelo_id' => $variacaoModelo->id,
                        'nome' => $va->nome
                    ]);

                    $produtoVariacao = ProdutoVariacao::where('descricao', $va->nome)
                    ->where('produto_id', $item->id)->first();

                    if($produtoVariacao == null){

                        $item->variacao_modelo_id = $variacaoModelo->id;
                        $item->save();
                        $dataProdutoVariacao = [
                            'produto_id' => $item->id,
                            'descricao' => $va->nome,
                            'valor' => $data->preco + $va->preco
                        ];
                        $produtoVariacao = ProdutoVariacao::create($dataProdutoVariacao);

                        // add estoque
                        $qtd = $this->getEstoqueCombinacao($v->nome, $va->nome, $data);
                        if($qtd > 0){
                            $this->utilEstoque->incrementaEstoque($item->id, $qtd, $produtoVariacao->id);
                            $transacao = Estoque::where('produto_id', $item->id)->orderBy('id', 'desc')->first();
                            $tipo = 'incremento';
                            $codigo_transacao = $transacao->id;
                            $tipo_transacao = 'alteracao_estoque';
                            $this->utilEstoque->movimentacaoProduto($item->id, $qtd, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $produtoVariacao->id);
                        }
                    }
                }
            }
        }else{
            //combo variação
            $combinacoes = $this->criaCombinacoes($data);
            // dd($combinacoes);
            foreach($combinacoes as $nomeCombinacao){
                // dd($nomeCombinacao);
                $produtoVariacao = ProdutoVariacao::where('descricao', $nomeCombinacao)
                ->where('produto_id', $item->id)->first();

                if($produtoVariacao == null){
                    $dataProdutoVariacao = [
                        'produto_id' => $item->id,
                        'descricao' => $nomeCombinacao,
                        'valor' => $data->preco
                    ];
                    // dd($data->variacoes[0]->id);
                    $qtd = $this->getEstoqueCombinacaoMulti($nomeCombinacao, $data);
                    $variacaoModelo = VariacaoModelo::where('vendizap_id', $data->variacoes[0]->id)
                    ->first();
                    $produtoVariacao = ProdutoVariacao::create($dataProdutoVariacao);

                    if($variacaoModelo){
                        $item->variacao_modelo_id = $variacaoModelo->id;
                        $item->save();
                    }
                    // add estoque

                    if($qtd > 0){
                        $this->utilEstoque->incrementaEstoque($item->id, $qtd, $produtoVariacao->id);
                        $transacao = Estoque::where('produto_id', $item->id)->orderBy('id', 'desc')->first();
                        $tipo = 'incremento';
                        $codigo_transacao = $transacao->id;
                        $tipo_transacao = 'alteracao_estoque';
                        $this->utilEstoque->movimentacaoProduto($item->id, $qtd, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $produtoVariacao->id);
                    }
                }

            }

        }
    }

    private function criaCombinacoes($data){
        // dd($data->variacoes);
        $variacoes1 = [];
        foreach($data->variacoes[0]->variaveis as $va){
            $variacoes1[] = "$va->nome";
        }

        $combinacoes = [];
        for($i=0; $i<sizeof($variacoes1); $i++){
            foreach($data->variacoes[1]->variaveis as $va){
                $combinacoes[] = "$variacoes1[$i] $va->nome";
            }
        }

        return $combinacoes;
    }

    private function getEstoqueCombinacao($variacaoNome, $itemVariacaoNome, $data){
        // dd($data);
        try{
            if(isset($data->estoque->combinacoes)){
                foreach($data->estoque->combinacoes as $combinacao){
                    $c = $combinacao->combinacao;
                    if($c->$variacaoNome == $itemVariacaoNome){
                        return $combinacao->quantidade;
                    }
                }
            }
        }catch(\Exception $e){

        }
        return 0;
    }

    private function getEstoqueCombinacaoMulti($nome, $data){
        try{
            if(isset($data->estoque->combinacoes)){
                foreach($data->estoque->combinacoes as $combinacao){
                    $c = $combinacao->combinacao;
                    $c = $this->getCombinacao($c, $data, $nome);
                    if($c != null){
                        return $combinacao->quantidade;
                    }
                }
            }
        }catch(\Exception $e){
            // dd($e->getMessage());
        }
        return 0;
    }

    private function getCombinacao($combinacao, $data, $nome){
        $variacoes = [];
        foreach($data->variacoes as $v){
            $variacoes[] = $v->nome;
        }
        $combinacao = (array)$combinacao;
        $nome1 = $combinacao[$variacoes[0]] . " " . $combinacao[$variacoes[1]];

        if($nome == $nome1){
            return $nome1;
        }

        $nome2 = $combinacao[$variacoes[1]] . " " . $combinacao[$variacoes[0]];
        if($nome == $nome2){
            return $nome2;
        }

        return null;
    }

    public function update(Request $request, $id){
        $item = Produto::findOrFail($id);
        $config = VendiZapConfig::where('empresa_id', request()->empresa_id)->first();

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
            'Content-Type: application/json'
        ];

        $data = [
            'descricao' => $request->nome,
            'preco' => __convert_value_bd($request->preco),
            'exibir' => (bool)$request->exibir,
            'destaque' => (bool)$request->destaque,
            'codigo' => $request->codigo ?? '',
            'video' => $request->video ?? '',
        ];

        if($request->promocao){
            $data['promocao']['precoPromocional'] = __convert_value_bd($request->promocao);
        }

        if($request->categorias){
            $categorias = [];
            for($i=0; $i<sizeof($request->categorias); $i++){
                $categorias[] = $request->categorias[$i];
            }
            $data['categorias'] = $categorias;
        }

        if($request->largura){
            $data['dimensoes']['largura'] = $request->largura;
            $data['dimensoes']['altura'] = $request->altura;
            $data['dimensoes']['comprimento'] = $request->comprimento;
            $data['dimensoes']['peso'] = $request->peso;
        }
        // dd($data);
        // dd(json_encode($data));
        $image = null;

        if ($request->hasFile('image')) {

            if (!is_dir(public_path('uploads') . 'image_temp')) {
                mkdir(public_path('uploads') . 'image_temp', 0777, true);
            }

            $this->clearFolder(public_path('uploads'). '/image_temp');

            $file = $request->image;
            $ext = $file->getClientOriginalExtension();
            $file_name = Str::random(20) . ".$ext";

            $file->move(public_path('uploads'). '/image_temp', $file_name);
            $image = env('APP_URL') . '/uploads/image_temp/'.$file_name;
        }

        if($image != null){
            $data['imagens'] = [$image];
        }

        if(isset($request->variacao)){
            $variacoes = $this->montaVariaveis($request);
            if(sizeof($variacoes) > 0){
                $data['variacoes'] = $variacoes;
            }
            // dd($data['variacoes']);
        }

        curl_setopt($ch, CURLOPT_URL, $this->url . '/produtos/'.$item->vendizap_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        // dd($data);
        if(isset($data->erros)){
            // dd($data);
            session()->flash("flash_error", "Algo deu errado: " . $data->erros[0]);
            return redirect()->back();
        }
        $item->nome = $request->nome;
        $item->valor_unitario = __convert_value_bd($request->preco);
        $item->save();

        // estoque

        if($request->estoque){
            if(!isset($request->variacao)){
                $retorno = $this->ajustaEstoqueSimples($item, $request->estoque);
                if($retorno !== true){
                    session()->flash("flash_error", $retorno);
                }
            }
        }else{
            $retorno = $this->ajustaEstoqueVariavel($item, $request);
        }

        session()->flash("flash_success", "Produto atualizado!");
        return redirect()->route('vendizap-produtos.index');
    }

    private function montaVariaveis($request){
        $vars = [];
        $variacoes = [];
        for($i=0; $i<sizeof($request->variacao); $i++){
            if(!in_array($request->variacao_id[$i], $vars)){
                $vars[] = $request->variacao_id[$i];
            }
        }

        $v = $request->variacao_id[0];
        $vTemp = [];
        $indice = 0;
        // dd($request->all());
        if($request->variacao[0]){
            for($i=0; $i<sizeof($request->variacao); $i++){
                if($i == 0){
                    $variacoes[$indice]['id'] = $v;
                }

                if(isset($request->variacao_id[$i+1]) && $request->variacao_id[$i+1] != $request->variacao_id[$i]){
                    $variacoes[$indice]['variaveis'] = $vTemp;
                    $indice++;
                }else{
                    $vTemp[] = [
                        'id' => $request->variavel_id[$i],
                        'preco' => __convert_value_bd($request->valor_variacao[$i]) - __convert_value_bd($request->preco)
                    ];
                }

                if(!isset($request->variacao_id[$i+1])){
                    $variacoes[$indice]['variaveis'] = $vTemp;
                    $vTemp = [];
                }
            }
        }else{
            $cont = 0;
            $indice = 0;
            $preco = __convert_value_bd($request->preco);

            $variavel_diff = array_unique($request->variavel_id);
            $variavel_diff2 = array_unique($request->variavel_id2);
            // dd($variavel_diff);
            $vTemp = [];
            $v = $request->variacao_id[0];
            foreach($variavel_diff as $va){
                $vTemp[] = [
                    'id' => $va,
                    'preco' => __convert_value_bd($request->valor_variacao[$cont]) - $preco
                ];
                $cont++;
            }
            $variacoes[$indice]['id'] = $v;
            $variacoes[$indice]['variaveis'] = $vTemp;

            $vTemp = [];
            $v = $request->variacao_id2[0];
            foreach($variavel_diff2 as $va){
                $vTemp[] = [
                    'id' => $va,
                    'preco' => __convert_value_bd($request->valor_variacao[$cont]) - $preco
                ];
                $cont++;
            }
            $indice++;
            $variacoes[$indice]['id'] = $v;
            $variacoes[$indice]['variaveis'] = $vTemp;
            
            
        }
        // dd($variacoes);
        return $variacoes;
    }

    private function ajustaEstoqueSimples($item, $quantidade){
        $config = VendiZapConfig::where('empresa_id', request()->empresa_id)->first();

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
            'Content-Type: application/json'
        ];

        $data = [
            'quantidade' => $quantidade
        ];

        curl_setopt($ch, CURLOPT_URL, $this->url . '/estoque/'.$item->vendizap_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        // dd($data);


        $estoque = Estoque::where('produto_id', $item->id)->first();
        if($estoque != null){
            $estoque->quantidade = $quantidade;
            $estoque->save();
        }else{
            $this->utilEstoque->incrementaEstoque($item->id, $quantidade, null);
        }

        return $data;
    }

    private function ajustaEstoqueVariavel($item, $request){
        $config = VendiZapConfig::where('empresa_id', request()->empresa_id)->first();
        if(!isset($request->variavel_id)){
            return;
        }
        if(!isset($request->variavel_id2[0])){
            for($i=0; $i<sizeof($request->estoque_variacao); $i++){

                $quantidade = $request->estoque_variacao[$i];
                $ch = curl_init();
                $headers = [
                    "X-Auth-Id: " . $config->auth_id,
                    "X-Auth-Secret: " . $config->auth_secret,
                    'Content-Type: application/json'
                ];

                $data = [
                    'quantidade' => $quantidade,
                    'combinacao' => [
                        $request->variacao_id[$i] => $request->variavel_id[$i]
                    ]
                ];

                // dd($data);

                curl_setopt($ch, CURLOPT_URL, $this->url . '/estoque/'.$item->vendizap_id);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

                $data = json_decode(curl_exec($ch));
                curl_close($ch);
            // dd($data);
            }
        }else{
            for($i=0; $i<sizeof($request->estoque_variacao); $i++){

                $quantidade = $request->estoque_variacao[$i];
                $ch = curl_init();
                $headers = [
                    "X-Auth-Id: " . $config->auth_id,
                    "X-Auth-Secret: " . $config->auth_secret,
                    'Content-Type: application/json'
                ];

                $data = [
                    'quantidade' => $quantidade,
                    'combinacao' => [
                        $request->variacao_id[$i] => $request->variavel_id[$i],
                        $request->variacao_id2[$i] => $request->variavel_id2[$i],
                    ]
                ];

                // dd($data);

                curl_setopt($ch, CURLOPT_URL, $this->url . '/estoque/'.$item->vendizap_id);
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
    }

    private function clearFolder($destino){
        $files = glob($destino."/*");
        foreach($files as $file){ 
            if(is_file($file)) unlink($file); 
        }
    }

    // public function teste(){
    //     $config = VendiZapConfig::where('empresa_id', request()->empresa_id)->first();
    //     $ch = curl_init();
    //     $headers = [
    //         "X-Auth-Id: " . $config->auth_id,
    //         "X-Auth-Secret: " . $config->auth_secret,
    //         'Content-Type: application/json'
    //     ];

    //     $data = [
    //         [
    //             'quantidade' => 10,
    //             'combinacao' => [
    //                 '682bae5bbf6ecd557535314c' => '682bae6a0c3429105a3d6a83',
    //                 '682bae7ba5dca763eb1a30b8' => '682bae7ba5dca763eb1a30b9',
    //             ]
    //         ]
    //     ];

    //     // dd(json_encode($data));

    //     curl_setopt($ch, CURLOPT_URL, $this->url . '/estoque/682ddee69fd3bb78ae4a4fb3');
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HEADER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    //     $data = json_decode(curl_exec($ch));
    //     curl_close($ch);
    //     dd($data);
    // }

    private function sincronizaEstoque($data){
        $produto = Produto::where('vendizap_id', $data->id)->first();
        // dd($data);
        if($produto != null && isset($data->estoque)){
            if(sizeof($produto->variacoes) > 0){
                $this->sincronizaEstoqueVariavel($produto, $data);
            }else{

                if(isset($data->estoque->_produto)){
                    $qtd = $data->estoque->_produto;
                    $estoque = Estoque::where('produto_id', $produto->id)->first();
                    if($estoque != null){
                        $estoque->quantidade = $qtd;
                        $estoque->save();
                    }else{
                        $this->utilEstoque->incrementaEstoque($produto->id, $qtd, null);
                    }
                }
            }
        }
    }

    private function sincronizaEstoqueVariavel($produto, $data){
        // dd($data);
        if(sizeof($data->variacoes) == 1){
            foreach($data->variacoes[0]->variaveis as $variavel){
                $qtd = $this->getEstoqueDaVariacao($variavel->nome, $data, $data->variacoes[0]->nome);
                if($qtd > 0){
                    $produtoVariacao = ProdutoVariacao::where('produto_id', $produto->id)
                    ->where('descricao', $variavel->nome)->first();
                    if($produtoVariacao && isset($produtoVariacao->estoque)){
                        $estoque = $produtoVariacao->estoque;
                        $estoque->quantidade = $qtd;
                        $estoque->save();
                    }else{
                        $this->utilEstoque->incrementaEstoque($produto->id, $qtd, $produtoVariacao->id);
                    }
                }
            }
        }else{
            $combinacoes = $this->criaCombinacoes($data);
            foreach($combinacoes as $nomeCombinacao){

                $qtd = $this->getEstoqueCombinacaoMulti($nomeCombinacao, $data);
                if($qtd > 0){
                    $produtoVariacao = ProdutoVariacao::where('produto_id', $produto->id)
                    ->where('descricao', $nomeCombinacao)->first();
                    if($produtoVariacao && isset($produtoVariacao->estoque)){
                        $estoque = $produtoVariacao->estoque;
                        $estoque->quantidade = $qtd;
                        $estoque->save();
                    }else{
                        $this->utilEstoque->incrementaEstoque($produto->id, $qtd, $produtoVariacao->id);
                    }
                }
            }
        }
    }

    private function getEstoqueDaVariacao($variavelNome, $data, $variacaoNome){
        if($data->estoque){
            foreach($data->estoque as $estoque){
                foreach($estoque as $e){
                    if($e->combinacao->$variacaoNome == $variavelNome){
                        return $e->quantidade;
                    }
                }
            }
        }
        return 0;
    }

}
