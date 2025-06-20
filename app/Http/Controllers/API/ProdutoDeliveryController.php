<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaProduto;

class ProdutoDeliveryController extends Controller
{
    public function switchCategoria(Request $request){
        $item = CategoriaProduto::findOrFail($request->id);
        $item->delivery = !$item->delivery;
        $item->save();
        return response()->json($item, 200);
    }
}
