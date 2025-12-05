<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\ItemNfe;
use App\Models\FaturaNfe;

use App\Models\Nfce;
use App\Models\ItemNfce;
use App\Models\Caixa;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\CategoriaProduto;
use App\Models\Garantia;
use App\Models\ItemPizzaNfce;
use App\Models\Empresa;
use App\Models\ContaReceber;
use App\Models\Pedido;
use App\Models\FaturaNfce;
use App\Models\ItemPedido;
use App\Models\VendaSuspensa;
use App\Models\ItemAdicional;
use App\Models\ItemAdicionalNfce;
use App\Models\ItemVendaSuspensa;
use App\Models\ItemPizzaPedido;
use App\Models\UsuarioEmissao;
use App\Models\TamanhoPizza;
use Illuminate\Support\Facades\DB;
use App\Utils\EstoqueUtil;
use App\Utils\FilaEnvioUtil;
use App\Models\ConfigGeral;
use App\Models\ImpressoraPedidoProduto;

class PdvMoboController extends Controller
{
    protected $util;
    protected $filaEnvioUtil;

    public function __construct(EstoqueUtil $util, FilaEnvioUtil $filaEnvioUtil)
    {
        $this->util = $util;
        $this->filaEnvioUtil = $filaEnvioUtil;
    }

