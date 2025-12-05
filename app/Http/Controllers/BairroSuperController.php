<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BairroDeliveryMaster;
use App\Models\Cidade;

class BairroSuperController extends Controller
{
    public function index(Request $request)
    {
        $cidade_id = $request->cidade_id;
        $data = BairroDeliveryMaster::
        when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->when($cidade_id, function ($q) use ($cidade_id) {
            return $q->where('cidade_id', $cidade_id);
        })
        ->paginate(__itensPagina());

        $cidade = null;
        if($cidade_id){
            $cidade = Cidade::findOrFail($cidade_id);
        }
        return view('bairro_super.index', compact('data', 'cidade'));
    }

    public function create()
    {
        return view('bairro_super.create');
    }

    public function edit($id)
    {
        $item = BairroDeliveryMaster::findOrFail($id);
        return view('bairro_super.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            BairroDeliveryMaster::create($request->all());
            session()->flash("flash_success", "Bairro cadastrado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('bairros-super.index');
    }

    public function update(Request $request, $id)
    {
        $item = BairroDeliveryMaster::findOrFail($id);
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Bairro alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('bairros-super.index');
    }

    public function destroy($id)
    {
        $item = BairroDeliveryMaster::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('bairros-super.index');
    }
}
