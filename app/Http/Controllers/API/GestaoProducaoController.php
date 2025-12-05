<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;

class GestaoProducaoController extends Controller
{
    public function calcular(Request $request){
        $produto_id = $request->produto_id;
        $empresa_id = $request->empresa_id;
        $quantidade = $request->quantidade;

        $produto = Produto::findOrFail($produto_id);

        $disponibilidade = 0;
        foreach($produto->composicao as $c){
            if($c->ingrediente->estoque){
                $d = $c->ingrediente->estoque->quantidade/$c->quantidade;
                if($d < $disponibilidade || $disponibilidade == 0){
                    $disponibilidade = number_format($d, 3, '.', '');
                }
            }
            $c->quantidade = $c->quantidade * $quantidade;
        }

        return view('gestao_producao.partials.table', compact('produto', 'quantidade', 'disponibilidade'));
    }
}
