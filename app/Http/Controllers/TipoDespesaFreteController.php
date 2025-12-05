<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDespesaFrete;

class TipoDespesaFreteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tipo_despesa_frete_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:tipo_despesa_frete_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tipo_despesa_frete_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:tipo_despesa_frete_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $data = TipoDespesaFrete::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->orderBy('nome')
        ->paginate(__itensPagina());

        return view('tipo_despesa_frete.index', compact('data'));
    }

    public function create()
    {
        return view('tipo_despesa_frete.create');
    }

    public function edit($id)
    {
        $item = TipoDespesaFrete::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('tipo_despesa_frete.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            
            TipoDespesaFrete::create($request->all());
            __createLog($request->empresa_id, 'Tipo de Despesa de Frete', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Tipo de Despesa de Frete', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('tipo-despesa-frete.index');
    }

    public function update(Request $request, $id)
    {
        $item = TipoDespesaFrete::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Tipo de Despesa de Frete', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Tipo de Despesa de Frete', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('tipo-despesa-frete.index');
    }

    public function destroy($id)
    {
        $item = TipoDespesaFrete::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Tipo de Despesa de Frete', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Tipo de Despesa de Frete', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->route('tipo-despesa-frete.index');
    }
}
