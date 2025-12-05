<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\PreVenda;
use App\Models\ItemPreVenda;
use App\Models\Localizacao;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\FaturaPreVenda;
use App\Models\NaturezaOperacao;
use Illuminate\Support\Str;

class PreVendaController extends Controller
{
    public function index(Request $request){

        $controlaId = $request->id > 0 ? $request->id : 9999999999999;
        $funcionario = Funcionario::where('codigo', $request->codigo_funcionario)
        ->where('empresa_id', $request->empresa_id)
        ->first();

        $data = PreVenda::where('empresa_id', $request->empresa_id)
        ->where('funcionario_id', $funcionario->id)
        ->orderBy('id', 'desc')
        ->with(['cliente', 'itens'])
        ->limit(8)
        ->where('id', '<', $controlaId)
        ->get();

        return response()->json($data, 200);
    }

    public function find(Request $request){

        $item = PreVenda::where('orcamento', 1)
        ->with(['cliente', 'itens', 'fatura'])
        ->findOrFail($request->orcamento_id);

        foreach($item->itens as $i){
            $i->produto_nome = $i->produto->nome;
        }

        return response()->json($item, 200);
    }

    public function destroy(Request $request){
        $item = PreVenda::findOrFail($request->pre_venda_id);
        $item->itens()->delete();
        $item->fatura()->delete();
        $item->delete();
        return response()->json($item, 200);
    }

    public function store(Request $request){
        try{
            $empresa = Empresa::findOrFail($request->empresa_id);
            if($request->cliente_id){
                $cliente = Cliente::find($request->cliente_id);
            }else{
                $cliente = null;
            }
            $funcionario = Funcionario::where('codigo', $request->codigo_funcionario)
            ->where('empresa_id', $empresa->id)
            ->first();
            if($funcionario == null){
                return response()->json("Funcionário não encontrado!", 404);
            }
            if($funcionario->usuario_id == null){
                return response()->json("Vincule o usuário ao funcionário $funcionario->nome", 401);
            }

            $local = Localizacao::where('empresa_id', $empresa->id)
            ->first();
            $naturezaPadrao = NaturezaOperacao::where('empresa_id', $request->empresa_id)
            ->where('padrao', 1)->first();
            if($naturezaPadrao == null){
                $naturezaPadrao = NaturezaOperacao::where('empresa_id', $request->empresa_id)->first();
            }

            $data = [
                'cliente_id' => $cliente ? $cliente->id : null,
                'valor_total' => __convert_value_bd($request->total),
                'desconto' => __convert_value_bd($request->desconto),
                'acrescimo' => __convert_value_bd($request->acrescimo),
                'natureza_id' => $naturezaPadrao->id,
                'local_id' => $local->id,
                'empresa_id' => $empresa->id,
                'codigo' => Str::random(8),
                'funcionario_id' => $funcionario->id,
                'usuario_id' => $funcionario->usuario_id,
            ];

            $preVenda = PreVenda::create($data);
            foreach($request->itens as $i){
                $produto = Produto::findOrFail($i['produto_id']);

                $cfop = 0;
                ItemPreVenda::create([
                    'pre_venda_id' => $preVenda->id,
                    'produto_id' => $produto->id,
                    'quantidade' => __convert_value_bd($i['quantidade']),
                    'valor' => __convert_value_bd($i['valor_unitario']),
                    'cfop' => $cfop,
                    'observacao' => $i['observacao_item'] ?? '',
                ]);
                
            }
            if(sizeof($request->fatura) > 0){
                foreach($request->fatura as $p){
                    FaturaPreVenda::create([
                        'valor_parcela' => __convert_value_bd($p['valor']),
                        'tipo_pagamento' => $p['tipo_pagamento'],
                        'pre_venda_id' => $preVenda->id,
                        'vencimento' => $p['data_vencimento']
                    ]);
                }
            }else{
                // FaturaPreVenda::create([
                //     'valor_parcela' => __convert_value_bd($request->valor_total),
                //     'tipo_pagamento' => $request->tipo_pagamento,
                //     'pre_venda_id' => $preVenda->id,
                //     'vencimento' => $request->data_vencimento
                // ]);
            }
            return response()->json($data, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

}