    public function store(Request $request){
        try {

            $venda = DB::transaction(function () use ($request) {

                $empresa = $config = Empresa::find($request->empresa_id);

                $caixa = Caixa::where('usuario_id', $request->usuario_id)
                ->where('status', 1)
                ->first();

                $config = __objetoParaEmissao($config, $caixa->local_id);

                $numero_nfce = $config->numero_ultima_nfce_producao;
                if ($config->ambiente == 2) {
                    $numero_nfce = $config->numero_ultima_nfce_homologacao;
                }

                $numeroSerieNfce = $config->numero_serie_nfce ? $config->numero_serie_nfce : 1;
                $configUsuarioEmissao = UsuarioEmissao::where('usuario_empresas.empresa_id', request()->empresa_id)
                ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
                ->select('usuario_emissaos.*')
                ->where('usuario_emissaos.usuario_id', $request->usuario_id)
                ->first();

                if($configUsuarioEmissao != null){
                    $numeroSerieNfce = $configUsuarioEmissao->numero_serie_nfce;
                    $numero_nfce = $configUsuarioEmissao->numero_ultima_nfce;
                }

                $tipoPagamento = $request->tipo_pagamento;
                if($request->fatura && sizeof($request->fatura) > 1){
                    $tipoPagamento = '99';
                }

                $cliente = null;
                if($request->cliente_id){
                    $cliente = Cliente::findOrFail($request->cliente_id);
                }

                $dataVenda = [
                    'natureza_id' => $empresa->natureza_id_pdv,
                    'emissor_nome' => $config->nome,
                    'emissor_cpf_cnpj' => $config->cpf_cnpj,
                    'ambiente' => $config->ambiente,
                    'chave' => '',
                    'cliente_id' => $request->cliente_id,
                    'numero_serie' => $numeroSerieNfce,
                    'numero' => $numero_nfce + 1,
                    'cliente_nome' => $cliente ? $cliente->razao_social : '',
                    'cliente_cpf_cnpj' => $request->cliente_cpf_cnpj ?? '',
                    'estado' => 'novo',
                    'total' => ($request->valor_total),
                    'desconto' => $request->desconto ? ($request->desconto) : 0,
                    'valor_cashback' => 0,
                    'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                    'valor_produtos' => ($request->valor_total) ?? 0,
                    'caixa_id' => $caixa->id,
                    'local_id' => $caixa->local_id,
                    'observacao' => $request->observacao,
                    'dinheiro_recebido' => 0,
                    'troco' => $request->troco,
                    'tipo_pagamento' => $tipoPagamento,
                    'cnpj_cartao' => $request->cnpj_cartao ?? '',
                    'bandeira_cartao' => $request->bandeira_cartao ?? '',
                    'cAut_cartao' => $request->cAut_cartao ?? '',
                    'user_id' => $request->usuario_id,
                    'empresa_id' => $request->empresa_id,
                    'valor_entrega' => 0,
                    'numero_sequencial' => $this->getLastNumero($request->empresa_id)
                ];

                $nfce = Nfce::create($dataVenda);

                foreach($request->itens as $i){
                    $i = (object)$i;
                    $product = Produto::findOrFail($i->id);
                    $product = __tributacaoProdutoLocalVenda($product, $caixa->local_id);
                    $variacao_id = null;
                    $itemNfce = ItemNfce::create([
                        'nfce_id' => $nfce->id,
                        'produto_id' => $product->id,
                        'quantidade' => (float)str_replace(",", ".", $i->quantidade),
                        'valor_unitario' => $i->valor_unitario,
                        'sub_total' => $i->subtotal,
                        'observacao' => $i->observacao ?? '',
                        'perc_icms' => __convert_value_bd($product->perc_icms),
                        'perc_pis' => __convert_value_bd($product->perc_pis),
                        'perc_cofins' => __convert_value_bd($product->perc_cofins),
                        'perc_ipi' => __convert_value_bd($product->perc_ipi),
                        'cst_csosn' => $product->cst_csosn,
                        'cst_pis' => $product->cst_pis,
                        'cst_cofins' => $product->cst_cofins,
                        'cst_ipi' => $product->cst_ipi,
                        'cfop' => $product->cfop_estadual,
                        'ncm' => $product->ncm,
                        'variacao_id' => $variacao_id,
                    ]);

                    if ($product->gerenciar_estoque) {
                        $this->util->reduzEstoque($product->id, __convert_value_bd($i->quantidade), $variacao_id, $caixa->local_id);

                        $tipo = 'reducao';
                        $codigo_transacao = $nfce->id;
                        $tipo_transacao = 'venda_nfce';

                        $this->util->movimentacaoProduto($product->id, __convert_value_bd($i->quantidade), $tipo, $codigo_transacao, $tipo_transacao, $request->usuario_id, $variacao_id);
                    }

                    if(isset($i->adicionais_escolhidos)){
                        foreach($i->adicionais_escolhidos as $add){
                            $add = trim($add);
                            if($add){
                                ItemAdicionalNfce::create([
                                    'item_nfce_id' => $itemNfce->id, 
                                    'adicional_id' => $add
                                ]);
                            }
                        }
                    }

                    if(isset($i->sabores_escolhidos)){
                        foreach($i->sabores_escolhidos as $add){
                            $add = trim($add);
                            ItemPizzaNfce::create([
                                'item_nfce_id' => $itemNfce->id,
                                'produto_id' => $add
                            ]);
                        }
                    }

                    if($product->prazo_garantia > 0 && $nfce->cliente_id != null){
                        Garantia::create([
                            'empresa_id' => $request->empresa_id,
                            'cliente_id' => $nfce->cliente_id,
                            'produto_id' => $product->id,
                            'nfce_id' => $nfce->id,
                            'usuario_id' => $request->usuario_id,
                            'prazo_garantia' => $product->prazo_garantia,
                            'data_venda' => date('Y-m-d')
                        ]);
                    }
                }

                if ($request->fatura && $request->fatura[0]['valor'] > 0) {
                    foreach($request->fatura as $key => $f){
                        $f = (object)$f;

                        $dataAtual = date('Y-m-d');
                        $vencimento = $f->data;
                        if($f->data && strtotime($vencimento) > strtotime($dataAtual)){

                            ContaReceber::create([
                                'nfe_id' => null,
                                'nfce_id' => $nfce->id,
                                'cliente_id' => $request->cliente_id,
                                'data_vencimento' => $vencimento,
                                'data_recebimento' => $vencimento,
                                'valor_integral' => __convert_value_bd($f->valor),
                                'valor_recebido' => 0,
                                'status' => 0,
                            // 'descricao' => "Parcela $i+1 da venda código $nfce->id",
                                'descricao' => 'Venda PDV #' . $nfce->numero_sequencial . " Parcela " . ($key+1) . " de " . sizeof($request->fatura),
                                'empresa_id' => $request->empresa_id,
                                'juros' => 0,
                                'multa' => 0,
                                'observacao' => $request->obs_row[$i] ?? '',
                                'tipo_pagamento' => $f->forma,
                                'local_id' => $caixa->local_id,
                                'referencia' => "Pedido PDV " . $nfce->numero_sequencial
                            ]);
                        }

                        FaturaNfce::create([
                            'nfce_id' => $nfce->id,
                            'tipo_pagamento' => $f->forma,
                            'data_vencimento' => $f->data ? $f->data : date('Y-m-d'),
                            'valor' => __convert_value_bd($f->valor)
                        ]);
                    }

                } else {

                    FaturaNfce::create([
                        'nfce_id' => $nfce->id,
                        'tipo_pagamento' => $tipoPagamento,
                        'data_vencimento' => date('Y-m-d'),
                        'valor' => $nfce->total
                    ]);
                }

                if ($request->venda_suspensa_id > 0) {
                    $vendaSuspensa = VendaSuspensa::findOrfail($request->venda_suspensa_id);
                    $vendaSuspensa->itens()->delete();
                    $vendaSuspensa->delete();
                }

                if ($request->pedido_id > 0) {
                    $pedido = Pedido::findOrfail($request->pedido_id);

                    $pedido->status = 0;
                    $pedido->em_atendimento = 0;
                    $pedido->nfce_id = $nfce->id;

                    $mesa = $pedido->_mesa;
                    if($mesa){
                        $mesa->ocupada = 0;
                        $mesa->save();
                    }

                    $pedido->save();

                    // $comandaFinalizada = ItemPedido::where('pedido_id', $pedido->id)
                    // ->where('finalizado_pdv', 0)->first();

                    // if($comandaFinalizada == null){
                    //     $pedido->status = 0;
                    //     $pedido->em_atendimento = 0;
                    //     $pedido->nfce_id = $nfce->id;

                    //     $mesa = $pedido->_mesa;
                    //     $mesa->ocupada = 0;
                    //     $mesa->save();

                    //     $pedido->save();
                    // }
                }

                $this->filaEnvioUtil->adicionaVendaFila($nfce);
                return $nfce;
            });
return response()->json($venda, 200);
} catch (\Exception $e) {
    return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 401);
}
}

