<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use App\Models\Adicional;
use App\Models\ProdutoAdicional;
use App\Models\ProdutoIngrediente;
use App\Models\TamanhoPizza;
use App\Models\ProdutoPizzaValor;
use App\Models\CategoriaAdicional;

class ProdutoCardapioController extends Controller
{
    public function categorias(Request $request){
        $nome = $request->nome;
        $data = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->when(!empty($nome), function ($q) use ($nome) {
            return $q->where('nome', 'LIKE', "%$nome%");
        })
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());
        return view('cardapio.categorias.index', compact('data'));
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
        ->where('cardapio', 1)
        ->paginate(__itensPagina());

        return view('cardapio.produtos.index', compact('data'));

    }

    public function show($id){

        $item = Produto::findOrFail($id);
        $adds = $item->adicionais->pluck('adicional_id')->toArray();

        $adicionais = Adicional::where('empresa_id', request()->empresa_id)
        ->orderBy('nome', 'asc')
        ->whereNotIn('id', $adds)
        ->get();

        $categorias = CategoriaAdicional::where('empresa_id', request()->empresa_id)
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->get();

        return view('cardapio.produtos.show', compact('item', 'adicionais', 'categorias'));

    }

    public function storeAdicional(Request $request){
        try {

            if($request->adicional_id){
                ProdutoAdicional::create($request->all());
            }else if($request->categoria_id){
                $categoria = CategoriaAdicional::findOrFail($request->categoria_id);
                foreach($categoria->adicionais as $a){
                    ProdutoAdicional::updateOrCreate([
                        'produto_id' => $request->produto_id,
                        'adicional_id' => $a->id
                    ]);
                }
            }else{
                session()->flash("flash_error", 'Selecione o adicional ou categoria para adicionar!');
                return redirect()->back();
            }
            session()->flash("flash_success", "Adicioanado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function storeIngrediente(Request $request){
        try {
            ProdutoIngrediente::create($request->all());
            session()->flash("flash_success", "Ingrediente adicioanado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroyAdicional($id)
    {
        $item = ProdutoAdicional::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroyIngrediente($id)
    {
        $item = ProdutoIngrediente::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function ingredientes($id){
        $item = Produto::findOrFail($id);

        return view('cardapio.produtos.ingredientes', compact('item'));

    }

    public function tamanhosPizza($id){
        $produto = Produto::findOrFail($id);
        $tamanhos = TamanhoPizza::where('empresa_id', request()->empresa_id)->get();
        return view('produtos.tamanho_pizza', compact('produto', 'tamanhos'));
    }

    public function setValoresTamnho(Request $request, $id){
        $produto = Produto::findOrFail($id);
        ProdutoPizzaValor::where('produto_id', $id)->delete();
        try{
            for($i=0; $i<sizeof($request->tamanho_id); $i++){
                ProdutoPizzaValor::create([
                    'produto_id' => $id,
                    'tamanho_id' => $request->tamanho_id[$i],
                    'valor' => __convert_value_bd($request->valor[$i])
                ]);
            }
            session()->flash("flash_success", "Valores para pizza salvo!");

        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->route('produtos-cardapio.index');
    }
}
