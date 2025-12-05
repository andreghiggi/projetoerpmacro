<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaProduto;
use Illuminate\Support\Str;

class CategoriaProdutoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:categoria_produtos_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:categoria_produtos_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:categoria_produtos_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:categoria_produtos_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $data = CategoriaProduto::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->where('categoria_id', null)
        ->orderBy('nome', 'asc')
        ->paginate(__itensPagina());
        return view('categoria_produtos.index', compact('data'));
    }

    public function create(Request $request)
    {
        $delivery = 0;
        if (isset($request->delivery)) {
            $delivery = 1;
        }
        return view('categoria_produtos.create', compact('delivery'));
    }

    public function edit($id)
    {
        $item = CategoriaProduto::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('categoria_produtos.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            $categoria = CategoriaProduto::where('nome', $request->nome)
            ->where('empresa_id', $request->empresa_id)->first();

            if($categoria != null){
                session()->flash("flash_warning", 'Já existe uma categoria com este nome');
                return redirect()->back();
            }

            if ($request->ecommerce) {
                $request->merge([
                    'hash_ecommerce' => Str::random(50),
                ]);
            }

            if ($request->delivery) {
                $request->merge([
                    'hash_delivery' => Str::random(50),
                ]);
            }
            CategoriaProduto::create($request->all());
            __createLog($request->empresa_id, 'Categoria de Produto', 'cadastrar', $request->nome);
            session()->flash("flash_success", "Categoria criada com sucesso!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Categoria de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('categoria-produtos.index');
    }

    public function update(Request $request, $id)
    {
        $item = CategoriaProduto::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            if ($request->ecommerce) {
                $request->merge([
                    'hash_ecommerce' => $item->hash_ecommerce != null ? $item->hash_ecommerce : Str::random(50),
                ]);
            }

            if ($request->delivery) {
                $request->merge([
                    'hash_delivery' => $item->hash_delivery != null ? $item->hash_delivery : Str::random(50),
                ]);
            }
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Categoria de Produto', 'editar', $request->nome);
            session()->flash("flash_success", "Categoria alterada com sucesso!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Categoria de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('categoria-produtos.index');
    }

    public function destroy($id)
    {
        $item = CategoriaProduto::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->delete();
            __createLog(request()->empresa_id, 'Categoria de Produto', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Categoria removida com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Categoria de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = CategoriaProduto::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->nome;
                $item->delete();
                __createLog(request()->empresa_id, 'Categoria de Produto', 'excluir', $descricaoLog);
                $removidos++;
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Categoria de Produto', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('categoria-produtos.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->back();
    }
}
