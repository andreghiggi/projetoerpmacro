<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoReservaController extends Controller
{
    public function index(Request $request){
        $status = $request->status;
        $nome = $request->nome;

        $data = Produto::where('empresa_id', $request->empresa_id)
        ->when(!empty($nome), function ($q) use ($nome) {
            return $q->where('nome', 'LIKE', "%$nome%");
        })
        ->when($status != '', function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->where('reserva', 1)
        ->paginate(__itensPagina());

        return view('reservas.produtos.index', compact('data'));

    }
}
