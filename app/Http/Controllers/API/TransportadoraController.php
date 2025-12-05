<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transportadora;
use Illuminate\Http\Request;

class TransportadoraController extends Controller
{
    public function find($id){
        $item = Transportadora::with('cidade')->findOrFail($id);
        return response()->json($item, 200);
    }

    public function pesquisa(Request $request)
    {
        $data = Transportadora::orderBy('razao_social', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where(function($q) use ($request){
            $q->where('razao_social', 'like', "%$request->pesquisa%");
        })
        ->get();
        return response()->json($data, 200);
    }
}
