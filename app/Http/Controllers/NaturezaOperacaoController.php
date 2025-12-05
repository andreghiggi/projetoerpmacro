<?php

namespace App\Http\Controllers;

use App\Models\NaturezaOperacao;
use App\Models\Produto;
use App\Models\Empresa;
use Illuminate\Http\Request;

class NaturezaOperacaoController extends Controller
{

    public function __construct(){
        $this->middleware('permission:natureza_operacao_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:natureza_operacao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:natureza_operacao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:natureza_operacao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = NaturezaOperacao::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->descricao), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('descricao', 'LIKE', "%$request->descricao%");
            });
        })
        ->paginate(__itensPagina());
        return view('natureza_operacao.index', compact('data'));
    }

    public function create()
    {
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        return view('natureza_operacao.create', compact('listaCTSCSOSN'));
    }

    public function edit($id)
    {
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $listaCTSCSOSN = Produto::listaCSOSN();
        if ($empresa->tributacao == 'Regime Normal') {
            $listaCTSCSOSN = Produto::listaCST();
        }
        $item = NaturezaOperacao::findOrFail($id);
        return view('natureza_operacao.edit', compact('item', 'listaCTSCSOSN'));
    }

    public function store(Request $request)
    {
        if($request->padrao == 1){
            NaturezaOperacao::where('empresa_id', $request->empresa_id)
            ->update(['padrao' => 0]);
        }
        try {
            NaturezaOperacao::create($request->all());
            session()->flash("flash_success", "Natureza criada com sucesso!");
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('natureza-operacao.index');
    }

    public function update(Request $request, $id)
    {
        $item = NaturezaOperacao::findOrFail($id);
        if($request->padrao == 1){
            NaturezaOperacao::where('empresa_id', $request->empresa_id)
            ->update(['padrao' => 0]);
        }
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Natureza alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('natureza-operacao.index');
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
        return redirect()->route('natureza-operacao.index');
    }
}
