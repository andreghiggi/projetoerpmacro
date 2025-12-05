<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoComposicao;
use Illuminate\Http\Request;

class ProdutoCompostoController extends Controller
{
    public function create($id)
    {
        $data = ProdutoComposicao::where('produto_id', $id)->get();
        $item = Produto::findOrFail($id);

        return view('produtos.composto.composto', compact('data', 'item'));
    }

    public function store(Request $request, $id)
    {
        // dd($request);
        $produto = Produto::findOrFail($id);
        try {
            $request->merge([
                'produto_id' => $request->produto_id,
                'ingrediente_id' => $request->ingrediente_id,
                'quantidade' => __convert_value_bd($request->quantidade)
            ]);
            ProdutoComposicao::create($request->all());
            session()->flash("flash_success", "Ingrediente Cadastrado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('produto-composto.create', [$produto->id]);
    }

    public function destroy($id)
    {
        $item = ProdutoComposicao::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Ingrediente Deletado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function show($id)
    {
        $item = Produto::findOrFail($id);
        $data = ProdutoComposicao::where('produto_id', $id)->get();

        return view('produtos.composto.composto', compact('item', 'data'));
    }
}
