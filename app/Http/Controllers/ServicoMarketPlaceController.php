<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaServico;
use App\Models\Servico;

class ServicoMarketPlaceController extends Controller
{
    public function categorias(Request $request){
        $nome = $request->nome;
        $data = CategoriaServico::where('empresa_id', $request->empresa_id)
        ->when(!empty($nome), function ($q) use ($nome) {
            return $q->where('nome', 'LIKE', "%$nome%");
        })
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());
        return view('delivery.servicos.categorias', compact('data'));
    }

    public function index(Request $request){
        $status = $request->status;
        $nome = $request->nome;

        $data = Servico::where('empresa_id', $request->empresa_id)
        ->when(!empty($nome), function ($q) use ($nome) {
            return $q->where('nome', 'LIKE', "%$nome%");
        })
        ->when($status != '', function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->where('marketplace', 1)
        ->paginate(__itensPagina());

        return view('delivery.servicos.index', compact('data'));

    }
}
