<?php

namespace App\Http\Controllers;

use App\Models\DiaSemana;
use App\Models\Funcionamento;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FuncionamentoController extends Controller
{
    public function index(Request $request)
    {
        $funcionario_id = $request->funcionario_id;
        $data = Funcionamento::select('funcionamentos.*')
        ->where('funcionarios.empresa_id', $request->empresa_id)
        ->join('funcionarios', 'funcionarios.id', '=', 'funcionamentos.funcionario_id')
        ->when($funcionario_id, function ($q) use ($funcionario_id) {
            return $q->where('funcionarios.id', $funcionario_id);
        })
        ->paginate(__itensPagina());

        $funcionario = null;
        if($funcionario_id){
            $funcionario = Funcionario::findOrFail($funcionario_id);
        }

        return view('funcionamento.index', compact('data', 'funcionario'));
    }

    public function create()
    {
        $funcionamentos = Funcionamento::where('empresa_id', request()->empresa_id)
        ->select('funcionarios.*')
        ->join('funcionarios', 'funcionarios.id', '=', 'funcionamentos.funcionario_id')
        ->pluck('funcionarios.id')
        ->all();

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->whereNotIn('id', $funcionamentos)->get();

        return view('funcionamento.create', compact('funcionarios'));
    }

    public function edit($id)
    {
        $item = Funcionario::findOrfail($id);
        $funcionamento = Funcionamento::where('funcionario_id', $id)->get();

        return view('funcionamento.edit', compact('item', 'funcionamento'));
    }

    public function store(Request $request)
    {
        try {
            Funcionamento::where('funcionario_id', $request->funcionario_id)->delete();
            for($i=0; $i<sizeof($request->inicio); $i++){
                Funcionamento::create([
                    'funcionario_id' => $request->funcionario_id,
                    'inicio' => $request->inicio[$i],
                    'fim' => $request->fim[$i],
                    'dia_id' => $request->dia[$i],
                ]);
            }
            session()->flash('flash_success', 'Horário de funcionamento atribuído com sucesso!');
        } catch (\Exception $e) {
            echo $e->getLine();
            die;
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('funcionamentos.index');
    }

    public function update(Request $request, $id)
    {
        $item = Funcionario::findOrFail($id);

        try {
            Funcionamento::where('funcionario_id', $item->id)->delete();

            for($i=0; $i<sizeof($request->inicio); $i++){
                Funcionamento::create([
                    'funcionario_id' => $request->funcionario_id,
                    'inicio' => $request->inicio[$i],
                    'fim' => $request->fim[$i],
                    'dia_id' => $request->dia[$i],
                ]);
            }
            session()->flash('flash_success', 'Horário de funcionamento atribuído com sucesso!');
        } catch (\Exception $e) {
            // echo $e->getLine();
            // die;
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('funcionamentos.index');
    }

    public function destroy($id)
    {
        $item = Funcionario::findOrFail($id);

        try {
            Funcionamento::where('funcionario_id', $item->id)->delete();
            session()->flash('flash_success', 'Apagado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('funcionamentos.index');
    }
}
