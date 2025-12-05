<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetaResultado;
use App\Models\Funcionario;

class MetaResultadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:metas_view', ['only' => ['create', 'store']]);
        $this->middleware('permission:metas_create', ['only' => ['edit', 'update']]);
        $this->middleware('permission:metas_edit', ['only' => ['show', 'index']]);
        $this->middleware('permission:metas_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {   
        $data = MetaResultado::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->funcionario_id), function ($q) use ($request) {
            return $q->where('funcionario_id', $request->funcionario_id);
        })
        ->paginate(__itensPagina());

        $funcionario = Funcionario::find($request->funcionario_id);
        return view('metas_resultado.index', compact('data', 'funcionario'));
    }

    public function create()
    {
        return view('metas_resultado.create');
    }

    public function edit($id)
    {
        $item = MetaResultado::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('metas_resultado.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            
            MetaResultado::create($request->all());
            __createLog($request->empresa_id, 'Meta', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Meta', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('metas.index');
    }

    public function update(Request $request, $id)
    {
        $item = MetaResultado::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Meta', 'editar', $item->funcionario->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Meta', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('metas.index');
    }

    public function destroy($id)
    {
        $item = MetaResultado::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->funcionario->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Meta', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Meta', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }
}
