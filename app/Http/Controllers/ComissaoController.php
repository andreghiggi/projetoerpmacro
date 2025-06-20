<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComissaoVenda;
use App\Models\Funcionario;
use App\Models\ContaPagar;

class ComissaoController extends Controller
{
    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $status = $request->get('status');
        $funcionario_id = $request->get('funcionario_id');

        $data = ComissaoVenda::
        when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($funcionario_id), function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->when($status != '', function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->where('empresa_id', $request->empresa_id)
        ->orderBy('created_at', 'desc')->paginate(50);

        $sumComissaoPendente = ComissaoVenda::
        where('status', 0)
        ->where('empresa_id', $request->empresa_id)
        ->sum('valor');

        $sumComissaoPago = ComissaoVenda::
        where('status', 1)
        ->where('empresa_id', $request->empresa_id)
        ->sum('valor');

        $sumVendas = ComissaoVenda::
        where('empresa_id', $request->empresa_id)
        ->sum('valor_venda');

        $funcionario = null;
        if($funcionario_id){
            $funcionario = Funcionario::findOrFail($funcionario_id);
        }

        return view('comissao_venda.index', 
            compact('data', 'sumComissaoPendente', 'sumComissaoPago', 'funcionario', 'sumVendas'));
    }

    public function edit($id){
        $item = ComissaoVenda::findOrfail($id);

        $item->status = 1;
        $item->save();
        session()->flash("flash_success", "Comissão paga!");
        return redirect()->back();
    }

    public function payMultiple(Request $request){
        for($i=0; $i<sizeof($request->check); $i++){

            $item = ComissaoVenda::findOrfail($request->check[$i]);
            $item->status = 1;
            $item->save();
        }

        $local_id = null;
        $caixa = __isCaixaAberto();
        if($caixa != null){
            $local_id = $caixa->local_id;
        }else{
            $local_id = __getLocalAtivo()->id;
        }

        if($request->gerar_conta){
            ContaPagar::create([
                'empresa_id' => $request->empresa_id,
                'valor_integral' => __convert_value_bd($request->valor_integral),
                'data_vencimento' => $request->data_vencimento,
                'observacao' => $request->observacao,
                'tipo_pagamento' => $request->tipo_pagamento,
                'descricao' => $request->descricao,
                'status' => $request->status,
                'local_id' => $local_id
            ]);
            session()->flash("flash_success", "Comissões adicionadas no conta a pagar!");

        }else{
            session()->flash("flash_success", "Comissões pagas!");
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        $item = ComissaoVenda::findOrFail($id);
        try {

            $item->delete();
            session()->flash('flash_success', 'Deletado com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }

}
