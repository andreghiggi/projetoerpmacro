<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaProduto;

class CategoriaProdutoController extends Controller
{
    public function categoriaParaSubcategoria(Request $request){
        $data = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->pesquisa%");
        })
        ->where('status', 1)
        ->where('categoria_id', null)
        ->get();
        return response()->json($data, 200);
    }


    public function subcategorias(Request $request){
        $data = CategoriaProduto::
        when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->pesquisa%");
        })
        ->where('categoria_id', $request->categoria_id)
        ->get();

        return response()->json($data, 200);
    }
}
