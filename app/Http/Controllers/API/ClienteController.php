<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ContaReceber;
use App\Models\CashBackConfig;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function find($id)
    {
        $item = Cliente::with(['cidade', 'listaPreco', 'tributacao', 'fatura'])->findOrFail($id);
        return response()->json($item, 200);
    }

    public function cashback($id)
    {
        $item = Cliente::with('cidade')->findOrFail($id);
        $config = CashBackConfig::where('empresa_id', $item->empresa_id)->first();
        if($config == null){
            return response()->json(null, 404);
        }
        $config->valor_cashback = $item->valor_cashback;
        return response()->json($config, 200);
    }

    public function pesquisa(Request $request)
    {
        $data = Cliente::orderBy('razao_social', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        // ->where('razao_social', 'like', "%$request->pesquisa%")
        ->where(function($q) use ($request){
            $q->where('razao_social', 'like', "%$request->pesquisa%")->orWhere('nome_fantasia', 'like', "%$request->pesquisa%")
            ->orWhere('numero_sequencial', 'LIKE', "%$request->pesquisa%");
        })
        ->get();

        return response()->json($data, 200);
    }

    public function pesquisaDelivery(Request $request)
    {
        $data = Cliente::orderBy('razao_social', 'desc')
        ->where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('uid', '!=', '')
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('razao_social', 'LIKE', "%$request->pesquisa%");
        })
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where('telefone', 'LIKE', "%$request->pesquisa%");
        })
        ->get();
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $cliente = Cliente::where('empresa_id', $request->empresa_id)
        ->where('cpf_cnpj', $request->cpf_cnpj)
        ->first();
        if($cliente != null){
            return response()->json("Cliente já cadastrado", 401);
        }
        $cliente = Cliente::create($request->all());
        return response()->json($cliente, 200);
    }

    public function consultaDebitos(Request $request){
        $totalVenda = $request->total;
        $somaContas = ContaReceber::where('cliente_id', $request->cliente_id)
        ->where('status', 0)
        ->sum('valor_integral');

        $cliente = Cliente::findOrFail($request->cliente_id);
        if($somaContas+$totalVenda > $cliente->valor_credito){
            return response()->json("Valor ultrapassa o limite definido no cadastro do cliente", 403);
        }
        return response()->json($cliente->valor_credito, 200);

    }
}
