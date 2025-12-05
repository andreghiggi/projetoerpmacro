<?php
namespace App\Http\Controllers\API\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use App\Models\Adicional;
use App\Models\ProdutoAdicional;
use App\Models\TamanhoPizza;
use App\Models\MarketPlaceConfig;
use App\Models\ProdutoPizzaValor;
use App\Models\DestaqueMarketPlace;
use App\Models\CategoriaAdicional;

class ProdutoController extends Controller
{
    public function all(){
        $data = CategoriaProduto::
        where('empresa_id', request()->empresa_id)
        ->where('delivery', 1)
        ->where('status', 1)
        ->with('produtosDelivery')
        ->get();

        return response()->json($data, 200);
    }

    public function adicionais(Request $request){

        $data = Adicional::
        where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->get();

        return response()->json($data, 200);
    }

    public function find($id){
        $item = Produto::
        with(['categoria', 'pizzaValores'])
        ->findOrFail($id);

        $item->adicionais_ativos = ProdutoAdicional::
        where('produto_id', $item->id)
        ->where('adicionals.status', 1)
        ->join('adicionals', 'adicionals.id', '=', 'produto_adicionals.adicional_id')
        ->with('adicional')->get();

        return response()->json($item, 200);
    }

    public function carrossel(){
        $data = DestaqueMarketPlace::
        where('empresa_id', request()->empresa_id)
        ->where('status', 1)
        ->orderBy('status', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($data, 200);
    }

    public function produtoModal($hash){
        $item = Produto::where('hash_delivery', $hash)->first();

        $tamanho = TamanhoPizza::where('empresa_id', $item->empresa_id)
        ->orderBy('maximo_sabores', 'desc')->first();
        $maximo_sabores_pizza = 0;
        if($tamanho != null){
            $maximo_sabores_pizza = $tamanho->maximo_sabores;
        }

        $config = MarketPlaceConfig::where('empresa_id', $item->empresa_id)->first();

        $tamanhosPizza = TamanhoPizza::where('empresa_id', $item->empresa_id)
        ->where('status', 1)
        ->with('produtos')
        ->get();

        $tipoPizza = 0;
        if($item->categoria && $item->categoria->tipo_pizza){
            $tipoPizza = 1;
        }

        $categoriasAdicional = CategoriaAdicional::where('categoria_adicionals.empresa_id', $item->empresa_id)
        ->where('categoria_adicionals.status', 1)
        ->select('categoria_adicionals.*')
        ->join('adicionals', 'adicionals.categoria_id', '=', 'categoria_adicionals.id')
        ->join('produto_adicionals', 'produto_adicionals.adicional_id', '=', 'adicionals.id')
        ->where('produto_adicionals.produto_id', $item->id)
        ->groupBy('categoria_adicionals.id')
        ->get();

        return view('food.partials.produto_modal', 
            compact('item', 'config', 'maximo_sabores_pizza', 'tamanhosPizza', 'tipoPizza', 'categoriasAdicional'))->render();
    }

    public function pesquisaPizza(Request $request){
        $tamanho_id = $request->tamanho_id;
        $pizzaPrincipal = Produto::findOrFail($request->produto_id);

        $data = Produto::orderBy('produtos.nome', 'desc')
        ->select('produtos.*')
        ->where('produtos.empresa_id', $pizzaPrincipal->empresa_id)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('produtos.nome', 'LIKE', "%$request->pesquisa%");
        })
        ->where('produtos.status', 1)
        ->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
        ->where('categoria_produtos.tipo_pizza', 1)
        ->get();

        foreach($data as $i){
            $pizza = ProdutoPizzaValor::where('produto_id', $i->id)
            ->where('tamanho_id', $tamanho_id)->first();
            $i->valor_pizza = 0;
            if($pizza){
                $i->valor_pizza = $pizza->valor;
            }
        }

        return view('food.partials.pizzas_modal', 
            compact('pizzaPrincipal', 'data'))->render();
    }

    public function montaPizza(Request $request){
        $tamanho_id = $request->tamanho_id;

        $pizzaPrincipal = Produto::findOrFail($request->produto_id);
        $config = MarketPlaceConfig::where('empresa_id', $pizzaPrincipal->empresa_id)->first();
        $tamanho = TamanhoPizza::findOrFail($request->tamanho_id);
        $sabores = [];

        $sabores_selecionados = $request->sabores_selecionados ?? [];

        if(sizeof($sabores_selecionados)+1 > $tamanho->maximo_sabores){
            return response()->json("Selecione até $tamanho->maximo_sabores sabor(es)", 404);
        }
        array_push($sabores, $pizzaPrincipal);

        $pizza = ProdutoPizzaValor::where('produto_id', $pizzaPrincipal->id)
        ->where('tamanho_id', $tamanho_id)->first();
        $maiorValor = $pizza->valor;
        $soma = $pizza->valor;

        for($i=0; $i<sizeof($sabores_selecionados); $i++){
            $p = Produto::findOrFail($sabores_selecionados[$i]);
            array_push($sabores, $p);

            $pizza = ProdutoPizzaValor::where('produto_id', $p->id)
            ->where('tamanho_id', $tamanho_id)->first();

            if($pizza){
                $soma += $pizza->valor;

                if($pizza->valor > $maiorValor){
                    $maiorValor = $pizza->valor;
                }
            }
        }

        $valorDaPizza = $maiorValor;
        if($config->tipo_divisao_pizza == 'divide'){
            $valorDaPizza = $soma/sizeof($sabores);
        }


        $data = [
            'view' => view('food.partials.monta_sabores', compact('sabores'))->render(),
            'sabores' => $sabores,
            'valor_pizza' => $valorDaPizza
        ];
        return response()->json($data, 200);
    }

}
