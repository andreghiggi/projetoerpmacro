<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PadraoTributacaoProdutoSuper;

class PadraoTributacaoProdutoSuperController extends Controller
{

    public function index(Request $request){
        $data = PadraoTributacaoProdutoSuper::
        when(!empty($request->descricao), function ($q) use ($request) {
            return $q->where('descricao', 'LIKE', "%$request->descricao%");
        })
        ->paginate(__itensPagina());

        return view('padrao_tributacao_super.index', compact('data'));
    }

    public function create(){
        return view('padrao_tributacao_super.create');
    }

    public function edit($id){
        $item = PadraoTributacaoProdutoSuper::findOrfail($id);
        return view('padrao_tributacao_super.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {

        if($request->padrao == 1){
            PadraoTributacaoProdutoSuper::where('status', 1)->update(['padrao' => 0]);
        }

        $item = PadraoTributacaoProdutoSuper::findOrFail($id);
        try {

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Padrão atualizado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('padrao-tributacao-produto-super.index');
    }

    public function destroy($id)
    {
        $item = PadraoTributacaoProdutoSuper::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->descricao;
            $item->delete();
            session()->flash("flash_success", "Padrão removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }


    public function store(Request $request)
    {

        if($request->padrao == 1){
            PadraoTributacaoProdutoSuper::where('status', 1)->update(['padrao' => 0]);
        }
        try {

            PadraoTributacaoProdutoSuper::create($request->all());
            session()->flash("flash_success", "Padrão cadastrado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('padrao-tributacao-produto-super.index');
    }
}
