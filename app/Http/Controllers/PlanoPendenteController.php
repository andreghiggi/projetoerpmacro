<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanoPendente;
use App\Models\PlanoEmpresa;
use App\Models\FinanceiroPlano;

class PlanoPendenteController extends Controller
{
    public function index(Request $request){
        $data = PlanoPendente::orderBy('id', 'desc')
        ->where('status', 0)
        ->paginate(__itensPagina());
        return view('planos_pendentes.index', compact('data'));
    }

    public function edit($id){
        $item = PlanoPendente::findOrfail($id);
        return view('planos_pendentes.edit', compact('item'));
    }

    public function update(Request $request, $id){
        $item = PlanoPendente::findOrfail($id);
        $intervalo = $item->plano->intervalo_dias;

        $exp = date('Y-m-d', strtotime("+$intervalo days",strtotime( 
          date('Y-m-d'))));
        try{
            $planoEmpresa = PlanoEmpresa::create([
                'empresa_id' => $item->empresa_id,
                'plano_id' => $item->plano_id,
                'data_expiracao' => $exp,
                'valor' => $item->valor,
                'forma_pagamento' => $request->forma_pagamento
            ]);

            FinanceiroPlano::create([
                'empresa_id' => $item->empresa_id,
                'plano_id' => $item->plano_id,
                'valor' => $item->valor,
                'tipo_pagamento' => $request->forma_pagamento,
                'status_pagamento' => $request->status_pagamento,
                'plano_empresa_id' => $planoEmpresa->id
            ]);

            $item->status = 1;
            $item->save();
            session()->flash("flash_success", "Plano liberado!");
            return redirect()->route('planos-pendentes.index');
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $item = PlanoPendente::findOrFail($id);
        try {
            $item->delete();
            session()->flash('flash_success', 'Removido com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->back();
    }
}
