<?php

namespace App\Http\Controllers;

use App\Models\DiaSemana;
use App\Models\Interrupcoes;
use App\Models\Funcionario;
use App\Models\MotivoInterrupcao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterrupcoesController extends Controller
{
    public function index(Request $request)
    {   
        $funcionario_id = $request->funcionario_id;
        $data = Interrupcoes::where('empresa_id', $request->empresa_id)
        ->when($funcionario_id, function ($q) use ($funcionario_id) {
            return $q->where('funcionario_id', $funcionario_id);
        })
        ->paginate(__itensPagina());

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->orderBy('nome', 'asc')
        ->get();

        return view('interrupcoes.index', compact('data', 'funcionarios'));
    }

    public function create()
    {
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->orderBy('nome', 'asc')
        ->get();

        $dias = DiaSemana::where('empresa_id', request()->empresa_id)
        ->pluck('funcionario_id')->all();

        $motivos = MotivoInterrupcao::where('empresa_id', request()->empresa_id)
        ->orderBy('motivo', 'asc')
        ->get();
        return view('interrupcoes.create', compact('funcionarios', 'dias', 'motivos'));
    }

    public function edit($id)
    {
        $item = Interrupcoes::findOrFail($id);
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->orderBy('nome', 'asc')
        ->get();

        $dias = DiaSemana::where('empresa_id', request()->empresa_id)
        ->pluck('funcionario_id')->all();

        $motivos = MotivoInterrupcao::where('empresa_id', request()->empresa_id)
        ->orderBy('motivo', 'asc')
        ->get();
        return view('interrupcoes.edit', compact('funcionarios', 'dias', 'motivos', 'item'));
    }

    public function register($id)
    {
        $item = DiaSemana::findOrFail($id);
        return view('interrupcoes.register', compact('item'));
    }

    public function store(Request $request)
    {

        try {
            Interrupcoes::create([
                'funcionario_id' => $request->funcionario_id,
                'inicio' => $request->inicio,
                'fim' => $request->fim,
                'dia_id' => $request->dia,
                'motivo' => $request->motivo,
                'empresa_id' => $request->empresa_id,
                'status' => $request->status
            ]);
            session()->flash('flash_success', 'Interrupção atribuída com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('interrupcoes.index');
    }

    public function update(Request $request, $id)
    {

        try {
            $item = Interrupcoes::findOrFail($id);

            $item->update([
                'funcionario_id' => $request->funcionario_id,
                'inicio' => $request->inicio,
                'fim' => $request->fim,
                'dia_id' => $request->dia,
                'motivo' => $request->motivo,
                'status' => $request->status
            ]);
            session()->flash('flash_success', 'Interrupção editada com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('interrupcoes.index');
    }

    public function destroy($id)
    {
        $item = Interrupcoes::findOrFail($id);
        try{
            $item->delete();
            session()->flash('flash_success', 'Deletado com sucesso!');
        }catch(\Exception $e){
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('interrupcoes.index');
    }
}
