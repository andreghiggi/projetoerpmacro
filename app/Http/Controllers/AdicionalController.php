<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adicional;

class AdicionalController extends Controller
{
    public function index(Request $request)
    {
        $data = Adicional::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('nome', 'LIKE', "%$request->nome%");
            });
        })
        ->orderBy('nome', 'asc')
        ->paginate(env("PAGINACAO"));
        return view('adicional.index', compact('data'));
    }

    public function create()
    {
        return view('adicional.create');
    }

    public function edit($id)
    {
        $item = Adicional::findOrFail($id);
        return view('adicional.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'valor' => __convert_value_bd($request->valor),
            ]);
            Adicional::create($request->all());
            session()->flash("flash_success", "Adicional criado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->route('adicionais.index');
    }

    public function update(Request $request, $id)
    {
        $item = Adicional::findOrFail($id);
        try {
            $request->merge([
                'valor' => __convert_value_bd($request->valor),
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Adicional alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->route('adicionais.index');
    }

    public function destroy($id)
    {
        $item = Adicional::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->route('adicionais.index');
    }
}