private function getLastNumero($empresa_id){
    $last = Nfce::where('empresa_id', $empresa_id)
    ->orderBy('numero_sequencial', 'desc')
    ->where('numero_sequencial', '>', 0)->first();
    $numero = $last != null ? $last->numero_sequencial : 0;
    $numero++;
    return $numero;
}

public function suspender(Request $request)
{

    try {

        $venda = DB::transaction(function () use ($request) {
            $config = Empresa::find($request->empresa_id);
            $caixa = Caixa::where('usuario_id', $request->usuario_id)
            ->where('status', 1)
            ->first();
            $venda = VendaSuspensa::create([
                'empresa_id' => $request->empresa_id,
                'cliente_id' => $request->cliente_id,
                'total' => ($request->valor_total),
                'desconto' => $request->desconto ? ($request->desconto) : 0,
                'acrescimo' => $request->acrescimo ? ($request->acrescimo) : 0,
                'observacao' => $request->observacao,
                'tipo_pagamento' => $request->tipo_pagamento ?? '',
                'local_id' => $caixa->local_id,
                'user_id' => $request->usuario_id
            ]);

            foreach($request->itens as $i){
                $i = (object)$i;
                $product = Produto::findOrFail($i->id);
                ItemVendaSuspensa::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $product->id,
                    'quantidade' => $i->quantidade,
                    'valor_unitario' => $i->valor_unitario,
                    'sub_total' => $i->subtotal,
                    'variacao_id' => null,
                ]);
            }

        });
        return response()->json($venda, 200);

    } catch (\Exception $e) {
        return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 401);
    }
}

