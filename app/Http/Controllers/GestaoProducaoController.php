<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GestaoCustoProducao;
use App\Models\GestaoCustoProducaoProduto;
use App\Models\GestaoCustoProducaoServico;
use App\Models\GestaoCustoProducaoOutroCusto;
use App\Models\Produto;
use App\Models\Cliente;
use App\Utils\EstoqueUtil;

class GestaoProducaoController extends Controller
{
    protected $estoqueUtil;

    public function __construct(EstoqueUtil $estoqueUtil)
    {
        $this->estoqueUtil = $estoqueUtil;

        $this->middleware('permission:gestao_producao_view', ['only' => ['create', 'store']]);
        $this->middleware('permission:gestao_producao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:gestao_producao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:gestao_producao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $produto_id = $request->get('produto_id');
        $status = $request->get('status');

        $data = GestaoCustoProducao::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($produto_id), function ($query) use ($produto_id) {
            return $query->where('produto_id', $produto_id);
        })
        ->when($status != "", function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $produto = null;
        $cliente = null;
        if($produto_id){
            $produto = Produto::findOrFail($produto_id);
        }
        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }

        return view('gestao_producao.index', compact('data', 'produto', 'cliente'));
    }

    public function create(){
        $produtos = Produto::where('empresa_id', request()->empresa_id)
        ->where('composto', 1)->orderBy('nome')->get();

        return view('gestao_producao.create', compact('produtos'));
    }

    public function edit($id){
        $item = GestaoCustoProducao::findOrFail($id);
        __validaObjetoEmpresa($item);

        $produtos = Produto::where('empresa_id', request()->empresa_id)
        ->where('composto', 1)->orderBy('nome')->get();

        $disponibilidade = 0;
        foreach($item->produto->composicao as $c){
            if($c->ingrediente->estoque){
                $d = $c->ingrediente->estoque->quantidade/$c->quantidade;
                if($d < $disponibilidade || $disponibilidade == 0){
                    // $disponibilidade = $d;
                    $disponibilidade = number_format($d, 3, '.', '');
                }
            }
        }
        return view('gestao_producao.edit', compact('item', 'produtos', 'disponibilidade'));
    }

    public function show($id){
        $item = GestaoCustoProducao::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('gestao_producao.show', compact('item'));
    }

    private function setNumeroSequencial(){

        $last = GestaoCustoProducao::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;

        return $numero;
    }

    public function store(Request $request){
        // dd($request->all());
        try{

            $item = GestaoCustoProducao::create([
                'numero_sequencial' => $this->setNumeroSequencial(),
                'empresa_id' => $request->empresa_id,
                'produto_id' => $request->produto_composto_id,
                'quantidade' => $request->quantidade,
                'cliente_id' => $request->cliente_id ?? null,
                'total_custo_servicos' => __convert_value_bd($request->total_custo_servicos),
                'total_custo_produtos' => __convert_value_bd($request->total_custo_produtos),
                'desconto' => __convert_value_bd($request->desconto),
                'frete' => __convert_value_bd($request->frete),
                'total_final' => __convert_value_bd($request->total_final),
                'status' => 0,
                'usuario_id' => \Auth::user()->id,
            ]);

            for($i=0; $i<sizeof($request->produto_id); $i++){
                GestaoCustoProducaoProduto::create([
                    'gestao_custo_id' => $item->id,
                    'produto_id' => $request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade_produto[$i]),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario_produto[$i]),
                    'sub_total' => __convert_value_bd($request->sub_total_produto[$i]),
                    'observacao' => $request->observacao_produto[$i] ?? ''

                ]);
            }

            if($request->servico_id){
                for($i=0; $i<sizeof($request->servico_id); $i++){
                    GestaoCustoProducaoServico::create([
                        'gestao_custo_id' => $item->id,
                        'servico_id' => $request->servico_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_servico[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_servico[$i]),
                        'observacao' => $request->observacao_servico[$i] ?? ''

                    ]);
                }
            }

