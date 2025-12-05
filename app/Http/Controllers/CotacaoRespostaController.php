<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cotacao;
use App\Models\Empresa;
use App\Models\ItemCotacao;
use App\Models\FaturaCotacao;
use Illuminate\Support\Facades\DB;

class CotacaoRespostaController extends Controller
{
    public function index($hash_link){
        $cotacao = Cotacao::where('hash_link', $hash_link)->first();

        if($cotacao == null){
            return view('cotacoes.not_found');
        }

        if($cotacao->estado != 'nova'){
            session()->flash("mensagem_erro", "Cotação finalizada!");
            return redirect()->route('cotacoes.finish');
        }
        $empresa = Empresa::findOrFail($cotacao->empresa_id);

        foreach($cotacao->itens as $key => $i){
            $i->descricao_produto = $i->produto->nome;

            if($cotacao->planejamento){
                $itemPlanejamento = $cotacao->planejamento->produtos[$key];
                if($itemPlanejamento){
                    $i->descricao_produto = $itemPlanejamento->descricao();
                }
            }
        }

        return view('cotacoes.resposta', compact('cotacao', 'empresa'));
    }

    public function store(Request $request){
        $cotacao = Cotacao::findOrFail($request->cotacao_id);

        try{

            $nfe = DB::transaction(function () use ($request, $cotacao) {
                $total = 0;
                for($i=0; $i<sizeof($request->subtotal); $i++){
                    $total += __convert_value_bd($request->subtotal[$i]);
                }

                $cotacao->desconto = $request->desconto ? __convert_value_bd($request->desconto) : 0;
                $cotacao->valor_frete = $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0;
                $cotacao->valor_total = $total - $cotacao->desconto + $cotacao->valor_frete;
                $cotacao->observacao_resposta = $request->observacao ?? '';
                $cotacao->observacao_frete = $request->observacao_frete ?? '';
                $cotacao->responsavel = $request->responsavel ?? '';
                $cotacao->estado = 'respondida';
                $cotacao->data_resposta = date('Y-m-d H:i:s');
                $cotacao->previsao_entrega = $request->previsao_entrega;

                $cotacao->save();

                for($i=0; $i<sizeof($request->valor_unitario); $i++){
                    $item = ItemCotacao::findOrFail($request->item_id[$i]);
                    $item->valor_unitario = __convert_value_bd($request->valor_unitario[$i]);
                    $item->sub_total = __convert_value_bd($request->subtotal[$i]);
                    $item->observacao = $request->observacao_item[$i] ?? '';
                    $item->save();
                }

                for($i=0; $i<sizeof($request->data_vencimento); $i++){
                    if($request->data_vencimento[$i] && $request->valor_parcela[$i] && $request->tipo_pagamento[$i]){
                        FaturaCotacao::create([
                            'data_vencimento' => $request->data_vencimento[$i],
                            'tipo_pagamento' => $request->tipo_pagamento[$i],
                            'valor' => __convert_value_bd($request->valor_parcela[$i]),
                            'cotacao_id' => $cotacao->id
                        ]);
                    }
                }
                return $cotacao;
            });
            session()->flash("mensagem_sucesso", "Cotação respondida!");
        }catch(\Exception $e){
            session()->flash("mensagem_erro", "Algo deu errado: " .$e->getMessage());
        }

        return redirect()->route('cotacoes.finish');

    }

    public function finish(){
        return view('cotacoes.finish');
    }
}
