<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContaPagarController extends Controller
{
    public function recorrencia(Request $request)
    {
        $data = explode("/", $request->data);
        $vencimento = $request->vencimento;
        $valor = $request->valor;

        $dia = \Carbon\Carbon::parse($vencimento)->format('d');

        $novaData = "20" . $data[1] . "-" . $data[0] . "-" . $dia;
        $dif = strtotime($novaData) - strtotime($vencimento);

        $meses = floor($dif / (60 * 60 * 24 * 30));

        $datas = [];
        $data = $vencimento;
        // return response()->json($meses, 200);
        for ($i = 0; $i < $meses; $i++) {
            $data = date('Y-m-d', strtotime("+30 days", strtotime($data)));
            array_push($datas, $data);
        }

        return view('conta-pagar.partials.recorrencia', compact('datas', 'valor'));
    }
}