public function vendasSuspensa(Request $request){
    $data = VendaSuspensa::where('empresa_id', $request->empresa_id)
    ->orderBy('id', 'desc')
    ->get();

    return view('pdv_mobo.partials.vendas_suspensas', compact('data'))->render();
}

public function vendasDiaria(Request $request){
    $data = Nfce::where('empresa_id', $request->empresa_id)
    ->where('user_id', $request->usuario_id)
    ->orderBy('id', 'desc')
    ->whereDate('created_at', date('Y-m-d'))
    ->get();

    return view('pdv_mobo.partials.vendas_diaria', compact('data'))->render();
}

public function produtosCategoria(Request $request)
{
    $categoria_id = $request->categoria_id;
    $pesquisa = $request->pesquisa;
    if($categoria_id != 0){
        $categoria = CategoriaProduto::findOrfail($categoria_id);
    }

    $mes = date('m');
    $ano = date('Y');

    $query = Produto::where('produtos.empresa_id', $request->empresa_id)
    ->leftJoin('item_nfces', function($join) use ($mes, $ano){
        $join->on('item_nfces.produto_id', '=', 'produtos.id')
        ->whereMonth('item_nfces.created_at', $mes)
        ->whereYear('item_nfces.created_at', $ano);
    })
    ->when(!empty($pesquisa), function ($q) use ($pesquisa) {
        return $q->where('produtos.nome', 'LIKE', "%$pesquisa%");
    })
    ->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
    ->select(
        'produtos.id',
        DB::raw('COALESCE(FLOOR(estoques.quantidade), 0) as estoque_atual'),
        'produtos.numero_sequencial',
        'produtos.codigo_barras',
        'produtos.referencia',
        'produtos.referencia_balanca',
        'produtos.valor_unitario',
        'produtos.nome',
        'produtos.categoria_id',
        'produtos.imagem',
        'produtos.gerenciar_estoque',
        'produtos.valor_atacado',
        'produtos.valor_minimo_venda',
        'produtos.quantidade_atacado',
        DB::raw('COALESCE(SUM(item_nfces.quantidade), 0) as total_vendido_mes')
    )
    ->where('produtos.status', 1)
    ->groupBy(
        'produtos.id',
        'produtos.numero_sequencial',
        'produtos.codigo_barras',
        'produtos.referencia',
        'produtos.referencia_balanca',
        'produtos.valor_unitario',
        'produtos.nome',
        'produtos.categoria_id',
        'produtos.imagem',
        'produtos.gerenciar_estoque',
        'produtos.valor_atacado',
        'produtos.valor_minimo_venda',
        'produtos.quantidade_atacado'
    )
    ->with('adicionais', 'categoria')
    ->orderByDesc('total_vendido_mes')
    ->orderBy('produtos.nome');

    if ($categoria_id != 0) {
        $query->where('produtos.categoria_id', $categoria_id);
    }

    return response()->json($query->get());
}

public function produtosCodigoBarras(Request $request){
    $produto = Produto::where('produtos.empresa_id', $request->empresa_id)
    ->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
    ->select(
        'produtos.id',
        DB::raw('COALESCE(FLOOR(estoques.quantidade), 0) as estoque_atual'),
        'produtos.numero_sequencial',
        'produtos.codigo_barras',
        'produtos.referencia',
        'produtos.referencia_balanca',
        'produtos.valor_unitario',
        'produtos.nome',
        'produtos.categoria_id',
        'produtos.imagem',
        'produtos.gerenciar_estoque',
        'produtos.valor_atacado',
        'produtos.valor_minimo_venda',
        'produtos.quantidade_atacado',
        DB::raw('COALESCE(SUM(item_nfces.quantidade), 0) as total_vendido_mes')
    )
    ->where('produtos.status', 1)
    ->where('codigo_barras', $request->codigo_barras)
    ->with('adicionais')
    ->first();

    if (!$produto) {
        return response()->json(['erro' => 'Produto não encontrado'], 404);
    }

    return response()->json($produto, 200);
}

