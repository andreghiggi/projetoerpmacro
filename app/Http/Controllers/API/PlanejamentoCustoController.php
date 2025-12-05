<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servico;
use App\Models\Produto;
use App\Models\ItemCotacao;

class PlanejamentoCustoController extends Controller
{
    public function findServico(Request $request){
        $item = Servico::findOrFail($request->servico_id);

        return response()->json($item, 200);
    }

    public function findProduto(Request $request){
        $item = Produto::findOrFail($request->produto_id);

        $itemCotacao = ItemCotacao::where('produto_id', $item->id)
        ->where('valor_unitario', '>', 0)
        ->orderBy('id', 'desc')->first();
        $item->valor = 0;
        if($itemCotacao){
            $item->valor = $itemCotacao->valor_unitario ?? 0;
        }
        return response()->json($item, 200);
    }
}
