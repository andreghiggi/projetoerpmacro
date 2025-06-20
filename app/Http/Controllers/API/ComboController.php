<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;

class ComboController extends Controller
{
    public function modelo(Request $request){
        $item = Produto::findOrFail($request->produto_id);
        return view('combo_modelo.table', compact('item'));
    }
}
