<?php

namespace App\Http\Controllers;

use App\Models\ConectaVendaConfig;
use App\Utils\ConectaVendaUtil;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\Estoque;
use App\Models\UnidadeMedida;
use App\Models\PadraoTributacaoProduto;
use App\Models\CategoriaProduto;
use App\Models\ProdutoLocalizacao;
use App\Models\ProdutoVariacao;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Utils\EstoqueUtil;

class ConectaVendaProdutoController extends Controller
{
    protected $util;
    protected $utilEstoque;

    public function __construct(ConectaVendaUtil $utilConectaVenda, EstoqueUtil $utilEstoque)
    {
        $this->utilConectaVenda = $utilConectaVenda;
        $this->utilEstoque = $utilEstoque;
    }

    public function index(Request $request)
    {
        $config = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();
        if(!$config){
            return redirect()->route('conecta-venda-config.index');
        }

        $data = Produto::where('empresa_id', request()->empresa_id)
            ->when(!empty($request->nome), function ($q) use ($request) {

                return $q->where('nome', 'LIKE', "%$request->nome%");
            })
            ->whereNotNull('conecta_venda_id')
            ->where('conecta_venda_status', true)
            ->paginate(env("PAGINACAO"));
            return view('conecta_venda_produtos.index', compact('data'));
    }

    public function create(Request $request){
        $config = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();

        if(!$config){
            return redirect()->route('conecta-venda-config.index');
        }

        $prod = Produto::where('empresa_id', $request->empresa_id)->first();
        return view('conecta_venda_produtos.create_produtos');
//        $response = $this->utilConectaVenda->createProduct($prod, $config);
//        dd($response);
    }

    private function validaProdutoCadastrado($ProdutoConecta, $empresa_id){

        $produto = Produto::where('empresa_id', $empresa_id)
            ->where('conecta_venda_id', $ProdutoConecta->id)
            ->first();
        if($produto != null){
            $this->atualizaProdutoConecta($ProdutoConecta, $produto);
            return true;
        }
        // dd($ProdutoConecta);

        $dataProdutoConecta = [
            'empresa_id' => $empresa_id,
            'nome' => $ProdutoConecta->produto_nome,
            'valor_venda' => $ProdutoConecta->preco,
            'conecta_venda_id' => $ProdutoConecta->id,
            'conecta_venda_valor' => $ProdutoConecta->preco,
            'mercado_livre_link' => $ProdutoConecta->permalink,
            'estoque' => $ProdutoConecta->available_quantity,
            'status' => $ProdutoConecta->status,
            'mercado_livre_categoria' => $ProdutoConecta->category_id
        ];

        if(sizeof($ProdutoConecta->variations) > 0){
            $variacoes = [];
            foreach($ProdutoConecta->variations as $v){
                $dataVariacao = [
                    '_id' => $v->id,
                    'quantidade' => $v->available_quantity,
                    'valor' => $v->price,
                    'nome' => $v->attribute_combinations[0]->name,
                    'valor_nome' => $v->attribute_combinations[0]->value_name
                ];
                array_push($variacoes, $dataVariacao);
            }

            $dataProdutoConecta['variacoes'] = $variacoes;
        }
        return $dataProdutoConecta;
    }

    private function atualizaProdutoConecta($ProdutoConecta, $produto){

        $produto->conecta_venda_status = $ProdutoConecta->status;
        $produto->conecta_venda_valor = $ProdutoConecta->preco;
        $produto->nome = $ProdutoConecta->produto_nome;
        $produto->save();
    }

}