            if($request->descricao_outros[0] != ''){
                for($i=0; $i<sizeof($request->descricao_outros); $i++){
                    GestaoCustoProducaoOutroCusto::create([
                        'gestao_custo_id' => $item->id,
                        'descricao' => $request->descricao_outros[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_outros[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_outros[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_outros[$i]),
                        'observacao' => $request->observacao_outros[$i] ?? ''

                    ]);
                }
            }

            session()->flash('flash_success', 'Gestão de produção cadastrada com sucesso!');
            return redirect()->route('gestao-producao.index');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir o cadastro ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function update(Request $request, $id){
        // dd($request->all());
        try{
            $item = GestaoCustoProducao::findOrFail($id);
            __validaObjetoEmpresa($item);
            $data = [
                // 'produto_id' => $request->produto_composto_id,
                'quantidade' => $request->quantidade,
                'cliente_id' => $request->cliente_id ?? null,
                'total_custo_servicos' => __convert_value_bd($request->total_custo_servicos),
                'total_custo_produtos' => __convert_value_bd($request->total_custo_produtos),
                'desconto' => __convert_value_bd($request->desconto),
                'frete' => __convert_value_bd($request->frete),
                'total_final' => __convert_value_bd($request->total_final),
            ];

            $item->fill($data)->update();

            $item->produtos()->delete();
            $item->servicos()->delete();
            $item->outros()->delete();

            for($i=0; $i<sizeof($request->produto_id); $i++){
                GestaoCustoProducaoProduto::create([
                    'gestao_custo_id' => $item->id,
                    'produto_id' => $request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade_produto[$i]),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario_produto[$i]),
                    'sub_total' => __convert_value_bd($request->sub_total_produto[$i]),
                    'observacao' => $request->observacao_produto[$i] ?? ''

                ]);
            }

            if($request->servico_id){
                for($i=0; $i<sizeof($request->servico_id); $i++){
                    GestaoCustoProducaoServico::create([
                        'gestao_custo_id' => $item->id,
                        'servico_id' => $request->servico_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_servico[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_servico[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_servico[$i]),
                        'observacao' => $request->observacao_servico[$i] ?? ''

                    ]);
                }
            }

            if($request->descricao_outros[0]){
                for($i=0; $i<sizeof($request->descricao_outros); $i++){
                    GestaoCustoProducaoOutroCusto::create([
                        'gestao_custo_id' => $item->id,
                        'descricao' => $request->descricao_outros[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_outros[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario_outros[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total_outros[$i]),
                        'observacao' => $request->observacao_outros[$i] ?? ''

                    ]);
                }
            }

            session()->flash('flash_success', 'Gestão de produção atualizada com sucesso!');
            return redirect()->route('gestao-producao.index');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir o atualização ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $item = GestaoCustoProducao::findOrFail($id);
        __validaObjetoEmpresa($item);

        try {

            $item->produtos()->delete();
            $item->servicos()->delete();
            
            $item->delete();
            session()->flash("flash_success", "Gestão de produção removida!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function finish(Request $request, $id){
        $item = GestaoCustoProducao::findOrFail($id);

        $item->status = 1;

        foreach($item->produtos as $p){
            if($p->produto->gerenciar_estoque){
                $this->estoqueUtil->reduzEstoque($p->produto_id, $p->quantidade, null);
            }
        }

        $this->estoqueUtil->incrementaEstoque($item->produto_id, $item->quantidade, null);

        $tipo = 'incremento';
        $codigo_transacao = $item->id;
        $tipo_transacao = 'alteracao_estoque';
        $this->estoqueUtil->movimentacaoProduto($item->produto_id, $item->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, null);

        $produto = $item->produto;
        $produto->valor_compra = $item->total_final/$item->quantidade;
        $produto->save();
        $item->save();

        session()->flash('flash_success', 'Gestão de produção finalizada!');
        return redirect()->route('gestao-producao.index');
    }
}
