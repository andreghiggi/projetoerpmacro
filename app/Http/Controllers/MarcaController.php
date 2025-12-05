<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:marcas_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:marcas_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:marcas_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:marcas_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Marca::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());
        return view('marcas.index', compact('data'));
    }

    public function create()
    {
        return view('marcas.create');
    }

    public function store(Request $request)
    {
        try {
            Marca::create($request->all());
            __createLog($request->empresa_id, 'Marca', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Marca', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('marcas.index');
    }

    public function edit($id)
    {
        $item = Marca::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('marcas.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Marca::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Marca', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Marca', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('marcas.index');
    }

    public function destroy($id)
    {
        $item = Marca::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Marca', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Removido com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Marca', 'erro', $e->getMessage());
            session()->flash('flash_warning', 'Marca esta sendo usada em algum produto');
        }
        return redirect()->back();
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = Marca::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->nome;
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Marca', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Marca', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('marcas.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->back();
    }
}
