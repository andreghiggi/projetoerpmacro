<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\IfoodUtil;
use App\Models\IfoodConfig;
use App\Models\Produto;
use App\Models\ProdutoIfood;
use App\Models\CategoriaProdutoIfood;
use App\Models\Localizacao;
use App\Models\ProdutoLocalizacao;
use App\Models\PadraoTributacaoProduto;

class IfoodProdutoController extends Controller
{

    protected $util;

    public function __construct(IfoodUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $config = IfoodConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        if($config == null){
            session()->flash("flash_error", "Configure o App");
            return redirect()->route('ifood-config.index');
        }

        if($config->catalogId == ""){
            session()->flash("flash_error", "Defina o catalogo!");
            return redirect()->route('ifood-catalogos.index');
        }

        $this->getProdutosIfood($config);

        $data = ProdutoIfood::where('empresa_id', $request->empresa_id)
        ->where('ifood_id', '!=', null)
        ->paginate(__itensPagina());

        return view('ifood_produtos.index', compact('data'));

    }

    private function getProdutosIfood($config){

        $result = $this->util->getCategories($config);

        if(isset($result->message)){
            if($result->message == "token expired"){
                $result = $this->util->oAuthToken($config);
                if(isset($result['success']) && $result['success'] == 1){
                    return redirect()->route('ifood-produtos.index');
                }else{
                    return redirect()->route('ifood-config.index');
                }
            }
        }

        foreach($result as $c){
            $categoria = CategoriaProdutoIfood::updateOrCreate([
                'empresa_id' => $config->empresa_id,
                'ifood_id' => $c->id,
                'nome' => $c->name,
                'status' => $c->status

            ]);

            if(isset($c->items)){
                foreach($c->items as $produto){

                    $estoque = $this->util->getStock($config, $produto->productId);

                    $dataProduto = [
                        'empresa_id' => $config->empresa_id,
                        'ifood_id' => $produto->productId,
                        'ifood_id_aux' => $produto->id,
                        'nome' => $produto->name,
                        'imagem' => $produto->imagePath,
                        'serving' => $produto->serving,
                        'status' => $produto->status,
                        'estoque' => isset($estoque->amount) ? $estoque->amount : 0,
                        'descricao' => $produto->description ?? '',
                        'valor' => $produto->price->value,
                        'categoria_produto_ifood_id' => $categoria->id
                    ];

                    $item = ProdutoIfood::where('empresa_id', $config->empresa_id)
                    ->where('ifood_id', $produto->productId)->first();

                    if($item == null){
                        $this->cadastrarProduto($dataProduto);
                    }
                }
            }
        }
    }

    private function cadastrarProduto($data){
        $padraoTributacaoProduto = PadraoTributacaoProduto::where('padrao', 1)
        ->where('empresa_id', $data['empresa_id'])->first();
        $dataProduto = [
            'ifood_id' => $data['ifood_id'],
            'empresa_id' => $data['empresa_id'],
            'nome' => $data['nome'],
            'valor_unitario' => $data['valor'],
        ];

        if($padraoTributacaoProduto){
            $dataProduto['perc_icms'] = $padraoTributacaoProduto->perc_icms;
            $dataProduto['perc_pis'] = $padraoTributacaoProduto->perc_pis;
            $dataProduto['perc_cofins'] = $padraoTributacaoProduto->perc_cofins;
            $dataProduto['perc_ipi'] = $padraoTributacaoProduto->perc_ipi;
            $dataProduto['cst_csosn'] = $padraoTributacaoProduto->cst_csosn;
            $dataProduto['cst_pis'] = $padraoTributacaoProduto->cst_pis;
            $dataProduto['cst_cofins'] = $padraoTributacaoProduto->cst_cofins;
            $dataProduto['cst_ipi'] = $padraoTributacaoProduto->cst_ipi;
            $dataProduto['perc_red_bc'] = $padraoTributacaoProduto->perc_red_bc;
            $dataProduto['cEnq'] = $padraoTributacaoProduto->cEnq;
            $dataProduto['pST'] = $padraoTributacaoProduto->pST;
            $dataProduto['cfop_estadual'] = $padraoTributacaoProduto->cfop_estadual;
            $dataProduto['cfop_outro_estado'] = $padraoTributacaoProduto->cfop_outro_estado;
            $dataProduto['cest'] = $padraoTributacaoProduto->cest;
            $dataProduto['ncm'] = $padraoTributacaoProduto->ncm;
            $dataProduto['cfop_entrada_estadual'] = $padraoTributacaoProduto->cfop_entrada_estadual;
            $dataProduto['cfop_entrada_outro_estado'] = $padraoTributacaoProduto->cfop_entrada_outro_estado;
        }

        $produto = Produto::create($dataProduto);

        $locais = Localizacao::where('empresa_id', $data['empresa_id'])->get();
        foreach($locais as $l){
            ProdutoLocalizacao::updateOrCreate([
                'produto_id' => $produto->id, 
                'localizacao_id' => $l->id
            ]);
        }

        $data['produto_id'] = $produto->id;
        ProdutoIfood::create($data);
    }

    public function edit($id){
        $item = ProdutoIfood::findOrFail($id);

    }
    
}
