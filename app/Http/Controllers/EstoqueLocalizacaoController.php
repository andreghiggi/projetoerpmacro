<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Utils\EstoqueUtil;

class EstoqueLocalizacaoController extends Controller
{

    public function __construct(EstoqueUtil $utilEstoque)
    {
        $this->utilEstoque = $utilEstoque;
    }

    public function define($id){
        $item = Produto::findOrFail($id);
        __validaObjetoEmpresa($item);


        return view('estoque.inicia_localizacao', compact('item'));
    }

    public function store(Request $request, $id){
        $item = Produto::findOrFail($id);
        try{
            for($i=0; $i<sizeof($request->quantidade); $i++){
                $this->utilEstoque->incrementaEstoque($item->id, $request->quantidade[$i], null, $request->local_id[$i]);
            }
            session()->flash("flash_success", "Estoque definido");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->route('produtos.index');
    }
}
