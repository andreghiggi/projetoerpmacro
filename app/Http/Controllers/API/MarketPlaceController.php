<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaServico;

class MarketPlaceController extends Controller
{
    public function switchCategoria(Request $request){
        $item = CategoriaServico::findOrFail($request->id);
        $item->marketplace = !$item->marketplace;
        $item->save();
        return response()->json($item, 200);
    }
}
