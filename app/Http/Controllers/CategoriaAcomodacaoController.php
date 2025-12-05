<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaAcomodacao;

class CategoriaAcomodacaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:categoria_acomodacao_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categoria_acomodacao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:categoria_acomodacao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:categoria_acomodacao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = CategoriaAcomodacao::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());
        return view('categoria_acomodacao.index', compact('data'));
    }

    public function create()
    {
        return view('categoria_acomodacao.create');
    }

    public function edit($id)
    {
        $item = CategoriaAcomodacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('categoria_acomodacao.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            CategoriaAcomodacao::create($request->all());
            session()->flash("flash_success", "Categoria criada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('categoria-acomodacao.index');
    }

    public function update(Request $request, $id)
    {
        $item = CategoriaAcomodacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Categoria alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('categoria-acomodacao.index');
    }

    public function destroy($id)
    {
        $item = CategoriaAcomodacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash("flash_success", "Categoria removida com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('categoria-acomodacao.index');
    }

}
