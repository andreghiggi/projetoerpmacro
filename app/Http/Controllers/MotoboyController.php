<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motoboy;
use App\Models\MotoboyComissao;
use App\Models\ContaPagar;

class MotoboyController extends Controller
{
    public function index(Request $request)
    {
        $data = Motoboy::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->when($request->status != '', function ($q) use ($request) {
            return $q->where('status', $request->status);
        })
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());
        return view('motoboys.index', compact('data'));
    }

    public function create()
    {
        return view('motoboys.create');
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'valor_comissao' => __convert_value_bd($request->valor_comissao)
            ]);
            Motoboy::create($request->all());
            session()->flash('flash_success', 'Cadastrado com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado' . $e->getMessage());
        }
        return redirect()->route('motoboys.index');
    }

    public function edit($id)
    {
        $item = Motoboy::findOrFail($id);
        return view('motoboys.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Motoboy::findOrFail($id);
        try {
            $request->merge([
                'valor_comissao' => __convert_value_bd($request->valor_comissao)
            ]);
            $item->fill($request->all())->save();
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado' . $e->getMessage());
        }
        return redirect()->route('motoboys.index');
    }

    public function destroy($id)
    {
        $item = Motoboy::findOrFail($id);
        try {
            $item->delete();
            session()->flash('flash_success', 'Removido com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado' . $e->getMessage());
        }
        return redirect()->route('motoboys.index');
    }

    public function comissao(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $status = $request->get('status');

        $motoboy_id = $request->motoboy_id;
        $data = MotoboyComissao::where('empresa_id', request()->empresa_id)
        ->when($status != '', function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($motoboy_id), function ($query) use ($motoboy_id) {
            return $query->where('motoboy_id', $motoboy_id);
        })
        ->orderBy('id', 'desc')
        ->paginate(50);

        $motoboys = Motoboy::where('empresa_id', request()->empresa_id)
        ->where('status', 1)
        ->get();

        $motoboy = null;
        if($motoboy_id){
            $motoboy = Motoboy::findOrFail($motoboy_id);
        }

        $sumComissaoPendente = MotoboyComissao::
        where('status', 0)
        ->where('empresa_id', $request->empresa_id)
        ->sum('valor');

        $sumComissaoPago = MotoboyComissao::
        where('status', 1)
        ->where('empresa_id', $request->empresa_id)
        ->sum('valor');

        $sumPedidos = MotoboyComissao::
        where('empresa_id', $request->empresa_id)
        ->sum('valor_total_pedido');

        return view('motoboys.comissao', 
            compact('data', 'motoboys', 'motoboy', 'sumComissaoPendente', 'sumComissaoPago', 'sumPedidos'));
    }

    public function payMultiple(Request $request){
        // dd($request->all());
        for($i=0; $i<sizeof($request->check); $i++){
            $item = MotoboyComissao::findOrfail($request->check[$i]);
            $item->status = 1;
            $item->save();
        }

        if($request->gerar_conta){

            $local_id = null;
            $caixa = __isCaixaAberto();
            if($caixa != null){
                $local_id = $caixa->local_id;
            }else{
                $local_id = __getLocalAtivo()->id;
            }
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

}
