<?php

namespace App\Http\Controllers;

use App\Models\EventoSalario;
use Illuminate\Http\Request;

class EventoFuncionarioController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:apuracao_mensal_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:apuracao_mensal_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:apuracao_mensal_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:apuracao_mensal_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = EventoSalario::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('nome', 'LIKE', "%$request->nome%");
            });
        })
        ->paginate(__itensPagina());
        return view('eventos.index', compact('data'));
    }

    public function create()
    {
        return view('eventos.create');
    }

    public function store(Request $request)
    {
        try {
            EventoSalario::create($request->all());
            __createLog($request->empresa_id, 'Evento Salário', 'cadastrar', $request->nome);
            session()->flash("flash_success", "Evento cadastrado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Evento Salário', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('evento-funcionarios.index');
    }

    public function edit($id)
    {
        $item = EventoSalario::findOrFail($id);
        return view('eventos.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = EventoSalario::findOrFail($id);
        try {
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Evento Salário', 'editar', $request->nome);
            session()->flash("flash_success", "Evento alterado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Evento Salário', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('evento-funcionarios.index');
    }

    public function destroy($id)
    {
        $item = EventoSalario::findOrFail($id);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Evento Salário', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Evento Deletado!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Evento Salário', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('evento-funcionarios.index');
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = EventoSalario::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->nome;
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Evento Salário', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Evento Salário', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->back();
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->back();
    }
}