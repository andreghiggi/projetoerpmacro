<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImpressoraPedido;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\PedidoDelivery;
use App\Models\ItemPedidoDelivery;

class ImpressaoPedidoController extends Controller
{
    public function index(Request $request){
        $data = ImpressoraPedido::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->get();

        return view('impressao_pedido.index', compact('data'));
    }

    public function show($id){
        $item = ImpressoraPedido::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('impressao_pedido.show', compact('item'));
    }

    public function comando(Request $request){
        if(isset($request->pedido_comanda_id)){
            $itens = [];

            $pedido = Pedido::findOrfail($request->pedido_comanda_id);
            for($i=0; $i<sizeof($request->item_id); $i++){
                $it = ItemPedido::findOrfail($request->item_id[$i]);
                $it->impresso = 1;
                $it->save();
                $itens[] = $it;
            }

            return view('impressao_pedido.comanda', compact('itens', 'pedido'));
        }

        if(isset($request->pedido_delivery_id)){
            $itens = [];

            $pedido = PedidoDelivery::findOrfail($request->pedido_delivery_id);
            for($i=0; $i<sizeof($request->item_id); $i++){
                $it = ItemPedidoDelivery::findOrfail($request->item_id[$i]);
                $it->impresso = 1;
                $it->save();
                $itens[] = $it;
            }

            return view('impressao_pedido.delivery', compact('itens', 'pedido'));
        }
    }
    
}
