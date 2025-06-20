<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaBoleto;

class ContaBoletoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:contas_boleto_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:contas_boleto_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:contas_boleto_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:contas_boleto_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = ContaBoleto::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->banco), function ($q) use ($request) {
            return $q->where('banco', $request->banco);
        })
        ->get();

        $banco = $request->banco;
        return view('contas_boleto.index', compact('data', 'banco'));
    }

    public function create(){
        return view('contas_boleto.create');
    }

    public function edit($id){
        $item = ContaBoleto::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('contas_boleto.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            if($request->padrao == 1){
                ContaBoleto::where('empresa_id', $request->empresa_id)
                ->update(['padrao' => 0]);
            }
            $request->merge([
                'juros' => $request->juros ? __convert_value_bd($request->juros) : 0,
                'multa' => $request->multa ? __convert_value_bd($request->multa) : 0,
            ]);
            ContaBoleto::create($request->all());
            __createLog($request->empresa_id, 'Conta para Boleto', 'cadastrar', $request->banco);
            session()->flash("flash_success", "Conta criada com sucesso!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta para Boleto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('contas-boleto.index');
    }

    public function update(Request $request, $id)
    {
        if($request->padrao == 1){
            ContaBoleto::where('empresa_id', $request->empresa_id)
            ->update(['padrao' => 0]);
        }
        $item = ContaBoleto::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $request->merge([
                'juros' => $request->juros ? __convert_value_bd($request->juros) : 0,
                'multa' => $request->multa ? __convert_value_bd($request->multa) : 0,
            ]);
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Conta para Boleto', 'editar', $request->banco);

            session()->flash("flash_success", "Conta alterada com sucesso!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Conta para Boleto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('contas-boleto.index');
    }

    public function destroy($id)
    {
        $item = ContaBoleto::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->banco;
            $item->delete();
            __createLog(request()->empresa_id, 'Conta para Boleto', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Conta removida com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Conta para Boleto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('contas-boleto.index');
    }
}
