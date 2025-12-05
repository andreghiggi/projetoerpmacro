<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanoEmpresa;
use App\Models\Empresa;
use App\Models\Plano;
use App\Models\FinanceiroPlano;

class GerenciarPlanoController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $request->empresa;
        $planos = Plano::orderBy('nome', 'asc')->get();
        $data = PlanoEmpresa::orderBy('id', 'desc')
        ->when(!empty($empresa), function ($query) use ($empresa) {
            return $query->where('empresa_id', $empresa);
        })
        ->paginate(__itensPagina());

        if($empresa){
            $empresa = Empresa::findOrFail($empresa);
        }
        return view('gerencia_planos.index', compact('data', 'planos', 'empresa'));
    }

    public function store(Request $request)
    {
        try {
            $plano = Plano::findOrfail($request->plano_id);
            $intervalo = $plano->intervalo_dias;
            $exp = date('Y-m-d', strtotime(date('Y-m-d') . "+ $intervalo days"));

            $planoEmpresa = PlanoEmpresa::create([
                'empresa_id' => $request->empresa_atribuir,
                'plano_id' => $request->plano_id,
                'data_expiracao' => $exp,
                'valor' => __convert_value_bd($request->valor),
                'forma_pagamento' => $request->forma_pagamento
            ]);

            FinanceiroPlano::create([
                'empresa_id' => $request->empresa_atribuir,
                'plano_id' => $request->plano_id,
                'valor' => __convert_value_bd($request->valor),
                'tipo_pagamento' => $request->forma_pagamento,
                'status_pagamento' => $request->status_pagamento,
                'plano_empresa_id' => $planoEmpresa->id
            ]);
            session()->flash("flash_success", "Plano atribuído!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        $item = PlanoEmpresa::findOrFail($id);
        try {
            $financeiro = FinanceiroPlano::where('plano_empresa_id', $item->id)->first();
            if($financeiro){
                $financeiro->delete();
            }
            $item->delete();
            session()->flash("flash_success", "Apagado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->back();
    }

    public function edit($id){
        $item = PlanoEmpresa::findOrFail($id);
        return view('gerencia_planos.edit', compact('item'));
    }

    public function update(Request $request, $id){
        $item = PlanoEmpresa::findOrFail($id);
        try{
            $item->data_expiracao = $request->data_expiracao;
            $item->save();
            session()->flash("flash_success", "Data alterada!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('gerenciar-planos.index');

    }

}
