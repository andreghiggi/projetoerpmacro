<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemPedido;
use App\Models\Funcionario;

class AtendimentoGarcomController extends Controller
{
    public function index(Request $request){
        $funcionario_id = $request->funcionario_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $funcionario = null;

        $data = ItemPedido::join('pedidos', 'pedidos.id', '=', 'item_pedidos.pedido_id')
        ->select('item_pedidos.*')
        ->where('pedidos.empresa_id', $request->empresa_id)
        ->where('item_pedidos.funcionario_id', '!=', null)
        ->when($funcionario_id, function ($q) use ($funcionario_id) {
            return $q->where('item_pedidos.funcionario_id', $funcionario_id);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('item_pedidos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('item_pedidos.created_at', '<=', $end_date);
        })
        ->orderBy('item_pedidos.id', 'desc')
        ->paginate(__itensPagina());

        $soma = ItemPedido::join('pedidos', 'pedidos.id', '=', 'item_pedidos.pedido_id')
        ->select('item_pedidos.*')
        ->where('pedidos.empresa_id', $request->empresa_id)
        ->where('item_pedidos.funcionario_id', '!=', null)
        ->when($funcionario_id, function ($q) use ($funcionario_id) {
            return $q->where('item_pedidos.funcionario_id', $funcionario_id);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('item_pedidos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('item_pedidos.created_at', '<=', $end_date);
        })
        ->sum('sub_total');

        return view('atendimento_garcom.index', compact('data', 'funcionario', 'soma'));
    }
}
