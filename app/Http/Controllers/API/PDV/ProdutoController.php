<?php

namespace App\Http\Controllers\API\PDV;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\ListaPreco;
use App\Models\CategoriaProduto;
use App\Models\Localizacao;

class ProdutoController extends Controller
{
    public function produtos(Request $request){
        $updated_at = $request->updated_at;
        $user_id = $request->user_id;

        $locais = Localizacao::where('usuario_localizacaos.usuario_id', $user_id)
        ->select('localizacaos.*')
        ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
        ->where('localizacaos.status', 1)->get();
        $locais = $locais->pluck(['id']);
        
        $data = Produto::where('empresa_id', $request->empresa_id)
        ->select('produtos.id as id', 'produtos.nome as nome', 'valor_unitario', 'categoria_id', 'codigo_barras', 'imagem', 'gerenciar_estoque', 
            'referencia_balanca')
        ->with(['categoria', 'estoque'])
        ->where('status', 1)
        ->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
        ->whereIn('produto_localizacaos.localizacao_id', $locais)
        ->groupBy('produtos.id')
        ->get();
        return response()->json($data, 200);
    }

    public function categorias(Request $request){
        $data = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->get();
        foreach($data as $item){
            $item->total_produtos = sizeof($item->produtos);
        }
        return response()->json($data, 200);
    }

    public function listaPreco(Request $request){
        $data = ListaPreco::where('empresa_id', $request->empresa_id)
        ->with('itens')
        ->where('status', 1)
        ->get();
        return response()->json($data, 200);
    }
}
