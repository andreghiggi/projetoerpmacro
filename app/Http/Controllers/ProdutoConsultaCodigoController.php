<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdutoUnico;
use App\Models\Produto;

class ProdutoConsultaCodigoController extends Controller
{
    public function index(Request $request){
        $produto_id = $request->produto_id;
        $codigo = $request->codigo;
        $produto = null;
        $data = [];

        if($produto_id || $codigo){
            $data = ProdutoUnico::
            where('produtos.empresa_id', $request->empresa_id)
            ->select('produto_unicos.*')
            ->join('produtos', 'produtos.id', '=', 'produto_unicos.produto_id')
            ->when($codigo, function ($q) use ($codigo) {
                return $q->where('produto_unicos.codigo', 'LIKE', "%$codigo%");
            })
            ->when($produto_id, function ($q) use ($produto_id) {
                return $q->where('produto_unicos.produto_id', $produto_id);
            })
            ->groupBy('produto_unicos.codigo')
            ->orderBy('produto_unicos.created_at', 'desc')
            ->get();
        }

        if($produto_id){
            $produto = Produto::findOrFail($produto_id);
        }
        return view('produtos.consulta_codigo', compact('data', 'produto'));
    }
}
