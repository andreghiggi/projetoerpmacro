<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceiroPlano;
use App\Models\Empresa;

class FinanceiroPlanoController extends Controller
{
    public function index(Request $request){

        $empresa = $request->get('empresa');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $status_pagamento = $request->get('status_pagamento');
        $data = FinanceiroPlano::when(!empty($empresa), function ($q) use ($empresa) {
            return $q->where('empresa_id', $empresa);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($status_pagamento), function ($q) use ($status_pagamento) {
            return $q->where('status_pagamento', $status_pagamento);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        $somaPendente = FinanceiroPlano::where('status_pagamento', 'pendente')
        ->sum('valor');
        $somaRecebido = FinanceiroPlano::where('status_pagamento', 'recebido')
        ->sum('valor');
        $somaCancelado = FinanceiroPlano::where('status_pagamento', 'cancelado')
        ->sum('valor');

        if($empresa){
            $empresa = Empresa::findOrFail($empresa);
        }
        return view('financeiro_plano.index', compact('data', 'somaPendente', 'somaRecebido', 'somaCancelado', 'empresa'));
    }

    public function edit($id){
        $item = FinanceiroPlano::findOrFail($id);
        return view('financeiro_plano.edit', compact('item'));
    }

    public function update(Request $request, $id){
        $item = FinanceiroPlano::findOrFail($id);
        try {

            $request->merge([
                'valor' => __convert_value_bd($request->valor),
            ]);

            $item->fill($request->except(['empresa_id']))->save();
            session()->flash("flash_success", "Registro alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('financeiro-plano.index');
    }

    public function destroy($id)
    {
        $item = FinanceiroPlano::findOrFail($id);

        try {
            $item->delete();
            session()->flash("flash_success", "Registro removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('financeiro-plano.index');
    }

}
