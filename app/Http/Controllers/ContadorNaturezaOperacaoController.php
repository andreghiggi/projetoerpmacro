<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NaturezaOperacao;
use App\Models\Empresa;
use App\Models\Produto;

class ContadorNaturezaOperacaoController extends Controller
{
    public function index(Request $request){

        $contador = Empresa::findOrFail($request->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        $data = NaturezaOperacao::where('empresa_id', $empresaSelecionada)
        ->when(!empty($request->descricao), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('descricao', 'LIKE', "%$request->descricao%");
            });
        })
        ->paginate(__itensPagina());

        return view('contador_natureza_operacao.index', compact('data'));
    }

    public function create()
    {
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;

        $empresa = Empresa::findOrFail($empresaSelecionada);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        return view('contador_natureza_operacao.create', compact('listaCTSCSOSN'));
    }

    public function edit($id)
    {
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;

        $empresa = Empresa::findOrFail($empresaSelecionada);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        $item = NaturezaOperacao::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);
        return view('contador_natureza_operacao.edit', compact('item', 'listaCTSCSOSN'));
    }

    public function store(Request $request)
    {
        $contador = Empresa::findOrFail($request->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;

        if($request->padrao == 1){
            NaturezaOperacao::where('empresa_id', $empresaSelecionada)
            ->update(['padrao' => 0]);
        }
        try {
            $request->merge(['empresa_id' => $empresaSelecionada]);

            NaturezaOperacao::create($request->all());
            session()->flash("flash_success", "Natureza criada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('contador-natureza-operacao.index');
    }

    public function update(Request $request, $id)
    {

        $contador = Empresa::findOrFail($request->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;

        $item = NaturezaOperacao::findOrFail($id);
        if($request->padrao == 1){
            NaturezaOperacao::where('empresa_id', $empresaSelecionada)
            ->update(['padrao' => 0]);
        }
        $request->merge(['empresa_id' => $empresaSelecionada]);
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Natureza alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('contador-natureza-operacao.index');
    }

    public function destroy($id)
    {
        $item = NaturezaOperacao::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        try {
            $item->delete();
            session()->flash("flash_success", "Apagado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('contador-natureza-operacao.index');
    }
}
