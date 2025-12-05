<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratorio;

class LaboratorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:laboratorio_view', ['only' => ['create', 'store']]);
        $this->middleware('permission:laboratorio_create', ['only' => ['edit', 'update']]);
        $this->middleware('permission:laboratorio_edit', ['only' => ['show', 'index']]);
        $this->middleware('permission:laboratorio_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {   
        $data = Laboratorio::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->paginate(__itensPagina());

        return view('laboratorios.index', compact('data'));
    }

    public function create()
    {
        return view('laboratorios.create');
    }

    public function edit($id)
    {
        $item = Laboratorio::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('laboratorios.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            Laboratorio::create($request->all());
            __createLog($request->empresa_id, 'Laboratório', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Laboratório', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('laboratorios.index');
    }

    public function update(Request $request, $id)
    {
        $item = Laboratorio::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Laboratório', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Laboratório', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('laboratorios.index');
    }

    public function destroy($id)
    {
        $item = Laboratorio::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Laboratório', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Laboratório', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }
}
