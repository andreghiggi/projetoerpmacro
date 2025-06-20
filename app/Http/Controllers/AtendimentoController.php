<?php

namespace App\Http\Controllers;

use App\Models\DiaSemana;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtendimentoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:atendimentos_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:atendimentos_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:atendimentos_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:atendimentos_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){

        $funcionario_id = $request->get('funcionario_id');
        
        $data = DiaSemana::where('empresa_id', $request->empresa_id)
        ->when(!empty($funcionario_id), function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->get();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        return view('atendimentos.index', compact('data', 'funcionarios'));
    }

    public function create()
    {
        $dias = DiaSemana::where('empresa_id', request()->empresa_id)
        ->pluck('funcionario_id')->all();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)
        ->whereNotIn('id', $dias)->get();

        $dias = DiaSemana::getDias();

        return view('atendimentos.create', compact('dias', 'funcionarios'));
        
    }

    public function edit($id)
    {
        $item = DiaSemana::findOrfail($id);
        $diasEdit = json_decode($item->dia);
        $dias = DiaSemana::getDias();

        return view('atendimentos.edit', compact('dias', 'item', 'diasEdit'));
        
    }

    public function store(Request $request)
    {
        try {

            if (!isset($request->dia)) {
                $request->merge([
                    'dia' => '[]',
                ]);
            } else {
                $request->merge([
                    'dia' => json_encode($request->dia),
                ]);
            }
            DiaSemana::create($request->all());
            session()->flash('flash_success', 'Cadastro concluído com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado:' . $e->getMessage());
        }
        return redirect()->route('atendimentos.index');
    }

    public function update(Request $request, $id)
    {
        $item = DiaSemana::findOrfail($id);
        try {

            if (!isset($request->dia)) {
                $request->merge([
                    'dia' => '[]'
                ]);
            } else {
                $request->merge([
                    'dia' => json_encode($request->dia)
                ]);
            }
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Cadastro Atualizado");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('atendimentos.index');
    }

    public function destroy($id)
    {
        $item = DiaSemana::findOrFail($id);
        try {
            $item->delete();
            session()->flash('flash_success', 'Deletado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_errors', 'Algo deu errado' . $e->getMessage());
        }
        return redirect()->route('atendimentos.index');
    }
}
