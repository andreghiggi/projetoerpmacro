<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PadraoTributacaoProduto;
use App\Models\Produto;
use App\Models\Empresa;

class PadraoTributacaoProdutoAdmController extends Controller
{
    public function index(Request $request){
        $data = PadraoTributacaoProduto::where('empresa_id', $request->empresa)
        ->paginate(__itensPagina());

        $empresa = Empresa::findOrFail($request->empresa);

        return view('padrao_tributacao_adm.index', compact('data', 'empresa'));
    }

    public function create(Request $request){
        $empresa = Empresa::findOrFail($request->empresa);

        return view('padrao_tributacao_adm.create', compact('empresa'));
    }

    public function edit($id){

        $item = PadraoTributacaoProduto::findOrfail($id);
        $empresa = Empresa::findOrFail($item->empresa_id);

        return view('padrao_tributacao_adm.edit', compact('item', 'empresa'));
    }

    public function store(Request $request)
    {

        $request->merge([
            'empresa_id' => $request->empresa
        ]);
        if($request->padrao == 1){
            PadraoTributacaoProduto::where('empresa_id', $request->empresa)
            ->update(['padrao' => 0]);
        }
        try {

            PadraoTributacaoProduto::create($request->all());
            session()->flash("flash_success", "Padrão cadastrado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('produtopadrao-tributacao-adm.index', ['empresa='.$request->empresa]);

    }

    public function update(Request $request, $id)
    {

        $item = PadraoTributacaoProduto::findOrFail($id);

        if($request->padrao == 1){
            PadraoTributacaoProduto::where('empresa_id', $item->empresa_id)
            ->update(['padrao' => 0]);
        }

        try {
            $request->merge([
                'empresa_id' => $item->empresa_id
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Padrão atualizado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('produtopadrao-tributacao-adm.index', ['empresa='.$item->empresa_id]);
    }

    public function destroy($id)
    {
        $item = PadraoTributacaoProduto::findOrFail($id);

        try {
            $item->delete();
            session()->flash("flash_success", "Padrão removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }
}
