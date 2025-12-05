<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\Troca;
use App\Models\Produto;
use App\Models\Caixa;
use App\Models\ItemTroca;
use App\Models\Cliente;
use App\Models\CreditoCliente;
use Illuminate\Support\Str;
use App\Utils\EstoqueUtil;

class TrocaController extends Controller
{

    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;
    }

    private function getLastNumero($empresa_id){
        $last = Troca::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    public function store(Request $request){
        if($request->tipo == 'nfce'){
            $item = Nfce::findOrFail($request->venda_id);
        }else{
            $item = Nfe::findOrFail($request->venda_id);
        }

        try{

            if($request->cliente_id){
                $item->cliente_id = $request->cliente_id;
            }

            $caixa = Caixa::where('usuario_id', $request->usuario_id)
            ->where('status', 1)
            ->first();

            $troca = Troca::create([
                'empresa_id' => $request->empresa_id,
                'nfce_id' => $request->tipo == 'nfce' ? $item->id : null,
                'nfe_id' => $request->tipo == 'nfe' ? $item->id : null,
                'caixa_id' => $caixa->id,
                'observacao' => '',
                'numero_sequencial' => $this->getLastNumero($request->empresa_id),
                'codigo' => Str::random(8),
                'valor_troca' => __convert_value_bd($request->valor_total),
                'valor_original' => $item->total,
                'tipo_pagamento' => $request->tipo_pagamento ? $request->tipo_pagamento : $item->tipo_pagamento
            ]);

            $item->total = __convert_value_bd($request->valor_total);
            $item->save();

            foreach($item->itens as $i){
                if ($i->produto->gerenciar_estoque) {
                    $this->util->incrementaEstoque($i->produto_id, $i->quantidade, null, $item->local_id);
                }
            }

            if($request->produto_id){
                for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                    $produto_id = $request->produto_id[$i];
                    $quantidade = __convert_value_bd($request->quantidade[$i]);
                    $add = 1;
                    $qtd = 0;
                    foreach($item->itens as $itemNfce){
                        if($itemNfce->produto_id == $produto_id && $itemNfce->quantidade == $quantidade){
                            $add = 0;
                        }else{
                            if($itemNfce->produto_id == $produto_id && $itemNfce->quantidade != $quantidade){
                                $quantidade -= $itemNfce->quantidade;
                            }
                        }
                    }

                    if($add == 1){
                        ItemTroca::create([
                            'produto_id' => $produto_id,
                            'quantidade' => $quantidade,
                            'troca_id' => $troca->id,
                            'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
                            'sub_total' => __convert_value_bd($request->subtotal_item[$i]),
                        ]);
                    }
                    $product = Produto::findOrFail($produto_id);
                    if ($product->gerenciar_estoque) {
                        $this->util->reduzEstoque($product->id, $quantidade, null, $item->local_id);
                    }
                }
            }

            if($request->valor_credito > 0 && $request->cliente_id){
                $cliente = Cliente::findOrFail($request->cliente_id);
                CreditoCliente::create([
                    'valor' => $request->valor_credito,
                    'cliente_id' => $cliente->id,
                    'troca_id' => $troca->id,
                    'status' => 1
                ]);

                $cliente->valor_credito += __convert_value_bd($request->valor_credito);
                $cliente->save();
            }
            __createLog($request->empresa_id, 'Troca', 'cadastrar', "#$troca->numero_sequencial - R$ " . __moeda($troca->valor_troca));

            return response()->json($troca, 200);
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Troca', 'erro', $e->getMessage());
            return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 401);
        }
    }
}
