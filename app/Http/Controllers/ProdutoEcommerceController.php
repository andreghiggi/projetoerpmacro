<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use Illuminate\Support\Str;

class ProdutoEcommerceController extends Controller
{

    private function insertHash(){
        $data = CategoriaProduto::where('empresa_id', request()->empresa_id)
        ->where('hash_ecommerce', null)->get();
        foreach($data as $c){
            $c->hash_ecommerce = Str::random(50);
            $c->save();
        }
    }

    public function categorias(Request $request){
        $this->insertHash();
        $nome = $request->nome;
        $data = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->when(!empty($nome), function ($q) use ($nome) {
            return $q->where('nome', 'LIKE', "%$nome%");
        })
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());
        return view('ecommerce.categorias.index', compact('data'));
    }

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
        ->where('ecommerce', 1)
        ->paginate(__itensPagina());

        return view('ecommerce.produtos.index', compact('data'));
    }
}
