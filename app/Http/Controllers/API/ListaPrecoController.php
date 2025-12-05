<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ListaPreco;
use App\Models\ListaPrecoUsuario;

class ListaPrecoController extends Controller
{
    public function pesquisa(Request $request)
    {
        $tipo_pagamento = $request->tipo_pagamento_lista;
        $funcionario_id = $request->funcionario_lista_id;
        $usuario_id = $request->usuario_id;

        $data = ListaPreco::orderBy('nome', 'desc')
        ->select('lista_precos.*')
        ->where('empresa_id', $request->empresa_id)
        ->where('lista_precos.status', 1)
        ->where('nome', 'like', "%$request->pesquisa%")
        ->when($tipo_pagamento, function ($query) use ($tipo_pagamento) {
            return $query->where('tipo_pagamento', $tipo_pagamento);
        })
        ->when($funcionario_id, function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->join('lista_preco_usuarios', 'lista_preco_usuarios.lista_preco_id', '=', 'lista_precos.id')
        ->where('lista_preco_usuarios.usuario_id', $usuario_id)
        ->get();
        
        return response()->json($data, 200);
    }

    public function find(Request $request)
    {
        $item = ListaPreco::findOrFail($request->id);
        return response()->json($item, 200);
    }
}