public function comandas(Request $request){
    $data = Pedido::
    where('empresa_id', $request->empresa_id)
    ->where('status', 1)
    ->orderBy('created_at', 'desc')
    ->orderBy('comanda')
    ->get();

    $abertas = $data->pluck('comanda')->toArray();

    $comandasFechadas = [];

    $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();
    $comandasConfiguradas = 1;
    if($config == null || $config->numero_inicial_comanda == null || $config->numero_final_comanda == null){
        $comandasConfiguradas = 0;
    }else{
        for($i=$config->numero_inicial_comanda; $i<=$config->numero_final_comanda; $i++){
            if(!in_array($i, $abertas)){
                $comandasFechadas[] = [
                    'numero' => $i,
                    'total' => 0,
                ];
            }
        }
    }

    return view('pdv_mobo.partials.comandas', compact('data', 'comandasConfiguradas', 'comandasFechadas'));
}

public function updateComanda(Request $request){
    $item = Pedido::findOrFail($request->pedido_id);
    $item->cliente_id = $request->cliente_id;
    $item->total = $request->total;
    if($request->cliente_id){
        $cliente = Cliente::findOrFail($request->cliente_id);
        $item->cliente_nome = $cliente->razao_social;
        $item->cliente_fone = $cliente->telefone;
    }

    if($request->itens && sizeof($request->itens) > 0){

        $itensAnterior = $item->itens;

        foreach($item->itens as $it){
            $it->adicionais()->delete();
            $it->pizzas()->delete();
            $it->delete();
        }

        foreach($request->itens as $i){
            $impresso = $this->validaItemImpressao($i['id'], $item['subtotal'], $item['quantidade'], $item['observacao'], $itensAnterior);

            $dataItem = [
                'pedido_id' => $item->id,
                'produto_id' => $i['id'],
                'observacao' => $i['observacao'] ?? '',
                'quantidade' => $i['quantidade'],
                'valor_unitario' => $i['valor_unitario'],
                'sub_total' => $i['subtotal'],
                'estado' => 0,
                'tamanho_id' => null,
                'impresso' => $impresso
            ];

            $itemPedido = ItemPedido::create($dataItem);
            $adicionais = $i['adicionais_escolhidos'] ?? [];

            if(sizeof($adicionais) > 0){
                foreach($adicionais as $add){
                    $add = trim($add);
                    if($add){
                        ItemAdicional::create([
                            'item_pedido_id' => $itemPedido->id, 
                            'adicional_id' => $add
                        ]);
                    }
                }
            }

            $sabores = $i['sabores_escolhidos'] ?? [];

            if(sizeof($sabores) > 0){
                foreach($sabores as $add){
                    $add = trim($add);
                    if($add){
                        ItemPizzaPedido::create([
                            'item_pedido_id' => $itemPedido->id, 
                            'produto_id' => $add
                        ]);
                    }
                }
            }
        }

    }

    $item->save();
    return response()->json("ok", 200);
}

private function validaItemImpressao($produto_id, $sub_total, $quantidade, $observacao, $itensAnterior){

    $imprime = ImpressoraPedidoProduto::where('produto_id', $produto_id)->first();
    if($imprime == null) return 1;

    foreach($itensAnterior as $i){
        // if($i->produto_id == $produto_id && $i->sub_total == (float)$sub_total && $i->quantidade == (float)$quantidade){
        if($i->produto_id == $produto_id && $i->sub_total == (float)$sub_total && $i->quantidade == (float)$quantidade && $i->observacao == $observacao){
            return 1;
        }
    }
    return 0;
}

