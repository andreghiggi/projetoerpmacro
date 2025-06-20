<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Funcionario;
use App\Models\DiaSemana;

class FuncionarioController extends Controller
{
    public function pesquisa(Request $request)
    {
        $data = Funcionario::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('nome', 'like', "%$request->pesquisa%")
        ->where('status', 1)
        ->get();
        return response()->json($data, 200);
    }

    public function find(Request $request)
    {
        $item = Funcionario::findOrFail($request->id);
        return response()->json($item, 200);
    }

    public function validaAtendimento(Request $request){
        $item = DiaSemana::where('funcionario_id', $request->funcionario_id)
        ->first();
        return response()->json($item != null ? 1 : 0, 200);
    }
}
