<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    public function find($id)
    {
        $item = Fornecedor::with('cidade')->findOrFail($id);
        return response()->json($item, 200);
    }

    public function pesquisa(Request $request)
    {
        $data = Fornecedor::orderBy('razao_social', 'desc')
        ->where('empresa_id', $request->empresa_id)
        // ->where('razao_social', 'like', "%$request->pesquisa%")
        ->where(function($q) use ($request){
            $q->where('razao_social', 'like', "%$request->pesquisa%")
            ->orWhere('numero_sequencial', 'LIKE', "%$request->pesquisa%");
        })
        ->get();
        return response()->json($data, 200);
    }

    public function store(Request $request){
        $cliente = Fornecedor::where('empresa_id', $request->empresa_id)
        ->where('cpf_cnpj', $request->cpf_cnpj)
        ->first();
        if($cliente != null){
            return response()->json("Fornecedor já cadastrado", 401);
        }
        $cliente = Fornecedor::create($request->all());
        return response()->json($cliente, 200);
    }
}
