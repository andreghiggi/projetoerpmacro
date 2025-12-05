<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CupomDesconto;
use App\Models\Cliente;

class CupomDescontoController extends Controller
{
    public function index(Request $request)
    {
        $cliente_id = $request->cliente_id;
        $data_expiracao = $request->data_expiracao;
        $status = $request->status;
        $data = CupomDesconto::where('empresa_id', request()->empresa_id)
        ->orderBy('id', 'desc')
        ->when($status != '', function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->when($data_expiracao, function ($q) use ($data_expiracao) {
            return $q->where('expiracao', $data_expiracao);
        })
        ->when($cliente_id, function ($q) use ($cliente_id) {
            return $q->where('cliente_id', $cliente_id);
        })
        ->paginate(__itensPagina());
        $cliente = null;
        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }
        return view('cupom_desconto.index', compact('data', 'cliente'));
    }

    public function create()
    {
        return view('cupom_desconto.create');
    }

    public function edit($id)
    {
        $item = CupomDesconto::findOrFail($id);
        return view('cupom_desconto.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'valor' => __convert_value_bd($request->valor),
                'valor_minimo_pedido' => __convert_value_bd($request->valor_minimo_pedido),
            ]);
            CupomDesconto::create($request->all());
            session()->flash("flash_success", "Cupom criado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('cupom-desconto.index');
    }

    public function update(Request $request, $id)
    {
        $item = CupomDesconto::findOrFail($id);
        try {
            $request->merge([
                'valor' => __convert_value_bd($request->valor),
                'valor_minimo_pedido' => __convert_value_bd($request->valor_minimo_pedido),
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Cupom alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('cupom-desconto.index');
    }

    public function destroy($id)
    {
        $item = CupomDesconto::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('cupom-desconto.index');
    }
}
