<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemPedido;
use App\Models\ItemPedidoDelivery;

class PedidoController extends Controller
{
    public function itensPendentes(Request $request){
        $empresa_id = $request->empresa_id;

        $data = [];
        $dataCardapio = ItemPedido::select('item_pedidos.*')
        ->join('pedidos', 'pedidos.id', '=', 'item_pedidos.pedido_id')
        ->where('pedidos.empresa_id', $empresa_id)
        ->where('item_pedidos.estado', '!=', 'finalizado')
        ->orderBy('item_pedidos.created_at', 'desc')
        ->get();

        foreach($dataCardapio as $item){
            $item->is_cardapio = 1;
            array_push($data, $item);
        }

        $dataDelivery = ItemPedidoDelivery::select('item_pedido_deliveries.*')
        ->join('pedido_deliveries', 'pedido_deliveries.id', '=', 'item_pedido_deliveries.pedido_id')
        ->where('pedido_deliveries.empresa_id', $empresa_id)
        ->where('item_pedido_deliveries.estado', '!=', 'finalizado')
        ->orderBy('item_pedido_deliveries.created_at', 'desc')
        ->get();

        foreach($dataDelivery as $item){
            $item->is_delivery = 1;
            array_push($data, $item);
        }

        return view('pedidos.partials.itens_pendentes', compact('data'));
    }
}
