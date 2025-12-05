<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Difal;

class DifalController extends Controller
{
    public function index(Request $request)
    {
        $data = Difal::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->cfop), function ($q) use ($request) {
            return $q->where('cfop', 'LIKE', "%$request->cfop%");
        })
        ->paginate(__itensPagina());
        return view('difal.index', compact('data'));
    }

    public function create()
    {
        return view('difal.create');
    }

    public function edit($id)
    {
        $item = Difal::findOrFail($id);
        return view('difal.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            Difal::create($request->all());
            session()->flash("flash_success", "Cadastrado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('difal.index');
    }

    public function update(Request $request, $id)
    {
        $item = Difal::findOrFail($id);
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('difal.index');
    }

    public function destroy($id)
    {
        $item = Difal::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('difal.index');
    }
}
