<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiaSemana;

class FuncionamentoController extends Controller
{
    public function diasDoFuncionario(Request $request){
        $funcionario_id = $request->funcionario_id;

        $data = DiaSemana::where('funcionario_id', $funcionario_id)->first();
        $temp = json_decode($data->dia);
        $dias = [];
        foreach ($temp as $d) {
            $dias[$d] = DiaSemana::getDiaStr($d);
        }
        return view('funcionamento.partials.line_day', compact('dias', 'funcionario_id'));
    }
}
