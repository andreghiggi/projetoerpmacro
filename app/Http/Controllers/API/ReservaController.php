<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Acomodacao;

class ReservaController extends Controller
{
    public function disponiveis(Request $request){
        $data_checkin = $request->data_checkin;
        $data_checkout = $request->data_checkout;
        $qtd_hospedes = $request->qtd_hospedes;
        $empresa_id = $request->empresa_id;

        $reservas = Reserva::where('empresa_id', $empresa_id)
        ->where(function ($query) use ($data_checkin, $data_checkout) {
            $query->whereDate('data_checkin', '<', $data_checkout)
            ->whereDate('data_checkout', '>', $data_checkin);
        })
        ->where('estado', '!=', 'cancelado')
        ->pluck('acomodacao_id')
        ->all();

        // return response()->json($reservas, 200);

        $data = Acomodacao::where('empresa_id', request()->empresa_id)
        ->whereNotIn('id', $reservas)
        ->where('capacidade', '>=', $qtd_hospedes)
        ->where('status', 1)
        ->get();

        $data = [
            'view' => view('reservas.partials.acomodacoes', compact('data', 'qtd_hospedes'))->render(),
            'resultados' => sizeof($data)
        ];
        return response()->json($data, 200);

    }

    public function dadosAcomodacao(Request $request){
        $data_checkin = $request->data_checkin;
        $data_checkout = $request->data_checkout;
        $acomodacao_id = $request->acomodacao_id;

        $dif = strtotime($data_checkout) - strtotime($data_checkin);
        $dif = $dif/86400;
        $item = Acomodacao::findOrFail($acomodacao_id);

        $item->valor_estadia = $item->valor_diaria * $dif;
        return response()->json($item, 200);

    }

    public function dadosHospedes(Request $request){
        $item = Reserva::findOrFail($request->reserva_id);
        return view('reservas.partials.hospedes', compact('item'));
    }
}
