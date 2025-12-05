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

    public function filtroTroca(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $codigo_venda = $request->codigo_venda;
        $numero_documento = $request->numero_documento;

        $perPage = 20;
        $page = $request->page ?? 1;

        $queryNfe = Nfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($codigo_venda), function ($query) use ($codigo_venda) {
            return $query->where('numero_sequencial', $codigo_venda);
        })
        ->when(!empty($numero_documento), function ($query) use ($numero_documento) {
            return $query->where('numero', $numero_documento);
        })
        ->where('tpNF', 1)
        ->where('orcamento', 0)
        ->orderBy('created_at', 'desc');

        $queryNfce = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($codigo_venda), function ($query) use ($codigo_venda) {
            return $query->where('numero_sequencial', $codigo_venda);
        })
        ->when(!empty($numero_documento), function ($query) use ($numero_documento) {
            return $query->where('numero', $numero_documento);
        })
        ->orderBy('created_at', 'desc');

        $nves  = $queryNfe->forPage($page, $perPage)->get();
        $nfces = $queryNfce->forPage($page, $perPage)->get();

        $data = $nves->merge($nfces)->sortByDesc('created_at')->values();

        $isLastPage = $data->count() < $perPage;

        return response()->json([
            'html' => view('trocas.partials.linha_escolha', compact('data'))->render(),
            'lastPage' => $isLastPage
        ]);
    }

}
