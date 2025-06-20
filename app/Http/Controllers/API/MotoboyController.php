<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motoboy;
use App\Models\PedidoDelivery;

class MotoboyController extends Controller
{
    public function calcComissao(Request $request){
        $item = Motoboy::findOrFail($request->motoboy_id);
        $pedido = PedidoDelivery::findOrFail($request->pedido_id);

        if($item->tipo_comissao == 'valor_fixo'){
            return response()->json($item->valor_comissao, 200);
        }

        $valor = $pedido->valor_total * ($item->valor_comissao/100);
        return response()->json($valor, 200);
    }
}
