<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Plano;

class PlanoController extends Controller
{
    public function find(Request $request){
        $plano = Plano::findOrfail($request->plano_id);
        $empresa = Empresa::findOrfail($request->empresa_id);
        if($empresa){
            $financeiroPlano = $empresa->financeiroPlano;
            if(sizeof($financeiroPlano) == 0 && $plano->valor_implantacao > 0){
                $plano->valor += $plano->valor_implantacao;
            }
        }

        return response()->json($plano, 200);
    }

    public function findOne($id){
        $plano = Plano::findOrfail($id);
        return response()->json($plano, 200);
    }
}
