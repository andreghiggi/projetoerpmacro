<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaAcomodacao;
use App\Models\Acomodacao;

class AcomodacaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:acomodacao_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:acomodacao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:acomodacao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:acomodacao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Acomodacao::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->when($request->categoria_id, function ($q) use ($request) {
            return $q->where('categoria_id', $request->categoria_id);
        })
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());

        $categorias = CategoriaAcomodacao::where('empresa_id', request()->empresa_id)->get();

        return view('acomodacao.index', compact('data', 'categorias'));
    }

    public function create()
    {
        $categorias = CategoriaAcomodacao::where('empresa_id', request()->empresa_id)->get();
        if(sizeof($categorias) == 0){
            session()->flash("flash_warning", 'Cadastre uma categoria!');
            return redirect()->route('categoria-acomodacao.create');
        }
        return view('acomodacao.create', compact('categorias'));
    }

    public function edit($id)
    {
        $item = Acomodacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        $categorias = CategoriaAcomodacao::where('empresa_id', request()->empresa_id)->get();
        if(sizeof($categorias) == 0){
            session()->flash("flash_warning", 'Cadastre uma categoria!');
            return redirect()->route('categoria-acomodacao.create');
        }
        return view('acomodacao.edit', compact('item', 'categorias'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'valor_diaria' => __convert_value_bd($request->valor_diaria)
            ]);
            Acomodacao::create($request->all());
            session()->flash("flash_success", "Acomodação criada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('acomodacao.index');
    }

    public function update(Request $request, $id)
    {
        $item = Acomodacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $request->merge([
                'valor_diaria' => __convert_value_bd($request->valor_diaria)
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Acomodação alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('acomodacao.index');
    }

    public function destroy($id)
    {
        $item = Acomodacao::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash("flash_success", "Acomodação removida com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('acomodacao.index');
    }
}
