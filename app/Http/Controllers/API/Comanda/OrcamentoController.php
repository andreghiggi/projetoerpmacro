<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\ItemNfe;
use App\Models\Produto;
use App\Models\NaturezaOperacao;
use App\Models\ConfigGeral;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Localizacao;
use App\Models\FaturaNfe;
use App\Models\ListaPreco;

class OrcamentoController extends Controller
{
    public function index(Request $request){

        $controlaId = $request->id > 0 ? $request->id : 9999999999999;
        $funcionario = Funcionario::where('codigo', $request->codigo_funcionario)
        ->where('empresa_id', $request->empresa_id)
        ->first();

        $data = Nfe::where('empresa_id', $request->empresa_id)
        ->where('orcamento', 1)
        ->where('funcionario_id', $funcionario->id)
        ->orderBy('id', 'desc')
        ->with(['cliente', 'itens'])
        ->limit(8)
        ->where('id', '<', $controlaId)
        ->get();

        return response()->json($data, 200);
    }

    public function find(Request $request){

        $item = Nfe::where('orcamento', 1)
        ->with(['cliente', 'itens', 'fatura'])
        ->findOrFail($request->orcamento_id);

        foreach($item->itens as $i){
            $i->produto_nome = $i->produto->nome;
        }

        return response()->json($item, 200);
    }

    public function produtos(Request $request){

        $data = Produto::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->orderBy('nome')
        ->with(['categoria', 'variacoes', 'estoque'])
        ->select('id', 'nome', 'valor_unitario', 'categoria_id', 'codigo_barras', 'unidade', 'referencia',
           'valor_prazo', 'gerenciar_estoque', 'imagem')
        ->get();

        return response()->json($data, 200);
    }

    public function listasDePreco(Request $request){

        $data = ListaPreco::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->orderBy('nome')
        ->with(['itens'])
        ->get();

        return response()->json($data, 200);
    }

    public function tiposDePagamento(Request $request){
        $tiposPagamento = Nfe::tiposPagamento();
        $data = [];
        foreach($tiposPagamento as $key => $t){
            $data[] = [
                'indice' => $key,
                'valor' => $t
            ];
        }
        return response()->json($data, 200);
    }

    public function dadosFuncionario(Request $request){
        $item = Funcionario::where('codigo', $request->codigo_funcionario)
        ->where('empresa_id', $request->empresa_id)
        ->first();

        $config = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();
        $item->app_valor_aprazo = 0;
        if($config != null){
            $item->app_valor_aprazo = $config->app_valor_aprazo;
        }
        return response()->json($item, 200);

    }

