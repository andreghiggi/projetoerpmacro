<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VariacaoModelo;
use App\Models\VariacaoModeloItem;
use Illuminate\Support\Facades\DB;

class VariacaoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:variacao_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:variacao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:variacao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:variacao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $data = VariacaoModelo::where('empresa_id', $request->empresa_id)
        ->get();

        return view('variacao_modelo.index', compact('data'));
    }

    public function create(){
        return view('variacao_modelo.create');
    }

    public function edit($id){
        $item = VariacaoModelo::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('variacao_modelo.edit', compact('item'));
    }

    public function store(Request $request){
        try{
            DB::transaction(function () use ($request) {
                $item = VariacaoModelo::create($request->all());

                for($i=0; $i<sizeof($request->nome); $i++){
                    VariacaoModeloItem::create([
                        'variacao_modelo_id' => $item->id,
                        'nome' => $request->nome[$i]
                    ]);
                }
                return 1;
            });

            __createLog($request->empresa_id, 'Variação  de Produto', 'cadastrar', $request->descricao);
            session()->flash("flash_success", "Cadastrado com Sucesso");
        }catch(\Exception $e){
            __createLog($request->empresa_id, 'Variação de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", "Não foi possivel fazer o cadastro" . $e->getMessage());
        }
        return redirect()->route('variacoes.index');
    }

    public function update(Request $request, $id){
        try{
            DB::transaction(function () use ($request, $id) {
                $item = VariacaoModelo::findOrFail($id);
                __validaObjetoEmpresa($item);
                $item->fill($request->all())->save();
                $item->itens()->delete();

                for($i=0; $i<sizeof($request->nome); $i++){
                    VariacaoModeloItem::create([
                        'variacao_modelo_id' => $item->id,
                        'nome' => $request->nome[$i]
                    ]);
                }
                return 1;
            });
            __createLog($request->empresa_id, 'Variação  de Produto', 'editar', $request->descricao);
            session()->flash("flash_success", "Atualizado com Sucesso");
        }catch(\Exception $e){
            __createLog($request->empresa_id, 'Variação de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", "Não foi possivel fazer o cadastro" . $e->getMessage());
        }
        return redirect()->route('variacoes.index');
    }

    public function destroy(string $id)
    {
        $item = VariacaoModelo::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->descricao;
            $item->itens()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Variação de Produto', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Variação de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('variacoes.index');
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = VariacaoModelo::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->nome;
                $item->itens()->delete();
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Variação de Produto', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Variação de Produto', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('variacoes.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->route('variacoes.index');
    }
}
