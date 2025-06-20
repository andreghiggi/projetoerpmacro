<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Nfce;

class VendaController extends Controller
{
    public function pesquisa(Request $request){
        $vendas = Nfe::where('nves.empresa_id', $request->empresa_id)
        ->join('clientes', 'clientes.id', '=', 'nves.cliente_id')
        ->select('nves.cliente_id', 'nves.id', 'nves.numero_sequencial')
        ->with('cliente')
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('clientes.razao_social', 'LIKE', "%$request->pesquisa%");
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nves.numero_sequencial', 'LIKE', "%$request->pesquisa%");
        })
        ->get();

        $vendasPdv = Nfce::where('nfces.empresa_id', $request->empresa_id)
        ->select('nfces.cliente_id', 'nfces.id', 'nfces.numero_sequencial')
        ->with('cliente')
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('clientes.razao_social', 'LIKE', "%$request->pesquisa%")
            ->join('clientes', 'clientes.id', '=', 'nfces.cliente_id');
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('nfces.numero_sequencial', 'LIKE', "%$request->pesquisa%");
        })
        ->get();

        $data = [];

        foreach($vendas as $v){
            $v->tipo = 'pedido';
            $data[] = $v;
        }

        foreach($vendasPdv as $v){
            $v->tipo = 'pdv';
            $data[] = $v;
        }

        return response()->json($data, 200);

    }
}
