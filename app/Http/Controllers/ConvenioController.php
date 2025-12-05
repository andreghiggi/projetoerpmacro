<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Convenio;

class ConvenioController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:convenio_view', ['only' => ['create', 'store']]);
        $this->middleware('permission:convenio_create', ['only' => ['edit', 'update']]);
        $this->middleware('permission:convenio_edit', ['only' => ['show', 'index']]);
        $this->middleware('permission:convenio_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {   
        $data = Convenio::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->paginate(__itensPagina());

        return view('convenios.index', compact('data'));
    }

    public function create()
    {
        return view('convenios.create');
    }

    public function edit($id)
    {
        $item = Convenio::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('convenios.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            
            Convenio::create($request->all());
            __createLog($request->empresa_id, 'Convênio', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Convênio', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('convenios.index');
    }

    public function update(Request $request, $id)
    {
        $item = Convenio::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Convênio', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Convênio', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('convenios.index');
    }

    public function destroy($id)
    {
        $item = Convenio::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Convênio', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Convênio', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }

}
