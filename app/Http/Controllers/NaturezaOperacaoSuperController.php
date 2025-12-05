<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NaturezaOperacaoSuper;
use App\Models\Produto;

class NaturezaOperacaoSuperController extends Controller
{
    public function index(Request $request)
    {
        $data = NaturezaOperacaoSuper::
        when(!empty($request->descricao), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('descricao', 'LIKE', "%$request->descricao%");
            });
        })
        ->paginate(__itensPagina());
        return view('natureza_operacao_super.index', compact('data'));
    }

    public function create()
    {
        $listaCTSCSOSN = Produto::listaCSTCSOSN();
        return view('natureza_operacao_super.create', compact('listaCTSCSOSN'));
    }

    public function edit($id)
    {

        $listaCTSCSOSN = Produto::listaCSTCSOSN();
        $item = NaturezaOperacaoSuper::findOrFail($id);
        return view('natureza_operacao_super.edit', compact('item', 'listaCTSCSOSN'));
    }

    public function store(Request $request)
    {
        if($request->padrao == 1){
            NaturezaOperacaoSuper::where('status', 1)->update(['padrao' => 0]);
        }
        try {
            NaturezaOperacaoSuper::create($request->all());
            session()->flash("flash_success", "Natureza criada com sucesso!");
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('natureza-operacao-super.index');
    }

    public function update(Request $request, $id)
    {
        $item = NaturezaOperacaoSuper::findOrFail($id);
        if($request->padrao == 1){
            NaturezaOperacaoSuper::update(['padrao' => 0]);
        }
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Natureza alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('natureza-operacao-super.index');
    }

    public function destroy($id)
    {
        $item = NaturezaOperacaoSuper::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Apagado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('natureza-operacao-super.index');
    }
}
