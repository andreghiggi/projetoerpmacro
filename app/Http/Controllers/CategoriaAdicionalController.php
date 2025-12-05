<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaAdicional;

class CategoriaAdicionalController extends Controller
{

    public function index(Request $request)
    {

        $data = CategoriaAdicional::where('empresa_id', request()->empresa_id)
        ->paginate(__itensPagina());

        return view('categoria_adicional.index', compact('data'));
    }

    public function create()
    {
        return view('categoria_adicional.create');
    }

    public function edit($id)
    {
        $item = CategoriaAdicional::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('categoria_adicional.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'minimo_escolha' => $request->minimo_escolha ?? 0,
                'maximo_escolha' => $request->maximo_escolha ?? 0,
            ]);
            CategoriaAdicional::create($request->all());
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {

            session()->flash('flash_error', 'Não foi possível concluir o cadastro: ' . $e->getMessage());
        }
        return redirect()->route('categoria-adicional.index');
    }

    public function update(Request $request, $id)
    {
        $item = CategoriaAdicional::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $request->merge([
                'minimo_escolha' => $request->minimo_escolha ?? 0,
                'maximo_escolha' => $request->maximo_escolha ?? 0,
            ]);
            $item->fill($request->all())->save();
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível alterar o cadastro: ' . $e->getMessage());
        }
        return redirect()->route('categoria-adicional.index');
    }

    public function destroy($id)
    {
        $item = CategoriaAdicional::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash('flash_success', 'Categoria removida com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível deletar: ' . $e->getMessage());
        }
        return redirect()->route('categoria-adicional.index');
    }
    
}
