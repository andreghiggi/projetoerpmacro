<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromocaoProduto;
use App\Models\Produto;
use App\Models\ConfigGeral;

class PromocaoProdutoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:promocao_produtos_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:promocao_produtos_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:promocao_produtos_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:promocao_produtos_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $produto_id = $request->produto_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $ativa = $request->ativa;

        $data = PromocaoProduto::where('produtos.empresa_id', request()->empresa_id)
        ->select('promocao_produtos.*')
        ->join('produtos', 'produtos.id', '=', 'promocao_produtos.produto_id')
        ->when(!empty($produto_id), function ($q) use ($produto_id) {
            return $q->where('produtos.id', $produto_id);
        })
        ->when(!is_null($status), function ($query) use ($status) {
            return $query->where('promocao_produtos.status', $status);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_inicio', '<=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data_fim', '>=', $end_date);
        })

        ->when(($ativa == 1), function ($query) {
            return $query->where('promocao_produtos.status', 1)
            ->whereDate('data_inicio', '<=', now())
            ->whereDate('data_fim', '>=', now());
        })
        ->when(($ativa == -1), function ($query) {
            return $query->whereDate('data_inicio', '>', now())
            ->whereDate('data_fim', '<', now());
        })

        ->orderBy('promocao_produtos.created_at', 'desc')
        ->paginate(__itensPagina());

        $produto = null;
        if($produto_id){
            $produto = Produto::findOrFail($produto_id);
        }

        $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();
        $tipoExibe = $configGeral && $configGeral->produtos_exibe_tabela == 0 
        ? 'card' 
        : 'tabela';

        return view('promocao_produtos.index', compact('data', 'produto', 'tipoExibe'));
    }

    public function create(Request $request)
    {
        return view('promocao_produtos.create');
    }

    public function edit($id)
    {
        $item = PromocaoProduto::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('promocao_produtos.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            $request->merge([
                'valor' => __convert_value_bd($request->valor),
                'valor_original' => __convert_value_bd($request->valor_original),
            ]);

            PromocaoProduto::create($request->all());
            session()->flash("flash_success", "Promoção cadastrada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('promocao-produtos.index');
    }

    public function update(Request $request, $id)
    {
        $item = PromocaoProduto::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            if ($request->ecommerce) {
                $request->merge([
                    'hash_ecommerce' => $item->hash_ecommerce != null ? $item->hash_ecommerce : Str::random(50),
                ]);
            }

            $request->merge([
                'valor' => __convert_value_bd($request->valor),
                'valor_original' => __convert_value_bd($request->valor_original),
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Promoção alterada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('promocao-produtos.index');
    }

    public function destroy($id)
    {
        $item = PromocaoProduto::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash("flash_success", "Promoção removida com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function group(){

        $produtos = Produto::where('empresa_id', request()->empresa_id)
        ->where('status', 1)
        ->get();

        return view('promocao_produtos.group', compact('produtos'));
    }

    public function setGroup(Request $request){
        try{
            $cont = 0;
            for($i=0; $i<sizeof($request->produto_check); $i++){
                $produto = Produto::find($request->produto_check[$i]);
                if($produto != null){
                    PromocaoProduto::create([
                        'produto_id' => $produto->id,
                        'status' => 1,
                        'valor' => $produto->valor_unitario - ($produto->valor_unitario*($request->percentual/100)),
                        'data_inicio' => $request->data_inicio,
                        'data_fim' => $request->data_fim,
                        'valor_original' => $produto->valor_unitario
                    ]);

                    $cont++;
                }
            }
            session()->flash("flash_success", "Promoção definida para $cont produtos!");

        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('promocao-produtos.index');
    }
}
