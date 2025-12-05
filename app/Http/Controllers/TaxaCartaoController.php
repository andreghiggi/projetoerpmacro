<?php

namespace App\Http\Controllers;

use App\Models\TaxaPagamento;
use Illuminate\Http\Request;

class TaxaCartaoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:taxa_pagamento_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:taxa_pagamento_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:taxa_pagamento_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:taxa_pagamento_delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data = TaxaPagamento::where('empresa_id', request()->empresa_id)
        ->paginate(__itensPagina());

        return view('taxa_cartao.index', compact('data'));
    }

    public function create()
    {
        return view('taxa_cartao.create');
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'taxa' => __convert_value_bd($request->taxa),
                'bandeira_cartao' => $request->bandeira_cartao ?? null
            ]);
            $item = TaxaPagamento::create($request->all());
            __createLog($request->empresa_id, 'Taxa de Pagamento', 'cadastrar', $item->getTipo());

            session()->flash("flash_success", "Taxa cadastrada com sucesso!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Taxa de Pagamento', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->route('taxa-cartao.index');
    }

    public function edit($id)
    {
        $item = TaxaPagamento::findOrFail($id);

        return view('taxa_cartao.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = TaxaPagamento::findOrFail($id);
        try {
            $request->merge([
                'taxa' => __convert_value_bd($request->taxa)
            ]);
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Taxa de Pagamento', 'editar', $item->getTipo());
            session()->flash("flash_success", "Taxa atualizada com sucesso!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Taxa de Pagamento', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->route('taxa-cartao.index');
    }

    public function destroy($id)
    {
        $item = TaxaPagamento::findOrFail($id);
        try{
            $descricaoLog = $item->getTipo();
            $item->delete();
            __createLog(request()->empresa_id, 'Taxa de Pagamento', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Deletado com sucesso!");
        }catch(\Exception $e){
            __createLog(request()->empresa_id, 'Taxa de Pagamento', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->route('taxa-cartao.index');
    }
}
