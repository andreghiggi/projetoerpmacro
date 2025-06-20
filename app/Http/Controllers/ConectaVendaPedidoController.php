<?php

namespace App\Http\Controllers;

use App\Models\ConectaVendaConfig;
use App\Models\ConectaVendaPedido;
use App\Models\Produto;
use App\Utils\ConectaVendaUtil;
use App\Utils\EstoqueUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConectaVendaPedidoController extends Controller
{

    protected $util;
    public function __construct(ConectaVendaUtil $util, EstoqueUtil $estoqueUtil)
    {
        $this->util = $util;
        $this->estoqueUtil = $estoqueUtil;
    }

    public function index(Request $request)
    {
        $config = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();
        if(!$config){
            return redirect()->route('conecta-venda-config.index');
        }

        $this->util->listOrders($request);

        $start_date = $request->get('start_date');

        $id_publico = $request->get('id_publico');
        $data = ConectaVendaPedido::where('empresa_id', $request->empresa_id)
            ->orderBy('id', 'desc')
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_criacao', '>=', $start_date);
            })
            ->when(!empty($id_publico), function ($query) use ($id_publico) {
                return $query->where('id', 'LIKE', "%$id_publico%");
            })
            ->paginate(30);

        return view('conecta_venda_pedidos.index', compact('data'));
    }

    public function show($id)
    {

        $produtos = Produto::whereNotNull('conecta_venda_id')
            ->pluck('nome', 'id');
        $pedido = ConectaVendaPedido::findOrFail($id);
        return view('conecta_venda_pedidos.show', compact('pedido', 'produtos'));
    }

    public function finishOrder($id, Request $request)
    {
        $pedido = ConectaVendaPedido::with('itens.produto')->findOrFail($id);
        $config = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();

        if (in_array($pedido->status, ['finalizado', 'cancelado'])) {
            return redirect()->back()->with('error', 'Este pedido já está finalizado ou cancelado.');
        }

        DB::beginTransaction();
        try {

            foreach ($pedido->itens as $item) {
                $produto = Produto::where('id', $item->produto_id)->with('variacoes')->first();
                $variacaoId = $produto->variacoes[0]->id ?? null;
                $quantidade = $item->quantidade;
                if (!$produto) {
                    throw new \Exception("Produto do item {$item->id} não encontrado.");
                }

                // 1) Reduz estoque se o produto gerencia estoque
                if ($produto->gerenciar_estoque) {
                    $this->estoqueUtil->reduzEstoque(
                        $produto->id,
                        $quantidade,
                        $variacaoId,
                        $request->local_id ?? null
                    );
                }

                // 2) Registra a movimentação de estoque
                $this->estoqueUtil->movimentacaoProduto(
                    $produto->id,
                    $quantidade,
                    'reducao',
                    $pedido->id,
                    'pedido_conecta',
                    auth()->id(),
                    $variacaoId
                );
            }

            // 3) Atualiza status no Conecta Venda
            $this->util->updateOrderStatus(
                $config,
                $pedido->id,
                'finalizado'
            );

            // 4) Atualiza status local
            $pedido->situacao = 'finalizado';
            $pedido->save();

            DB::commit();
            return redirect()->back()->with('success', 'Pedido finalizado e estoque atualizado com sucesso!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao finalizar pedido: ' . $e->getMessage());
        }
    }

}