    public function store(Request $request){
        try{
            $empresa = Empresa::findOrFail($request->empresa_id);
            $cliente = Cliente::find($request->cliente_id);
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

            $last = Nfe::where('empresa_id', $request->empresa_id)
            ->orderBy('numero_sequencial', 'desc')
            // ->where('orcamento', 0)
            ->where('numero_sequencial', '>', 0)->first();
            $numero = $last != null ? $last->numero_sequencial : 0;
            $numero++;

            $data = [
                'cliente_id' => $cliente->id,
                'total' => __convert_value_bd($request->total),
                'desconto' => __convert_value_bd($request->desconto),
                'acrescimo' => __convert_value_bd($request->acrescimo),
                'tpNF' => 1,
                'orcamento' => 1,
                'tipo' => 9,
                'natureza_id' => $naturezaPadrao->id,
                'estado' => 'novo',
                'local_id' => $local->id,
                'empresa_id' => $empresa->id,
                'numero_sequencial' => $numero,
                'funcionario_id' => $funcionario->id,
                'ambiente' => $empresa->ambiente
            ];
            // return response()->json($data, 200);

            $nfe = Nfe::create($data);
            foreach($request->itens as $i){
                $produto = Produto::findOrFail($i['produto_id']);
                $variacao_id = $i['variacao_id'];
                if($empresa->cidade->uf == $cliente->cidade->uf){
                    $cfop = $produto->cfop_estadual;
                }else{
                    $cfop = $produto->cfop_outro_estado;
                }
                $item = [
                    'nfe_id' => $nfe->id,
                    'produto_id' => $i['produto_id'],
                    'quantidade' => __convert_value_bd($i['quantidade']),
                    'valor_unitario' => __convert_value_bd($i['valor_unitario']),
                    'sub_total' => __convert_value_bd($i['sub_total']),

                    'perc_icms' => $produto->perc_icms,
                    'perc_pis' => $produto->perc_pis,
                    'perc_cofins' => $produto->perc_cofins,
                    'perc_ipi' => $produto->perc_ipi,
                    'cst_csosn' => $produto->cst_csosn,
                    'cst_pis' => $produto->cst_pis,
                    'cst_cofins' => $produto->cst_cofins,
                    'cst_ipi' => $produto->cst_ipi,
                    'perc_red_bc' => $produto->perc_red_bc,
                    'cfop' => $cfop,
                    'ncm' => $produto->ncm,
                    'codigo_beneficio_fiscal' => $produto->codigo_beneficio_fiscal,
                    'variacao_id' => $variacao_id,
                    'cEnq' => $produto->cEnq,
                    'xPed' => '',
                    'nItemPed' => '',
                    'infAdProd' => $i['observacao_item'],
                ];
                ItemNfe::create($item);
            }

            foreach($request->fatura as $p){
                FaturaNfe::create([
                    'nfe_id' => $nfe->id,
                    'tipo_pagamento' => $p['tipo_pagamento'],
                    'data_vencimento' => $p['data_vencimento'],
                    'valor' => __convert_value_bd($p['valor'])
                ]);
            }
            return response()->json($data, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

    public function update(Request $request, $id){
        $nfe = Nfe::findOrFail($id);
        $empresa = Empresa::findOrFail($request->empresa_id);
        $cliente = Cliente::findOrFail($request->cliente_id);
        $data = [
            'cliente_id' => $request->cliente_id,
            'total' => __convert_value_bd($request->total),
            'desconto' => __convert_value_bd($request->desconto),
            'acrescimo' => __convert_value_bd($request->acrescimo),
        ];

        $nfe->fill($data)->save();

        $nfe->itens()->delete();
        foreach($request->itens as $i){
            $produto = Produto::findOrFail($i['produto_id']);
            $variacao_id = null;
            if($empresa->cidade->uf != $cliente->cidade->uf){
                $cfop = $produto->cfop_estadual;
            }else{
                $cfop = $produto->cfop_outro_estado;
            }
            $item = [
                'nfe_id' => $nfe->id,
                'produto_id' => $i['produto_id'],
                'quantidade' => __convert_value_bd($i['quantidade']),
                'valor_unitario' => __convert_value_bd($i['valor_unitario']),
                'sub_total' => __convert_value_bd($i['sub_total']),

                'perc_icms' => $produto->perc_icms,
                'perc_pis' => $produto->perc_pis,
                'perc_cofins' => $produto->perc_cofins,
                'perc_ipi' => $produto->perc_ipi,
                'cst_csosn' => $produto->cst_csosn,
                'cst_pis' => $produto->cst_pis,
                'cst_cofins' => $produto->cst_cofins,
                'cst_ipi' => $produto->cst_ipi,
                'perc_red_bc' => $produto->perc_red_bc,
                'cfop' => $cfop,
                'ncm' => $produto->ncm,
                'codigo_beneficio_fiscal' => $produto->codigo_beneficio_fiscal,
                'variacao_id' => $variacao_id,
                'cEnq' => $produto->cEnq,
                'xPed' => '',
                'nItemPed' => '',
                'infAdProd' => $i['observacao_item'],
            ];
            ItemNfe::create($item);
        }

        $nfe->fatura()->delete();

        foreach($request->fatura as $p){
            FaturaNfe::create([
                'nfe_id' => $nfe->id,
                'tipo_pagamento' => $p['tipo_pagamento'],
                'data_vencimento' => $p['data_vencimento'],
                'valor' => __convert_value_bd($p['valor'])
            ]);
        }
        return response()->json($item, 200);
    }

    public function destroy(Request $request){
        $item = Nfe::findOrFail($request->orcamento_id);
        $item->itens()->delete();
        $item->fatura()->delete();
        $item->delete();
        return response()->json($item, 200);

    }
}
