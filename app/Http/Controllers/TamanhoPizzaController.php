<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TamanhoPizza;

class TamanhoPizzaController extends Controller
{

    public function index(Request $request)
    {
        $data = TamanhoPizza::where('empresa_id', request()->empresa_id)
        ->orderBy('nome', 'asc')
        ->get();
        return view('tamanhos_pizza.index', compact('data'));
    }

    public function create()
    {
        return view('tamanhos_pizza.create');
    }

    public function edit($id)
    {
        $item = TamanhoPizza::findOrFail($id);
        return view('tamanhos_pizza.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            TamanhoPizza::create($request->all());
            session()->flash("flash_success", "Tamanho criado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado, ' . $e->getMessage());
        }
        return redirect()->route('tamanhos-pizza.index');
    }

    public function update(Request $request, $id)
    {
        $item = TamanhoPizza::findOrFail($id);
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Tamanho alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado, ' . $e->getMessage());
        }
        return redirect()->route('tamanhos-pizza.index');
    }

    public function destroy($id)
    {
        $item = TamanhoPizza::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Apagado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('tamanhos-pizza.index');
    }
}
