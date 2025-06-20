<?php

namespace App\Http\Controllers\API\TokenSuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlanoEmpresa;
use App\Models\Empresa;
use App\Models\Plano;
use App\Models\FinanceiroPlano;

class GerenciarPlanoController extends Controller
{
    public function store(Request $request){

        try {
            $plano = Plano::find($request->plano_id);
            $empresa = Empresa::find($request->empresa_id);
            if($plano == null){
                return response()->json("Plano não encontrado!", 403);
            }
            if($empresa == null){
                return response()->json("Empresa não encontrada!", 403);
            }
            $intervalo = $plano->intervalo_dias;
            $exp = date('Y-m-d', strtotime(date('Y-m-d') . "+ $intervalo days"));

            $planoEmpresa = PlanoEmpresa::create([
                'empresa_id' => $empresa->id,
                'plano_id' => $plano->id,
                'data_expiracao' => $exp,
                'valor' => $plano->valor,
                'forma_pagamento' => $request->forma_pagamento ?? "Dinheiro"
            ]);

            FinanceiroPlano::create([
                'empresa_id' => $empresa->id,
                'plano_id' => $plano->id,
                'valor' => $plano->valor,
                'tipo_pagamento' => $request->forma_pagamento ?? "Dinheiro",
                'status_pagamento' => $request->status_pagamento ?? "recebido",
                'plano_empresa_id' => $planoEmpresa->id
            ]);

            return response()->json("Plano atribuído!", 200);

        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }
}
