<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImpressoraPedido;
use App\Models\ImpressoraPedidoProduto;
use App\Models\Produto;

class ImpressoraPedidoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:impressora_pedido_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:impressora_pedido_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:impressora_pedido_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:impressora_pedido_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = ImpressoraPedido::where('empresa_id', $request->empresa_id)
        ->get();

        return view('impressoras_pedido.index', compact('data'));
    }

    public function create(Request $request){

        $produtos = Produto::where('empresa_id', $request->empresa_id)
        ->select('id', 'nome')
        ->get();
        return view('impressoras_pedido.create', compact('produtos'));
    }

    public function edit(Request $request, $id){
        $item = ImpressoraPedido::findOrFail($id);
        __validaObjetoEmpresa($item);

        $produtos = Produto::where('empresa_id', $request->empresa_id)
        ->select('id', 'nome')
        ->get();
        return view('impressoras_pedido.edit', compact('item', 'produtos'));
    }

    public function store(Request $request)
    {

        try{

            $item = ImpressoraPedido::create($request->except('produtos'));
            foreach($request->produtos as $p){
                ImpressoraPedidoProduto::updateOrCreate([
                    'impressora_id' => $item->id,
                    'produto_id' => $p,
                ]);
            }
            session()->flash("flash_success", 'Impressora criada com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('impressoras-pedido.index');
    }

    public function update(Request $request, $id)
    {

        try{
            $item = ImpressoraPedido::findOrFail($id);
            $item->fill($request->except('produtos'))->save();
            $item->produtos()->delete();
            foreach($request->produtos as $p){

                ImpressoraPedidoProduto::updateOrCreate([
                    'impressora_id' => $item->id,
                    'produto_id' => $p,
                ]);
            }
            session()->flash("flash_success", 'Impressora criada com sucesso.');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }

        return redirect()->route('impressoras-pedido.index');
    }

    public function destroy($id)
    {
        $item = ImpressoraPedido::findOrFail($id);
        __validaObjetoEmpresa($item);

        try {
            $item->produtos()->delete();
            $item->delete();
            session()->flash("flash_success", 'Registro removido com sucesso.');
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }
    
}
