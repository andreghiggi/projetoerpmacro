<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaNuvemShop;

class NuvemShopController extends Controller
{
    public function getCategorias(Request $request){
        $data = CategoriaNuvemShop::
        where('nome', 'like', "%$request->pesquisa%")
        ->where('empresa_id', $request->empresa_id)
        ->get();

        return response()->json($data, 200);
    }
}
