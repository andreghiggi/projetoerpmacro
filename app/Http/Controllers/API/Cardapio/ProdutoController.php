<?php

namespace App\Http\Controllers\API\Cardapio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\TamanhoPizza;
use App\Models\ConfiguracaoCardapio;
use App\Models\CategoriaAdicional;
use App\Models\ItemCarrinhoCardapio;

class ProdutoController extends Controller
{
    public function produtoModal($hash){
        $item = Produto::where('hash_delivery', $hash)->first();

        $tamanho = TamanhoPizza::where('empresa_id', $item->empresa_id)
        ->orderBy('maximo_sabores', 'desc')->first();
        $maximo_sabores_pizza = 0;
        if($tamanho != null){
            $maximo_sabores_pizza = $tamanho->maximo_sabores;
        }

        $config = ConfiguracaoCardapio::where('empresa_id', $item->empresa_id)->first();

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

        $link = request()->link;

        return view('qr_code_cardapio.partials.produto_modal', 
            compact('item', 'config', 'maximo_sabores_pizza', 'tamanhosPizza', 'tipoPizza', 'categoriasAdicional', 'link'))->render();
    }

}