public function saboresTamanhos(Request $request){
    $tamanhosPizza = TamanhoPizza::where('empresa_id', $request->empresa_id)
    ->where('status', 1)
    ->get();

    $produtoPrincipal = Produto::with('pizzaValores')
    ->where('id', $request->produto_id)
    ->first();

    $outrosProdutos = Produto::with('pizzaValores')
    ->whereHas('categoria', function ($q) {
        $q->where('tipo_pizza', 1);
    })
    ->where('id', '!=', $request->produto_id)
    ->get();

    $produtos = collect([$produtoPrincipal])->merge($outrosProdutos);

    $sabores_escolhidos = json_decode($request->sabores_escolhidos);

    $sabores = [];
    foreach($produtos as $p){
        $sabores[] = [
            'id' => $p->id,
            'nome' => $p->nome,
            'img' => $p->img ?? null,
            'valores' => $p->pizzaValores ?? [],
            'selecionado' => in_array($p->id, $sabores_escolhidos)
        ];
    }

    return view('pdv_mobo.partials.sabores', compact('tamanhosPizza', 'sabores'));
}

public function storeNfe(Request $request){
    try {

        $venda = DB::transaction(function () use ($request) {

            $empresa = $config = Empresa::find($request->empresa_id);

            $caixa = Caixa::where('usuario_id', $request->usuario_id)
            ->where('status', 1)
            ->first();

            $config = __objetoParaEmissao($config, $caixa->local_id);

            $numero_nfe = $config->numero_ultima_nfe_producao;
            if ($config->ambiente == 2) {
                $numero_nfe = $config->numero_ultima_nfe_homologacao;
            }

            $numeroSerieNfe = $config->numero_serie_nfe ? $config->numero_serie_nfe : 1;

            $tipoPagamento = $request->tipo_pagamento;
            if($request->fatura && sizeof($request->fatura) > 1){
                $tipoPagamento = '99';
            }

            $cliente = null;
            if(!$request->cliente_id){
                return response()->json("Selecione o cliente!", 401);
            }
            $cliente = Cliente::findOrFail($request->cliente_id);

            $dataVenda = [
                'natureza_id' => $empresa->natureza_id_pdv,
                'emissor_nome' => $config->nome,
                'emissor_cpf_cnpj' => $config->cpf_cnpj,
                'ambiente' => $config->ambiente,
                'chave' => '',
                'cliente_id' => $request->cliente_id,
                'numero_serie' => $numeroSerieNfce,
                'numero' => $numero_nfce + 1,
                'cliente_nome' => $cliente ? $cliente->razao_social : '',
                'cliente_cpf_cnpj' => $request->cliente_cpf_cnpj ?? '',
                'estado' => 'novo',
                'total' => ($request->valor_total),
                'desconto' => $request->desconto ? ($request->desconto) : 0,
                'valor_cashback' => 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'valor_produtos' => ($request->valor_total) ?? 0,
                'caixa_id' => $caixa->id,
                'local_id' => $caixa->local_id,
                'observacao' => $request->observacao,
                'dinheiro_recebido' => 0,
                'troco' => $request->troco,
                'tipo_pagamento' => $tipoPagamento,
                'cnpj_cartao' => $request->cnpj_cartao ?? '',
                'bandeira_cartao' => $request->bandeira_cartao ?? '',
                'cAut_cartao' => $request->cAut_cartao ?? '',
                'user_id' => $request->usuario_id,
                'empresa_id' => $request->empresa_id,
                'valor_entrega' => 0,
                'numero_sequencial' => $this->getLastNumero($request->empresa_id)
            ];

            $nfe = Nfe::create($dataVenda);
            return $nfe;
        });
        return response()->json($venda, 200);
    } catch (\Exception $e) {
        return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 401);
    }
}

}
