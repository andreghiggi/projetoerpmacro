<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Veiculo;

class VeiculoController extends Controller
{
    public function pesquisa(Request $request)
    {
        $data = Veiculo::where('empresa_id', $request->empresa_id)
        ->where(function($q) use ($request) {
            $q->where('placa', 'LIKE', "%$request->pesquisa%")->orWhere('modelo', 'LIKE', "%$request->pesquisa%");
        })
        ->where('status', 1)
        ->get();

        return response()->json($data, 200);
    }

    public function store(Request $request){
        
        $item = Veiculo::create($request->all());
        return response()->json($item, 200);
    }
}
