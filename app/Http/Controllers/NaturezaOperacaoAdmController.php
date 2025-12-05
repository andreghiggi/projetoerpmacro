<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NaturezaOperacao;
use App\Models\Empresa;
use App\Models\Produto;

class NaturezaOperacaoAdmController extends Controller
{
    public function index(Request $request)
    {
        $data = NaturezaOperacao::where('empresa_id', $request->empresa)
        ->when(!empty($request->descricao), function ($q) use ($request) {
            return $q->where('descricao', 'LIKE', "%$request->descricao%");
        })
        ->paginate(__itensPagina());

        $empresa = Empresa::findOrFail($request->empresa);
        return view('natureza_operacao_adm.index', compact('data', 'empresa'));
    }

    public function create(Request $request)
    {
        $empresa = Empresa::findOrFail($request->empresa);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        return view('natureza_operacao_adm.create', compact('listaCTSCSOSN', 'empresa'));
    }

    public function edit($id)
    {
        $item = NaturezaOperacao::findOrFail($id);

        $empresa = Empresa::findOrFail($item->empresa_id);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        return view('natureza_operacao_adm.edit', compact('item', 'listaCTSCSOSN', 'empresa'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'empresa_id' => $request->empresa
            ]);
            NaturezaOperacao::create($request->all());
            session()->flash("flash_success", "Natureza criada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('natureza-operacao-adm.index', ['empresa='.$request->empresa]);
    }

    public function update(Request $request, $id)
    {
        $item = NaturezaOperacao::findOrFail($id);
        try {
            $request->merge([
                'empresa_id' => $item->empresa_id
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Natureza alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('natureza-operacao-adm.index', ['empresa='.$item->empresa_id]);

    }

    public function destroy($id)
    {
        $item = NaturezaOperacao::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Apagado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('natureza-operacao-adm.index', ['empresa='.$item->empresa_id]);
    }

}
