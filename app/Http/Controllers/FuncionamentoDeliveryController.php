<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuncionamentoDelivery;

class FuncionamentoDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $funcionario_id = $request->funcionario_id;
        $data = FuncionamentoDelivery::
        where('empresa_id', $request->empresa_id)
        ->get();

        return view('funcionamento_delivery.index', compact('data'));
    }

    public function create()
    {
        $funcionamentos = FuncionamentoDelivery::
        where('empresa_id', request()->empresa_id)
        ->pluck('dia')->all();

        $temp = FuncionamentoDelivery::getDiaSemana();
        $dias = [];
        foreach($temp as $key => $t){
            if(!in_array($key, $funcionamentos)){
                $dias[$key] = $t;
            }
        }
        return view('funcionamento_delivery.create', compact('dias'));
    }

    public function edit($id)
    {
        $item = FuncionamentoDelivery::findOrfail($id);
        return view('funcionamento_delivery.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            FuncionamentoDelivery::create($request->all());
            session()->flash('flash_success', 'Funcionamento atribuído com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('funcionamento-delivery.index');
    }

    public function update(Request $request, $id)
    {
        $item = FuncionamentoDelivery::findOrFail($id);

        try {
            $item->fill($request->all())->save();
            session()->flash('flash_success', 'Funcionamento atualizado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('funcionamento-delivery.index');
    }

    public function destroy($id)
    {
        $item = FuncionamentoDelivery::findOrFail($id);

        try {
            $item->delete();
            session()->flash('flash_success', 'Apagado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('funcionamento-delivery.index');
    }
}
