<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\Troca;
use App\Models\Produto;
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
        $nfce = Nfce::findOrFail($request->venda_id);

        try{

            if($request->cliente_id){
                $nfce->cliente_id = $request->cliente_id;
            }

            $troca = Troca::create([
                'empresa_id' => $request->empresa_id,
                'nfce_id' => $request->venda_id,
                'observacao' => '',
                'numero_sequencial' => $this->getLastNumero($request->empresa_id),
                'codigo' => Str::random(8),
                'valor_troca' => __convert_value_bd($request->valor_total),
                'valor_original' => $nfce->total,
                'tipo_pagamento' => $request->tipo_pagamento ? $request->tipo_pagamento : $nfce->tipo_pagamento
            ]);

            $nfce->total = __convert_value_bd($request->valor_total);
            $nfce->save();

            foreach($nfce->itens as $i){
                if ($i->produto->gerenciar_estoque) {
                    $this->util->incrementaEstoque($i->produto_id, $i->quantidade, null, $nfce->local_id);
                }
            }

            if($request->produto_id){
                for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                    $produto_id = $request->produto_id[$i];
                    $quantidade = __convert_value_bd($request->quantidade[$i]);
                    $add = 1;
                    $qtd = 0;
                    foreach($nfce->itens as $itemNfce){
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
                            'troca_id' => $troca->id
                        ]);
                    }
                    $product = Produto::findOrFail($produto_id);
                    if ($product->gerenciar_estoque) {
                        $this->util->reduzEstoque($product->id, $quantidade, null, $troca->nfce->local_id);
                    }
                }
            }

            if($request->valor_credito > 0 && $request->cliente_id && $troca->tipo_pagamento == '00'){
                $cliente = Cliente::findOrFail($request->cliente_id);
                CreditoCliente::create([
                    'valor' => $request->valor_credito,
                    'cliente_id' => $cliente->id
                ]);

                $cliente->valor_credito += $request->valor_credito;
                $cliente->save();
            }
            __createLog($request->empresa_id, 'PDV Troca', 'cadastrar', "#$troca->numero_sequencial - R$ " . __moeda($troca->valor_troca));

            return response()->json($troca, 200);
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'PDV Troca', 'erro', $e->getMessage());
            return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 401);
        }
    }
}
