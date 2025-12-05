<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoArmacao;

class TipoArmacaoController extends Controller
{
    public function index(Request $request)
    {   
        $data = TipoArmacao::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->paginate(__itensPagina());

        return view('tipo_armacao.index', compact('data'));
    }

    public function create()
    {
        return view('tipo_armacao.create');
    }

    public function edit($id)
    {
        $item = TipoArmacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('tipo_armacao.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            
            TipoArmacao::create($request->all());
            __createLog($request->empresa_id, 'Tipo de Armação', 'cadastrar', $request->nome);
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Tipo de Armação', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('tipo-armacao.index');
    }

    public function update(Request $request, $id)
    {
        $item = TipoArmacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Tipo de Armação', 'editar', $request->nome);
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Tipo de Armação', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível alterar o cadastro' . $e->getMessage());
        }
        return redirect()->route('tipo-armacao.index');
    }

    public function destroy($id)
    {
        $item = TipoArmacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Tipo de Armação', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Tipo de Armação', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }
}
