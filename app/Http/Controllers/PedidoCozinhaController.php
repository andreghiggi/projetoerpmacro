<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemPedido;
use App\Models\ItemPedidoDelivery;

class PedidoCozinhaController extends Controller
{
    public function index(){
        return view('pedido_cozinha.index');
    }

    public function updateItem(Request $request, $id){
        $item = ItemPedido::findOrfail($id);
        $item->estado = $request->estado;
        if(isset($request->tempo_preparo)){
            $item->tempo_preparo = $request->tempo_preparo;
        }
        $item->save();
        session()->flash("flash_success", "Estado do item #$id - ". $item->produto->nome ." alterado para $request->estado!");
        return redirect()->back();
    }

    public function updateAll(Request $request){
        ItemPedido::join('pedidos', 'pedidos.id', '=', 'item_pedidos.pedido_id')
        ->where('pedidos.empresa_id', $request->empresa_id)
        ->where('item_pedidos.estado', '!=', 'finalizado')
        ->update(['item_pedidos.estado' => 'finalizado']);

        ItemPedidoDelivery::join('pedido_deliveries', 'pedido_deliveries.id', '=', 'item_pedido_deliveries.pedido_id')
        ->where('pedido_deliveries.empresa_id', $request->empresa_id)
        ->where('item_pedido_deliveries.estado', '!=', 'finalizado')
        ->update(['item_pedido_deliveries.estado' => 'finalizado']);

        session()->flash("flash_success", "Itens finalizados");
        return redirect()->back();
    }
}
