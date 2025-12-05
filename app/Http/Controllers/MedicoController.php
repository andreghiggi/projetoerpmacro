<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medico;

class MedicoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:medico_view', ['only' => ['create', 'store']]);
        $this->middleware('permission:medico_create', ['only' => ['edit', 'update']]);
        $this->middleware('permission:medico_edit', ['only' => ['show', 'index']]);
        $this->middleware('permission:medico_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {   
        $data = Medico::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->paginate(__itensPagina());

        return view('medicos.index', compact('data'));
    }

    public function create()
    {
        return view('medicos.create');
    }

    public function edit($id)
    {
        $item = Medico::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('medicos.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            Medico::create($request->all());
            __createLog($request->empresa_id, 'Médico', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Médico', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('medicos.index');
    }

    public function update(Request $request, $id)
    {
        $item = Medico::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Médico', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Médico', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('medicos.index');
    }

    public function destroy($id)
    {
        $item = Medico::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Médico', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Médico', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }

}
