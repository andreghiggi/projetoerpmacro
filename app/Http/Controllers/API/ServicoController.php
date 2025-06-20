<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servico;

class ServicoController extends Controller
{
    public function pesquisa(Request $request)
    {
        $data = Servico::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->pesquisa%");
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('numero_sequencial', $request->pesquisa);
        })
        ->get();

        return response()->json($data, 200);
    }

    public function pesquisaReserva(Request $request)
    {
        $data = Servico::orderBy('nome', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('reserva', 1)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->pesquisa%");
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('numero_sequencial', $request->pesquisa);
        })
        ->get();

        return response()->json($data, 200);
    }

    public function find($id)
    {
        $item = Servico::findOrFail($id);

        return response()->json($item, 200);
    }
}
