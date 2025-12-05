<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VariacaoModelo;
use App\Models\Produto;
use App\Models\ProdutoVariacao;

class VariacaoController extends Controller
{
    public function modelo(Request $request){
        $item = VariacaoModelo::findOrFail($request->variacao_modelo_id);
        return view('variacao_modelo.table', compact('item'));
    }

    public function modeloVariacoes(Request $request){
        $item = VariacaoModelo::findOrFail($request->variacao_modelo_id);
        $sub = VariacaoModelo::findOrFail($request->sub_variacao_modelo_id);
        // die;
        return view('variacao_modelo.table', compact('item', 'sub'));
    }

    public function find(Request $request){
        $item = Produto::findOrFail($request->produto_id);
        return view('variacao_modelo.change', compact('item'));
    }

    public function findById(Request $request){
        $item = ProdutoVariacao::findOrFail($request->codigo_variacao);
        return response()->json($item, 200);
    }
}
