<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use App\Models\Adicional;
use App\Models\ProdutoAdicional;
use App\Models\ProdutoIngrediente;
use App\Models\TamanhoPizza;
use App\Models\ProdutoPizzaValor;
class ProdutoDeliveryController extends Controller
{
    public function categorias(Request $request){
        $nome = $request->nome;
        $data = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->when(!empty($nome), function ($q) use ($nome) {
            return $q->where('nome', 'LIKE', "%$nome%");
        })
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->where('categoria_id', null)
        ->paginate(__itensPagina());
        
        return view('delivery.categorias.index', compact('data'));
    }

    public function index(Request $request){
        $status = $request->status;
        $nome = $request->nome;

        $data = Produto::where('empresa_id', $request->empresa_id)
        ->when(!empty($nome), function ($q) use ($nome) {
            return $q->where('nome', 'LIKE', "%$nome%");
        })
        ->when($status != '', function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->where('delivery', 1)
        ->paginate(__itensPagina());

        return view('delivery.produtos.index', compact('data'));

    }

}
