<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelacaoDadosFornecedor;

class RelacaoDadosFornecedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:relacao_dados_fornecedor_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:relacao_dados_fornecedor_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:relacao_dados_fornecedor_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:relacao_dados_fornecedor_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        
        $data = RelacaoDadosFornecedor::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->cst_csosn_entrada), function ($q) use ($request) {
            return $q->where('cst_csosn_entrada', $request->cst_csosn_entrada);
        })
        ->when(!empty($request->cst_csosn_saida), function ($q) use ($request) {
            return $q->where('cst_csosn_saida', $request->cst_csosn_saida);
        })
        ->when(!empty($request->cfop_saida), function ($q) use ($request) {
            return $q->where('cfop_saida', $request->cfop_saida);
        })
        ->when(!empty($request->cfop_entrada), function ($q) use ($request) {
            return $q->where('cfop_entrada', $request->cfop_entrada);
        })
        ->paginate(__itensPagina());

        return view('relacao_dados_fornecedor.index', compact('data'));
    }

    public function create()
    {
        return view('relacao_dados_fornecedor.create');
    }

    public function edit($id)
    {
        $item = RelacaoDadosFornecedor::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('relacao_dados_fornecedor.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            RelacaoDadosFornecedor::create($request->all());
            session()->flash("flash_success", "Relação criada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('relacao-dados-fornecedor.index');
    }

    public function update(Request $request, $id)
    {
        $item = RelacaoDadosFornecedor::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Relação alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('relacao-dados-fornecedor.index');
    }

    public function destroy($id)
    {
        $item = RelacaoDadosFornecedor::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash("flash_success", "Relação removida com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

}
