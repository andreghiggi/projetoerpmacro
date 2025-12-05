<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use Illuminate\Http\Request;

class CidadeController extends Controller
{
    public function index(Request $request)
    {
        $data = Cidade::when(!empty($request->nome), function ($q) use ($request) {
            return $q->where(function ($quer) use ($request) {
                return $quer->where('nome', 'LIKE', "%$request->nome%");
            });
        })
        ->paginate(__itensPagina());

        return view('cidades.index', compact('data'));
    }

    public function create()
    {
        return view('cidades.create');
    }

    public function edit($id)
    {
        $item = Cidade::findOrFail($id);
        return view('cidades.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            Cidade::create($request->all());
            session()->flash("flash_success", "Cidade cadastrada!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('cidades.index');
    }

    public function update(Request $request, $id)
    {
        $item = Cidade::findOrFail($id);
        try{
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Cidade atualizada!");
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('cidades.index');
    }
}
