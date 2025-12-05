<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\TransferenciaEstoque;
use App\Models\ItemTransferenciaEstoque;
use App\Models\Localizacao;
use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\ProdutoLocalizacao;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use App\Utils\EstoqueUtil;

class TransferenciaEstoqueController extends Controller
{

    protected $utilEstoque;
    public function __construct(EstoqueUtil $utilEstoque)
    {
        $this->utilEstoque = $utilEstoque;

        $this->middleware('permission:transferencia_estoque_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:transferencia_estoque_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:transferencia_estoque_delete', ['only' => ['destroy']]);
    }
    
    public function index(Request $request){

        $locaisCount = Localizacao::where('empresa_id', $request->empresa_id)
        ->where('status',1)->count();

        if($locaisCount == 0){
            session()->flash('flash_error', 'É necessário ter ao menos 2 localizações ativas na empresa!');
            return redirect()->back();
        }

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $produto = $request->get('produto');

        $data = TransferenciaEstoque::where('transferencia_estoques.empresa_id', $request->empresa_id)
        ->orderBy('transferencia_estoques.id', 'desc')
        ->select('transferencia_estoques.*')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('transferencia_estoques.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('transferencia_estoques.created_at', '<=', $end_date);
        })
        ->when(!empty($produto), function ($query) use ($produto) {
            return $query->join('item_transferencia_estoques', 
                'item_transferencia_estoques.transferencia_id', '=', 'transferencia_estoques.id')
            ->join('produtos', 'produtos.id', '=', 'item_transferencia_estoques.produto_id')
            ->where('produtos.nome', 'like', "%$produto%");
        })
        ->paginate(__itensPagina());

        return view('transferencia_estoque.index', compact('data'));
    }

    public function create(){
        return view('transferencia_estoque.create');
    }

    public function store(Request $request){
        try{
            $item = TransferenciaEstoque::create([
                'empresa_id' => $request->empresa_id,
                'local_saida_id' => $request->local_saida_id,
                'local_entrada_id' => $request->local_entrada_id,
                'usuario_id' => Auth::user()->id,
                'observacao' => $request->observacao ?? '',
                'codigo_transacao' => Str::random(10)
            ]);

            $localSaida = Localizacao::findOrFail($request->local_saida_id);
            $localEntrada = Localizacao::findOrFail($request->local_entrada_id);

            for($i=0; $i<sizeof($request->produto_id); $i++){
                //valida estoque
                $produto = Produto::findOrFail($request->produto_id[$i]);
                $estoque = Estoque::where('produto_id', $request->produto_id[$i])
                ->where('local_id', $request->local_saida_id)->first();

                if($estoque == null){
                    session()->flash("flash_error", "$produto->nome sem estoque!");
                    return redirect()->back();
                }

                if($estoque->quantidade < __convert_value_bd($request->quantidade[$i])){
                    session()->flash("flash_error", "$produto->nome com estoque insuficiente!");
                    return redirect()->back();
                }
            }

            for($i=0; $i<sizeof($request->produto_id); $i++){
                $qtd = __convert_value_bd($request->quantidade[$i]);
                $itemTransferencia = ItemTransferenciaEstoque::create([
                    'transferencia_id' => $item->id,
                    'produto_id' => $request->produto_id[$i],
                    'quantidade' => $qtd,
                    'observacao' => $request->observacao_item[$i] ?? ''
                ]);

                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $request->produto_id[$i], 
                    'localizacao_id' => $request->local_entrada_id
                ]);

                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $request->produto_id[$i], 
                    'localizacao_id' => $request->local_saida_id
                ]);

                $this->utilEstoque->incrementaEstoque($request->produto_id[$i], $qtd, null, $request->local_entrada_id);
                $this->utilEstoque->reduzEstoque($request->produto_id[$i], $qtd, null, $request->local_saida_id);

                $tipo = 'incremento';
                $codigo_transacao = $itemTransferencia->id;
                $tipo_transacao = 'alteracao_estoque';
                $this->utilEstoque->movimentacaoProduto($request->produto_id[$i], $qtd, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id);

            }
            $descricaoLog = "Saída de $localSaida->nome para $localEntrada->nome";
            __createLog($request->empresa_id, 'Transferência de Estoque', 'cadastrar', $descricaoLog);
            session()->flash("flash_success", "Transferência salva!");

        }catch(\Exception $e){
            __createLog(request()->empresa_id, 'Transferência de Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('transferencia-estoque.index');

    }

    public function destroy($id)
    {
        $item = TransferenciaEstoque::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            foreach($item->itens as $p){
                $this->utilEstoque->incrementaEstoque($p->produto_id, $p->quantidade, null, $item->local_saida_id);
                $this->utilEstoque->reduzEstoque($p->produto_id, $p->quantidade, null, $item->local_entrada_id);

            }
            $item->itens()->delete();
            $item->delete();
            $descricaoLog = "Saída de " . $item->local_saida->nome . " para " . $item->local_entrada->nome;
            __createLog($item->empresa_id, 'Transferência de Estoque', 'excluir', $descricaoLog);

            session()->flash("flash_success", "Transferência removida com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Transferência de Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('transferencia-estoque.index');
    }

    public function imprimir($id)
    {
        $item = TransferenciaEstoque::findOrFail($id);
        __validaObjetoEmpresa($item);

        $empresa = Empresa::findOrFail($item->empresa_id);

        $p = view('transferencia_estoque.print', compact('empresa', 'item'))
        ->with('title', 'Transferência de estoque');

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Transferência de estoque $id.pdf", array("Attachment" => false));
    }

}
