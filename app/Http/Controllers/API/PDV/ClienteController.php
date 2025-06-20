<?php

namespace App\Http\Controllers\API\PDV;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Funcionario;

class ClienteController extends Controller
{
    public function all(Request $request){
        $data = Cliente::where('empresa_id', $request->empresa_id)
        ->select('id', 'razao_social', 'cpf_cnpj', 'rua', 'numero', 'bairro', 'complemento', 'status', 'ie', 'cidade_id', 'cep')
        ->with('cidade')
        ->get();
        return response()->json($data, 200);
    }

    public function vendedores(Request $request){
        $data = Funcionario::where('empresa_id', $request->empresa_id)
        ->select('id', 'nome', 'cpf_cnpj', 'rua', 'numero', 'bairro', 'status', 'cidade_id')
        ->with('cidade')
        ->where('status', 1)
        ->get();
        return response()->json($data, 200);
    }
}
