<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnidadeMedida;

class UnidadeMedidaController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:unidade_medida_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:unidade_medida_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:unidade_medida_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:unidade_medida_delete', ['only' => ['destroy']]);
    }

    private function insertUnidadesMedida($empresa_id){

        $unidade = UnidadeMedida::where('empresa_id', $empresa_id)->first();
        if($unidade == null){
            foreach(UnidadeMedida::unidadesMedidaPadrao() as $u){
                UnidadeMedida::create([
                    'empresa_id' => $empresa_id,
                    'status' => 1,
                    'nome' => $u
                ]);
            }
        }
    }

    public function index(Request $request)
    {
        $this->insertUnidadesMedida(request()->empresa_id);
        $data = UnidadeMedida::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->paginate(__itensPagina());

        return view('unidades_medida.index', compact('data'));
    }

    public function create()
    {
        return view('unidades_medida.create');
    }

    public function edit($id)
    {
        $item = UnidadeMedida::findOrFail($id);
        return view('unidades_medida.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            UnidadeMedida::create($request->all());
            __createLog($request->empresa_id, 'Unidade de Medida', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Unidade de Medida', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('unidades-medida.index');
    }

    public function update(Request $request, $id)
    {
        $item = UnidadeMedida::findOrFail($id);
        try {

            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Unidade de Medida', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Unidade de Medida', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('unidades-medida.index');
    }

    public function destroy($id)
    {
        $item = UnidadeMedida::findOrFail($id);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Unidade de Medida', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Unidade de Medida', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }
}
