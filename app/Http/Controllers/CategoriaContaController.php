<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaConta;

class CategoriaContaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:categoria_conta_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categoria_conta_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:categoria_conta_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:categoria_conta_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        
        $data = CategoriaConta::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->paginate(__itensPagina());

        return view('categoria_conta.index', compact('data'));
    }

    public function create()
    {
        return view('categoria_conta.create');
    }

    public function edit($id)
    {
        $item = CategoriaConta::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('categoria_conta.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $file_name = '';
            if ($request->hasFile('image')) {
                $file_name = $this->util->uploadImage($request, '/categoriaServico');
            }

            if ($request->marketplace) {
                $request->merge([
                    'hash_delivery' => Str::random(50),
                ]);
            }

            $request->merge([
                'imagem' => $file_name
            ]);
            CategoriaConta::create($request->all());
            session()->flash('flash_success', 'Categoria cadastrada com sucesso');
        } catch (\Exception $e) {

            session()->flash('flash_error', 'Não foi possível concluir o cadastro: ' . $e->getMessage());
        }
        return redirect()->route('categoria-conta.index');
    }

    public function update(Request $request, $id)
    {
        $item = CategoriaConta::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            $item->fill($request->all())->save();

            session()->flash('flash_success', 'Categoria alterado com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível alterar o cadastro: ' . $e->getMessage());
        }
        return redirect()->route('categoria-conta.index');
    }

    public function destroy($id)
    {
        $item = CategoriaConta::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash('flash_success', 'Categoria removida com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível deletar: ' . $e->getMessage());
        }
        return redirect()->route('categoria-conta.index');
    }
}
