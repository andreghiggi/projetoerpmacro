<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Caixa;
use App\Models\ComissaoVenda;
use App\Models\ContaReceber;
use App\Models\Empresa;
use App\Models\FaturaNfce;
use App\Models\MargemComissao;
use App\Models\ItemServicoNfce;
use App\Models\ItemNfce;
use App\Models\ItemPedido;
use App\Models\VendaSuspensa;
use App\Models\ItemVendaSuspensa;
use App\Models\Localizacao;
use App\Models\Agendamento;
use App\Models\CashBackCliente;
use App\Models\Cliente;
use App\Models\Estoque;
use App\Models\Pedido;
use App\Models\ItemListaPreco;
use App\Models\PedidoDelivery;
use App\Models\MotoboyComissao;
use App\Models\ItemPedidoDelivery;
use App\Models\Nfce;
use App\Models\ConfigGeral;
use App\Models\ProdutoVariacao;
use App\Models\Produto;
use App\Models\CategoriaProduto;
use App\Models\Marca;
use App\Models\UsuarioAcesso;
use App\Models\CashBackConfig;
use App\Models\ProdutoTributacaoLocal;
use App\Models\Funcionario;
use App\Models\UsuarioEmissao;
use App\Utils\EstoqueUtil;
use Dflydev\DotAccessData\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Utils\WhatsAppUtil;
use App\Models\RegistroTef;

class FrontBoxController extends Controller
{
    protected $util;
    protected $utilWhatsApp;

    public function __construct(EstoqueUtil $util, WhatsAppUtil $utilWhatsApp)
    {
        $this->util = $util;
        $this->utilWhatsApp = $utilWhatsApp;
    }

    public function gerarFatura(Request $request){
        try {

            $entrada_fatura = $request->entrada_fatura;
            $tipo_pagamento = $request->tipo_pagamento_fatura;
            $parcelas_fatura = $request->parcelas_fatura;
            $intervalo_fatura = $request->intervalo_fatura;
            $primeiro_vencimento_fatura = $request->primeiro_vencimento_fatura;
            $total = $request->total;

            if($primeiro_vencimento_fatura == ''){
                $primeiro_vencimento_fatura = date('Y-m-d');
            }

            $somaFatura = $total;
            if($request->entrada_fatura){
                $somaFatura -= __convert_value_bd($entrada_fatura);
                $parcelas_fatura--;
            }

            $valorParcela = $somaFatura/$parcelas_fatura;
            $valorParcela = (float)number_format($valorParcela, 2, '.', '');
            if($request->entrada_fatura){
                $parcelas_fatura++;
            }

            $data = [];
            $somaLoop = 0;
            for($i=0; $i<$parcelas_fatura; $i++){
                if($i == 0){
                    $vencimento = $primeiro_vencimento_fatura;
                }else{
                    $vencimento = date('Y-m-d', strtotime($vencimento. " + $intervalo_fatura days"));
                }

                $p['vencimento'] = $vencimento;

                if($request->entrada_fatura > 0 && $i == 0){
                    $p['valor'] = __convert_value_bd($request->entrada_fatura);
                    $somaLoop += __convert_value_bd($request->entrada_fatura);
                    
                }else{

                    if($i == $parcelas_fatura-1){
                        $p['valor'] = $total - $somaLoop;
                    }else{
                        $p['valor'] = $valorParcela;
                        $somaLoop += $valorParcela;
                    }
                }


                array_push($data, $p);
            }
            // return response()->json($tipo_pagamento, 401);

            return view('front_box.partials.row_fatura', compact('data', 'tipo_pagamento'))->render();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function gerarFaturaPdv(Request $request){
        try {

            $entrada_fatura = $request->entrada_fatura;
            $tipo_pagamento = $request->tipo_pagamento_fatura;
            $parcelas_fatura = $request->parcelas_fatura;
            $intervalo_fatura = $request->intervalo_fatura;
            $primeiro_vencimento_fatura = $request->primeiro_vencimento_fatura;
            $total = $request->total;

            if($primeiro_vencimento_fatura == ''){
                $primeiro_vencimento_fatura = date('Y-m-d');
            }

            $somaFatura = $total;
            if($request->entrada_fatura){
                $somaFatura -= __convert_value_bd($entrada_fatura);
                $parcelas_fatura--;
            }

            $valorParcela = $somaFatura/$parcelas_fatura;
            $valorParcela = (float)number_format($valorParcela, 2, '.');
            $parcelas_fatura++;

            $data = [];
            $somaLoop = 0;
            for($i=0; $i<$parcelas_fatura; $i++){
                if($i == 0){
                    $vencimento = $primeiro_vencimento_fatura;
                }else{
                    $vencimento = date('Y-m-d', strtotime($vencimento. " + $intervalo_fatura days"));
                }

                $p['vencimento'] = $vencimento;

                if($request->entrada_fatura && $i == 0){
                    $p['valor'] = __convert_value_bd($request->entrada_fatura);
                    $somaLoop += __convert_value_bd($request->entrada_fatura);
                    
                }else{
                    if($i == $parcelas_fatura-1){
                        $p['valor'] = $total - $somaLoop;
                    }else{
                        $p['valor'] = $valorParcela;
                        $somaLoop += $valorParcela;
                    }
                }


                array_push($data, $p);
            }
            // return response()->json($tipo_pagamento, 401);

            return view('front_box.partials.row_fatura_pdv', compact('data', 'tipo_pagamento'))->render();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }
    public function gerarFaturaPdv2(Request $request){
        try {

            $entrada_fatura = $request->entrada_fatura;
            $tipo_pagamento = $request->tipo_pagamento_fatura;
            $parcelas_fatura = $request->parcelas_fatura;
            $intervalo_fatura = $request->intervalo_fatura;
            $primeiro_vencimento_fatura = $request->primeiro_vencimento_fatura;
            $total = $request->total;

            if($primeiro_vencimento_fatura == ''){
                $primeiro_vencimento_fatura = date('Y-m-d');
            }

            $somaFatura = $total;
            if($request->entrada_fatura){
                $somaFatura -= __convert_value_bd($entrada_fatura);
                $parcelas_fatura--;
            }

            $valorParcela = $somaFatura/$parcelas_fatura;
            $valorParcela = (float)number_format($valorParcela, 2, '.');
            $parcelas_fatura++;

            $data = [];
            $somaLoop = 0;
            for($i=0; $i<$parcelas_fatura; $i++){
                if($i == 0){
                    $vencimento = $primeiro_vencimento_fatura;
                }else{
                    $vencimento = date('Y-m-d', strtotime($vencimento. " + $intervalo_fatura days"));
                }

                $p['vencimento'] = $vencimento;

                if($request->entrada_fatura && $i == 0){
                    $p['valor'] = __convert_value_bd($request->entrada_fatura);
                    $somaLoop += __convert_value_bd($request->entrada_fatura);
                    
                }else{
                    if($i == $parcelas_fatura-1){
                        $p['valor'] = $total - $somaLoop;
                    }else{
                        $p['valor'] = $valorParcela;
                        $somaLoop += $valorParcela;
                    }
                }


                array_push($data, $p);
            }
            // return response()->json($tipo_pagamento, 401);
            $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
            $tiposPagamento = Nfce::tiposPagamento();
        // dd($tiposPagamento);
            if($config != null){
                $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
                $temp = [];
                if(sizeof($config->tipos_pagamento_pdv) > 0){
                    foreach($tiposPagamento as $key => $t){
                        if(in_array($t, $config->tipos_pagamento_pdv)){
                            $temp[$key] = $t;
                        }
                    }
                    $tiposPagamento = $temp;
                }
            }
            return view('front_box.partials_form2.row_fatura_pdv2', compact('data', 'tipo_pagamento', 'tiposPagamento'))->render();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function linhaProdutoVenda(Request $request)
    {
        try {

            $qtd = $request->qtd;
            $value_unit = __convert_value_bd($request->value_unit);
            $sub_total = __convert_value_bd($request->sub_total);
            $product_id = $request->product_id;
            $variacao_id = $request->variacao_id;
            $key = $request->key;
            $local_id = $request->local_id;

            $variacao = null;
            if($variacao_id){
                $variacao = ProdutoVariacao::findOrfail($variacao_id);
            }

            $product = Produto::findOrFail($product_id);
            if ($product->gerenciar_estoque == true) {
                if($product->combo){
                    $estoqueMsg = $this->util->verificaEstoqueCombo($product, (float)$qtd);
                    if($estoqueMsg != ""){
                        return response()->json($estoqueMsg, 401);
                    }
                }else{
                    $estoque = Estoque::where('produto_id', $product->id)
                    ->when($variacao_id != null, function ($q) use ($variacao_id) {
                        return $q->where('produto_variacao_id', $variacao_id);
                    })
                    ->where('local_id', $local_id)->first();
                    if ($estoque == null) {
                        return response()->json("Produto sem estoque", 401);
                    } else if ($estoque->quantidade < $qtd) {
                        return response()->json("Produto com estoque insuficiente", 401);
                    }
                }
            }
            return view('front_box.partials.row_frontBox', 
                compact('product', 'qtd', 'value_unit', 'sub_total', 'key', 'variacao_id', 'variacao'));
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function linhaProdutoVendaAdd(Request $request)
    {
        $product = Produto::findOrFail($request->id);
        $lista_id = $request->lista_id;
        $local_id = $request->local_id;

        if($product->variacao_modelo_id){
            return response("produto com variação", 402);
        }
        $qtd = __convert_value_bd($request->qtd);
        try{
            $qtd = (float)$qtd+1;
        }catch(\Exception $e){

        }
        try {

            $product = __tributacaoProdutoLocalVenda($product, $local_id);

            if($lista_id){
                $itemLista = ItemListaPreco::where('lista_id', $lista_id)
                ->where('produto_id', $product->id)
                ->first();
                if($itemLista != null){
                    $product->valor_unitario = $itemLista->valor;
                }
            }

            $value_unit = $product->valor_unitario;
            $sub_total = $product->valor_unitario;
            $variacao_id = $request->variacao_id;
            $product_id = $product->id;

            // $key = $request->key;
            if ($product->gerenciar_estoque == true) {
                if($product->combo){
                    $estoqueMsg = $this->util->verificaEstoqueCombo($product, (float)$qtd);
                    if($estoqueMsg != ""){
                        return response()->json($estoqueMsg, 401);
                    }
                }else{

                    $estoque = Estoque::where('produto_id', $product->id)
                    ->where('local_id', $local_id)->first();
                    if ($estoque == null) {
                        return response()->json("Produto sem estoque", 401);
                    } else if ($estoque->quantidade < $qtd) {
                        return response()->json("Produto com estoque insuficiente", 401);
                    }
                }
            }
            $variacao = null;
            // die;
            $qtd = __moeda($qtd);
            return view('front_box.partials.row_frontBox', 
                compact('product', 'qtd', 'value_unit', 'sub_total', 'variacao_id', 'variacao'));
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function linhaParcelaVenda(Request $request)
    {
        try {
            $tipo_pagamento_row = $request->tipo_pagamento_row;
            $data_vencimento_row = $request->data_vencimento_row;
            $valor_integral_row = $request->valor_integral_row;
            $quantidade = $request->quantidade;
            $obs_row = $request->obs_row;

            $tipo = Nfce::getTipoPagamento($tipo_pagamento_row);
            return view('front_box.partials.row_pagamento_multiplo', compact(
                'valor_integral_row',
                'data_vencimento_row',
                'quantidade',
                'tipo',
                'obs_row',
                'tipo_pagamento_row'
            ));
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    private function rateioCashBack($valor_cashback, $nfce){
        $data = CashBackCliente::where('empresa_id', $nfce->empresa_id)
        ->where('status', 1)
        ->where('cliente_id', $nfce->cliente_id)
        ->get();

        $cliente = Cliente::findOrFail($nfce->cliente_id);
        $cliente->valor_cashback -= $valor_cashback;
        $cliente->save();

        $soma = 0;
        foreach($data as $i){
            if($soma < $valor_cashback){
                $valorCredito = $i->valor_credito;
                if($valorCredito <= $valor_cashback){
                    $i->status = 0;
                    $i->valor_credito = 0;
                    $i->save();
                    $soma += $valorCredito;
                }else{
                    $i->valor_credito -= ($valor_cashback - $soma);
                    $i->save();
                    $soma = $valor_cashback;
                }
            }
        }
    }

    private function saveCashBack($venda){
        $config = CashBackConfig::where('empresa_id', $venda->empresa_id)
        ->first();
        if($venda->cliente && $config != null){

            if($venda->total >= $config->valor_minimo_venda){
                $valor_percentual = $config->valor_percentual;
                $dias_expiracao = $config->dias_expiracao;

                $valor_credito = $venda->total * ($valor_percentual/100);
                $data = [
                    'empresa_id' => $venda->empresa_id,
                    'cliente_id' => $venda->cliente_id,
                    'tipo' => 'pdv',
                    'venda_id' => $venda->id,
                    'valor_venda' => $venda->total,
                    'valor_credito' => $valor_credito,
                    'valor_percentual' => $valor_percentual,
                    'status' => 1,
                    'data_expiracao' => date('Y-m-d', strtotime("+$dias_expiracao days"))
                ];
                $cashBackCliente = CashBackCliente::create($data);

                $cliente = $venda->cliente;
                $cliente->valor_cashback = $cliente->valor_cashback + $valor_credito;
                $cliente->save();

                $this->sendWhatsMessage($cashBackCliente, $venda->local_id);
            }
        }
    }

    private function sendWhatsMessage($cashBackCliente, $local_id){

        if($cashBackCliente->cliente->telefone != ''){
            $config = CashBackConfig::where('empresa_id', $cashBackCliente->cliente->empresa_id)
            ->first();

            $message = $config->mensagem_padrao_whatsapp;
            $telefone = "55".preg_replace('/[^0-9]/', '', $cashBackCliente->cliente->telefone);

            $nomeCliente = $cashBackCliente->cliente->razao_social;
            if($cashBackCliente->cliente->nome_fantasia != ''){
                $nomeCliente = $cashBackCliente->cliente->nome_fantasia;
            }

            $message = str_replace("{credito}", __moeda($cashBackCliente->valor_credito), $message);
            $message = str_replace("{expiracao}", __data_pt($cashBackCliente->data_expiracao, 0), $message);
            $message = str_replace("{nome}", $nomeCliente, $message);

            // $retorno = $this->utilWhatsApp->sendMessage($telefone, $message, $cashBackCliente->cliente->empresa_id);
            $retorno = $this->utilWhatsApp->sendMessageWithLocal($telefone, $message, $local_id);
        }
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
                    'total' => __convert_value_bd($request->valor_total),
                    'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                    'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                    'observacao' => $request->observacao,
                    'tipo_pagamento' => $request->tipo_pagamento ?? '',
                    'local_id' => $caixa->local_id,
                    'user_id' => $request->usuario_id
                ]);

                if($request->produto_id){
                    for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                        $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;
                        ItemVendaSuspensa::create([
                            'venda_id' => $venda->id,
                            'produto_id' => (int)$request->produto_id[$i],
                            'quantidade' => __convert_value_bd($request->quantidade[$i]),
                            'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
                            'sub_total' => __convert_value_bd($request->subtotal_item[$i]),
                            'variacao_id' => $variacao_id,
                        ]);
                    }
                }

            });
            return response()->json($venda, 200);

        } catch (\Exception $e) {
            return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 401);
        }
    }

    public function vendasSuspensas(Request $request){
        $data = VendaSuspensa::where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')
        ->get();

        return view('front_box.partials.vendas_suspensas', compact('data'))->render();
    }

    private function getLastNumero($empresa_id){
        $last = Nfce::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    private function validaCreditoCliente($request){
        if($request->cliente_id == null){
            return 0;
        }

        if(!isset($request->tipo_pagamento_row) && $request->tipo_pagamento != '06'){
            return 0;
        }
        $cliente = Cliente::findOrFail($request->cliente_id);
        $faturaPrazo = 0;
        $total = 0;

        if($request->tipo_pagamento_row){
            for ($i = 0; $i < sizeof($request->tipo_pagamento_row); $i++) {
                $vencimento = $request->data_vencimento_row[$i];
                $dataAtual = date('Y-m-d');
                if(strtotime($vencimento) > strtotime($dataAtual)){
                    $faturaPrazo = 1;
                    $total += __convert_value_bd($request->valor_integral_row[$i]);
                }
            }
        }

        if($faturaPrazo == 0 && $request->tipo_pagamento == '06'){
            $faturaPrazo = 1;
        }

        if($faturaPrazo == 0){
            return 0;
        }

        if($cliente->limite_credito == null || $cliente->limite_credito == 0){
            return "Cliente sem limite de crédito definido!";
        }

        $somaPendente = ContaReceber::where('cliente_id', $cliente->id)
        ->where('status', 0)->sum('valor_integral');
        if($total == 0){
            $total = __convert_value_bd($request->valor_total);
        }
        $somaPendente += $total;
        if($somaPendente > $cliente->limite_credito){
            return "Limite de crédito do cliente ultrapassou em R$ " . __moeda($somaPendente-$cliente->limite_credito) . 
            "\nTotal de crédito definido para este cliente R$ " . __moeda($cliente->limite_credito);
        }
        return 0;
    }

    public function store(Request $request)
    {

        try {

            $retornoCredito = $this->validaCreditoCliente($request);
            if($retornoCredito != 0){
                return response()->json($retornoCredito, 401);
            }

            $nfce = DB::transaction(function () use ($request) {
                // $caixa = __isCaixaAberto();
                $config = Empresa::find($request->empresa_id);
                $caixa = Caixa::where('usuario_id', $request->usuario_id)
                ->where('status', 1)
                ->first();

                $numero_nfce = $config->numero_ultima_nfce_producao;
                if ($config->ambiente == 2) {
                    $numero_nfce = $config->numero_ultima_nfce_homologacao;
                }

                if(isset($request->valor_cashback) && $request->valor_cashback > 0){
                    // $request->desconto = __convert_value_bd($request->valor_cashback);
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
                
                $request->merge([
                    'natureza_id' => $config->natureza_id_pdv,
                    'emissor_nome' => $config->nome,
                    'emissor_cpf_cnpj' => $config->cpf_cnpj,
                    'ambiente' => $config->ambiente,
                    'chave' => '',
                    'cliente_id' => $request->cliente_id,
                    'numero_serie' => $numeroSerieNfce,
                    'lista_id' => $request->lista_id,
                    'numero' => $numero_nfce + 1,
                    'cliente_nome' => $request->cliente_nome ?? '',
                    'cliente_cpf_cnpj' => $request->cliente_cpf_cnpj ?? '',
                    'estado' => 'novo',
                    'total' => __convert_value_bd($request->valor_total),
                    'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                    'valor_cashback' => $request->valor_cashback ? __convert_value_bd($request->valor_cashback) : 0,
                    'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                    'valor_produtos' => __convert_value_bd($request->valor_total) ?? 0,
                    'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                    'caixa_id' => $caixa->id,
                    'local_id' => $caixa->local_id,
                    'observacao' => $request->observacao ?? '',
                    'dinheiro_recebido' => $request->valor_recebido ? __convert_value_bd($request->valor_recebido) : 0,
                    'troco' => $request->troco ? __convert_value_bd($request->troco) : 0,
                    'tipo_pagamento' => $request->tipo_pagamento_row ? '99' : $request->tipo_pagamento,
                    'cnpj_cartao' => $request->cnpj_cartao ?? '',
                    'bandeira_cartao' => $request->bandeira_cartao ?? '',
                    'cAut_cartao' => $request->cAut_cartao ?? '',
                    'user_id' => $request->usuario_id,
                    'valor_entrega' => isset($request->valor_entrega) ? $request->valor_entrega : 0,
                    'numero_sequencial' => $this->getLastNumero($request->empresa_id)
                ]);
                $nfce = Nfce::create($request->all());
                if($request->produto_id){
                    for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                        $product = Produto::findOrFail($request->produto_id[$i]);
                        $product = __tributacaoProdutoLocalVenda($product, $caixa->local_id);
                        $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;
                        ItemNfce::create([
                            'nfce_id' => $nfce->id,
                            'produto_id' => (int)$request->produto_id[$i],
                            'quantidade' => __convert_value_bd($request->quantidade[$i]),
                            'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
                        // 'valor_custo' => $product->valor_compra,
                            'sub_total' => __convert_value_bd($request->subtotal_item[$i]),
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
                            $this->util->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $caixa->local_id);
                        }

                        $tipo = 'reducao';
                        $codigo_transacao = $nfce->id;
                        $tipo_transacao = 'venda_nfce';

                        $this->util->movimentacaoProduto($product->id, __convert_value_bd($request->quantidade[$i]), $tipo, $codigo_transacao, $tipo_transacao, $request->usuario_id, $variacao_id);
                    }
                }

                if($request->servico_id){
                    for ($i = 0; $i < sizeof($request->servico_id); $i++) {
                        ItemServicoNfce::create(
                            [
                                'nfce_id' => $nfce->id,
                                'servico_id' => $request->servico_id[$i],
                                'quantidade' => __convert_value_bd($request->quantidade_servico[$i]),
                                'valor_unitario' => __convert_value_bd($request->valor_unitario_servico[$i]),
                                'sub_total' => __convert_value_bd($request->valor_unitario_servico[$i]) * __convert_value_bd($request->quantidade_servico[$i]),
                                'observacao' => ''
                            ]
                        );
                    }
                }

                if($request->agendamento_id){
                    $agendamento = Agendamento::findOrFail($request->agendamento_id);
                    $agendamento->status = 1;
                    $agendamento->nfce_id = $nfce->id;
                    $agendamento->save();
                }

                if(isset($request->tef_hash) && $request->tef_hash){
                    $registroTef = RegistroTef::where('hash',  $request->tef_hash)->first();
                    if($registroTef){
                        $registroTef->nfce_id = $nfce->id;
                        $registroTef->save();
                    }
                }

                if ($request->tipo_pagamento == '06') {
                    ContaReceber::create([
                        'nfe_id' => null,
                        'nfce_id' => $nfce->id,
                        'data_vencimento' => $request->data_vencimento,
                        'data_recebimento' =>  $request->data_vencimento,
                        'valor_integral' => __convert_value_bd($request->valor_total),
                        'valor_recebido' => 0,
                        'referencia' => $request->referencia,
                        'status' => 0,
                        'empresa_id' => $request->empresa_id,
                        'cliente_id' => $request->cliente_id,
                        'tipo_pagamento' => $request->tipo_pagamento,
                        'observacao' => $request->observacao,
                        'local_id' => $caixa->local_id,
                    ]);
                }

                if ($request->tipo_pagamento_row) {
                    for ($i = 0; $i < sizeof($request->tipo_pagamento_row); $i++) {
                        // if ($request->tipo_pagamento_row[$i] == '06') {
                        $vencimento = $request->data_vencimento_row[$i];
                        $dataAtual = date('Y-m-d');
                        if(strtotime($vencimento) > strtotime($dataAtual)){
                            ContaReceber::create([
                                'nfe_id' => null,
                                'nfce_id' => $nfce->id,
                                'cliente_id' => $request->cliente_id,
                                'data_vencimento' => $request->data_vencimento_row[$i],
                                'data_recebimento' => $request->data_vencimento_row[$i],
                                'valor_integral' => __convert_value_bd($request->valor_integral_row[$i]),
                                'valor_recebido' => 0,
                                'status' => 0,
                                'referencia' => "Parcela $i+1 da venda código $nfce->id",
                                'empresa_id' => $request->empresa_id,
                                'juros' => 0,
                                'multa' => 0,
                                'observacao' => $request->obs_row[$i] ?? '',
                                'tipo_pagamento' => $request->tipo_pagamento_row[$i],
                                'local_id' => $caixa->local_id
                            ]);
                        }
                        // }
                    }
                    for ($i = 0; $i < sizeof($request->tipo_pagamento_row); $i++) {
                        if ($request->tipo_pagamento_row[$i]) {
                            FaturaNfce::create([
                                'nfce_id' => $nfce->id,
                                'tipo_pagamento' => $request->tipo_pagamento_row[$i],
                                'data_vencimento' => $request->data_vencimento_row[$i],
                                'valor' => __convert_value_bd($request->valor_integral_row[$i])
                            ]);
                        }
                    }
                } else {
                    FaturaNfce::create([
                        'nfce_id' => $nfce->id,
                        'tipo_pagamento' => $request->tipo_pagamento,
                        'data_vencimento' => date('Y-m-d'),
                        'valor' => __convert_value_bd($request->valor_total)
                    ]);
                }

                if ($request->funcionario_id != null) {
                    $funcionario = Funcionario::findOrFail($request->funcionario_id);
                    $comissao = $funcionario->comissao;
                    $valorRetorno = $this->calcularComissaoVenda($nfce, $comissao, $request->empresa_id);

                    if($valorRetorno > 0){
                        ComissaoVenda::create([
                            'funcionario_id' => $request->funcionario_id,
                            'nfe_id' => null,
                            'nfce_id' => $nfce->id,
                            'tabela' => 'nfce',
                            'valor' => $valorRetorno,
                            'valor_venda' => __convert_value_bd($request->valor_total),
                            'status' => 0,
                            'empresa_id' => $request->empresa_id
                        ]);
                    }
                }
                if(isset($request->valor_cashback) && $request->valor_cashback == 0 && $request->permitir_credito){
                    $this->saveCashBack($nfce);
                }else{
                    if(isset($request->valor_cashback) && $request->valor_cashback > 0){
                        // $cliente = $nfce->cliente;
                        // if($cliente != null){
                        //     $cliente->valor_cashback -= $request->valor_cashback;
                        //     $cliente->save();
                        // }
                        $this->rateioCashBack($request->valor_cashback, $nfce);

                    }
                }

                if($request->valor_credito){
                    $cliente = $nfce->cliente;
                    $cliente->valor_credito -= __convert_value_bd($request->valor_credito);
                    $cliente->save();
                }

                if (isset($request->pedido_id)) {
                    $pedido = Pedido::findOrfail($request->pedido_id);
                    $pedido->status = 0;
                    $pedido->em_atendimento = 0;
                    $pedido->nfce_id = $nfce->id;

                    ItemPedido::where('pedido_id', $pedido->id)
                    ->update([ 'estado' => 'finalizado' ]);
                    $pedido->save();
                }

                if (isset($request->pedido_delivery_id)) {
                    $pedido = PedidoDelivery::findOrfail($request->pedido_delivery_id);
                    $pedido->estado = 'finalizado';
                    $pedido->finalizado = 1;
                    $pedido->horario_entrega = date('H:i');

                    $pedido->nfce_id = $nfce->id;

                    if($pedido->motoboy_id){
                        MotoboyComissao::create([
                            'empresa_id' => $request->empresa_id,
                            'pedido_id' => $pedido->id,
                            'motoboy_id' => $pedido->motoboy_id,
                            'valor' => $pedido->comissao_motoboy,
                            'valor_total_pedido' => __convert_value_bd($request->valor_total),
                            'status' => 0
                        ]);
                    }
                    $this->sendMessageWhatsApp($pedido, "Seu pedido foi concluído e logo sera entregue!", $nfce->local_id);
                    ItemPedidoDelivery::where('pedido_id', $pedido->id)
                    ->update([ 'estado' => 'finalizado' ]);
                    $pedido->save();
                }


                if (isset($request->venda_suspensa_id)) {
                    $vendaSuspensa = VendaSuspensa::findOrfail($request->venda_suspensa_id);
                    $vendaSuspensa->itens()->delete();
                    $vendaSuspensa->delete();
                }

                return $nfce;
            });
// return response()->json($nfce, 401);
__createLog($request->empresa_id, 'PDV', 'cadastrar', "#$nfce->numero_sequencial - R$ " . __moeda($nfce->total));

return response()->json($nfce, 200);
} catch (\Exception $e) {
    __createLog($request->empresa_id, 'PDV', 'erro', $e->getMessage());
    return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 401);
}
}

public function update(Request $request, $id){
    try{

        $nfce = DB::transaction(function () use ($request, $id) {
            $item = Nfce::findOrFail($id);
            $config = Empresa::find($request->empresa_id);

            $numero_nfce = $config->numero_ultima_nfce_producao;
            if ($config->ambiente == 2) {
                $numero_nfce = $config->numero_ultima_nfce_homologacao;
            }

            $caixa = Caixa::where('usuario_id', $request->usuario_id)
            ->where('status', 1)
            ->first();
            $request->merge([
                'natureza_id' => $config->natureza_id_pdv,
                'emissor_nome' => $config->nome,
                'emissor_cpf_cnpj' => $config->cpf_cnpj,
                'ambiente' => $config->ambiente,
                'cliente_id' => $request->cliente_id,
                'numero_serie' => $config->numero_serie_nfce,
                'lista_id' => $request->lista_id,
                'numero' => $numero_nfce + 1,
                'cliente_nome' => $request->cliente_nome ?? '',
                'cliente_cpf_cnpj' => $request->cliente_cpf_cnpj ?? '',
                'estado' => 'novo',
                'total' => __convert_value_bd($request->valor_total),
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'valor_cashback' => $request->valor_cashback ? __convert_value_bd($request->valor_cashback) : 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'valor_produtos' => __convert_value_bd($request->valor_total) ?? 0,
                'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                'observacao' => $request->observacao,
                'dinheiro_recebido' => $request->valor_recebido ? __convert_value_bd($request->valor_recebido) : 0,
                'troco' => $request->troco ? __convert_value_bd($request->troco) : 0,
                'tipo_pagamento' => isset($request->tipo_pagamento_row[0]) ? $request->tipo_pagamento_row[0] : $request->tipo_pagamento,
                'cnpj_cartao' => $request->cnpj_cartao ?? '',
                'bandeira_cartao' => $request->bandeira_cartao ?? '',
                'cAut_cartao' => $request->cAut_cartao ?? '',
            ]);

            $item->fill($request->all())->save();

            if($request->produto_id){
                foreach($item->itens as $i){
                    if ($i->produto->gerenciar_estoque) {
                        $this->util->incrementaEstoque($i->produto->id, $i->quantidade, $i->variacao_id, $item->local_id);
                    }
                }
                $item->itens()->delete();
                for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                    $product = Produto::findOrFail($request->produto_id[$i]);
                    $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;
                    ItemNfce::create([
                        'nfce_id' => $item->id,
                        'produto_id' => (int)$request->produto_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),

                        'sub_total' => __convert_value_bd($request->subtotal_item[$i]),
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
                        $this->util->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $item->local_id);
                    }

                    $tipo = 'reducao';
                    $codigo_transacao = $item->id;
                    $tipo_transacao = 'venda_nfce';

                    $this->util->movimentacaoProduto($product->id, __convert_value_bd($request->quantidade[$i]), $tipo, $codigo_transacao, $tipo_transacao, $request->usuario_id);
                }
            }

            if ($request->tipo_pagamento_row) {
                $item->fatura()->delete();
                $item->contaReceber()->delete();
                for ($i = 0; $i < sizeof($request->tipo_pagamento_row); $i++) {

                    $vencimento = $request->data_vencimento_row[$i];
                    $dataAtual = date('Y-m-d');
                    if(strtotime($vencimento) > strtotime($dataAtual)){
                        ContaReceber::create([
                            'nfe_id' => null,
                            'nfce_id' => $item->id,
                            'cliente_id' => $request->cliente_id,
                            'data_vencimento' => $request->data_vencimento_row[$i],
                            'data_recebimento' => $request->data_vencimento_row[$i],
                            'valor_integral' => __convert_value_bd($request->valor_integral_row[$i]),
                            'valor_recebido' => 0,
                            'status' => 0,
                            'referencia' => "Parcela $i+1 da venda código $item->id",
                            'empresa_id' => $request->empresa_id,
                            'juros' => 0,
                            'multa' => 0,
                            'observacao' => $request->obs_row[$i] ?? '',
                            'tipo_pagamento' => $request->tipo_pagamento_row[$i],
                            'local_id' => $item->local_id
                        ]);
                    }

                }
                for ($i = 0; $i < sizeof($request->tipo_pagamento_row); $i++) {
                    if ($request->tipo_pagamento_row[$i]) {
                        FaturaNfce::create([
                            'nfce_id' => $item->id,
                            'tipo_pagamento' => $request->tipo_pagamento_row[$i],
                            'data_vencimento' => $request->data_vencimento_row[$i],
                            'valor' => __convert_value_bd($request->valor_integral_row[$i])
                        ]);
                    }
                }
            } else {
                $item->fatura()->delete();
                FaturaNfce::create([
                    'nfce_id' => $item->id,
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'data_vencimento' => date('Y-m-d'),
                    'valor' => __convert_value_bd($request->valor_total)
                ]);
            }

            if ($request->funcionario_id != null) {

                $comissao = ComissaoVenda::where('empresa_id', $item->empresa_id)
                ->where('nfce_id', $item->id)->first();
                if($comissao){
                    $comissao->delete();
                }
                $funcionario = Funcionario::findOrFail($request->funcionario_id);
                $comissao = $funcionario->comissao;
                $valorRetorno = $this->calcularComissaoVenda($item, $comissao, $request->empresa_id);

                if($valorRetorno > 0){
                    ComissaoVenda::create([
                        'funcionario_id' => $request->funcionario_id,
                        'nfe_id' => null,
                        'nfce_id' => $item->id,
                        'tabela' => 'nfce',
                        'valor' => $valorRetorno,
                        'valor_venda' => __convert_value_bd($request->valor_total),
                        'status' => 0,
                        'empresa_id' => $request->empresa_id
                    ]);
                }
            }
            return $item;
        });
__createLog($request->empresa_id, 'PDV', 'editar', "#$nfce->numero_sequencial - R$ " . __moeda($nfce->total));

return response()->json($nfce, 200);

} catch (\Exception $e) {
    __createLog($request->empresa_id, 'PDV', 'erro', $e->getMessage());
    return response()->json($e->getMessage() . ", line: " . $e->getLine() . ", file: " . $e->getFile(), 401);
}
}

private function sendMessageWhatsApp($pedido, $message, $local_id){
    $telefone = "55".preg_replace('/[^0-9]/', '', $pedido->cliente->telefone);
    // $retorno = $this->utilWhatsApp->sendMessage($telefone, $texto, $pedido->empresa_id);
    $retorno = $this->utilWhatsApp->sendMessageWithLocal($telefone, $message, $local_id);
    // dd($retorno);
}

private function calcularComissaoVenda($nfce, $comissao, $empresa_id)
{
    $valorRetorno = 0;
    $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

    $tipoComissao = 'percentual_vendedor';
    if($config != null && $config->tipo_comissao == 'percentual_margem'){
        $tipoComissao = 'percentual_margem';
    }
    if($tipoComissao == 'percentual_vendedor'){
        $valorRetorno = ($nfce->total * $comissao) / 100;
    }else{
        foreach ($nfce->itens as $i) {

            $percentualLucro = ((($i->produto->valor_compra-$i->valor_unitario)/$i->produto->valor_compra)*100)*-1;
            $margens = MargemComissao::where('empresa_id', request()->empresa_id)->get();
            $margemComissao = null;
            $dif = 0;
            $difAnterior = 100;
            foreach($margens as $m){
                $margem = $m->margem;
                if($percentualLucro >= $margem){
                    $dif = $percentualLucro - $margem;
                    if($dif < $difAnterior){
                        $margemComissao = $m;
                        $difAnterior = $dif;
                    }
                }
            }
            if($margemComissao){
                $valorRetorno += ($i->sub_total * $margemComissao->percentual) / 100;
            }
        }
    }
    return $valorRetorno;
}

public function buscaFuncionario($id)
{
    $item = Funcionario::findOrFail($id);
    return response()->json($item, 200);
}

public function categoriasPage(Request $request){
    if($request->ajax()){
        $categorias = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->where('categoria_id', null)
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->paginate(4);
        return view('front_box.partials_form2.categorias', compact('categorias'))->render();
    }
}

public function marcasPage(Request $request){
    if($request->ajax()){
        $marcas = Marca::where('empresa_id', $request->empresa_id)
        ->orderBy('nome', 'asc')
        ->paginate(4);
        return view('front_box.partials_form2.marcas', compact('marcas'))->render();
    }
}

public function produtosPage(Request $request){
    if($request->ajax()){
        $categoria_id = $request->categoria_id;
        $marca_id = $request->marca_id;
        $lista_id = $request->lista_id;
        $local_id = $request->local_id;

        $produtos = Produto::select('produtos.*', \DB::raw('sum(quantidade) as quantidade'))
        ->where('empresa_id', $request->empresa_id)
        ->where('produtos.status', 1)
        ->leftJoin('item_nfces', 'item_nfces.produto_id', '=', 'produtos.id')
        ->groupBy('produtos.id')
        ->where('status', 1)
        ->orderBy('quantidade', 'desc')
        ->when($categoria_id, function ($query) use ($categoria_id) {
            return $query->where('produtos.categoria_id', $categoria_id);
        })
        ->when($marca_id, function ($query) use ($marca_id) {
            return $query->where('produtos.marca_id', $marca_id);
        })
        ->when($lista_id, function ($query) use ($lista_id) {
            return $query->join('item_lista_precos', 'item_lista_precos.produto_id', '=', 'produtos.id');
        })
        ->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
        ->where('produto_localizacaos.localizacao_id', $local_id)
        ->paginate(12);

        return view('front_box.partials_form2.produtos', compact('produtos', 'lista_id', 'local_id'))->render();
    }
}

public function addProduto(Request $request){
    try{

        $qtd = __convert_value_bd($request->qtd);
        $local_id = $request->local_id;
        $variacao = null;
        try{
            $qtd = (float)$qtd+1;
        }catch(\Exception $e){

        }
        if(isset($request->variacao_id) && $request->variacao_id != null){
            $variacao = ProdutoVariacao::findOrfail($request->variacao_id);
        }
        if($variacao == null){
            $item = Produto::findOrFail($request->produto_id);
        }else{
            $item = $variacao->produto;
            $item->valor_unitario = $variacao->valor;
            $item->nome .= " " .$variacao->descricao;
        }

        if ($item->gerenciar_estoque == true) {
            if($item->combo){
                $estoqueMsg = $this->util->verificaEstoqueCombo($item, (float)$qtd);
                if($estoqueMsg != ""){
                    return response()->json($estoqueMsg, 401);
                }
            }else{

                $estoque = Estoque::where('produto_id', $item->id)
                ->where('local_id', $local_id)->first();
                if ($estoque == null) {
                    return response()->json("Produto sem estoque", 401);
                } else if ($estoque->quantidade < $qtd) {
                    return response()->json("Produto com estoque insuficiente", 401);
                }
                // if (!isset($item->estoque)) {
                //     return response()->json("Produto com estoque insuficiente", 401);
                // } else if ($item->estoque->quantidade < $qtd) {
                //     return response()->json("Produto com estoque insuficiente", 401);
                // }
            }
        }
        $item = __tributacaoProdutoLocalVenda($item, $local_id);

        $lista_id = $request->lista_id;
        if($lista_id){

            $itemLista = ItemListaPreco::where('lista_id', $lista_id)
            ->where('produto_id', $request->produto_id)
            ->first();
            if($itemLista != null){
                $item->valor_unitario = $itemLista->valor;
            }
        }
        $code = rand(0,9999999999);
        return view('front_box.partials_form2.item_venda_row', compact('item', 'code'))->render();
    }catch(\Exception $e){
        return response()->json("Produto não econtrado!", 404);
    }
}

public function pesquisaProduto(Request $request){
    try{
        
        $data = Produto::
        where('empresa_id', $request->empresa_id)
        ->when(!is_numeric($request->pesquisa), function ($q) use ($request) {
            // return $q->where('nome', 'LIKE', "%$request->pesquisa%");
            return $q->where(function($query) use ($request)
            {
                return $query->where('nome', 'LIKE', "%$request->pesquisa%")
                ->orWhere('referencia', 'LIKE', "%$request->pesquisa%");
            });
        })
        ->where('status', 1)
        ->when(is_numeric($request->pesquisa), function ($q) use ($request) {
            return $q->where(function($query) use ($request)
            {
                return $query->where('codigo_barras', 'LIKE', "%$request->pesquisa%")
                ->orWhere('codigo_barras2', 'LIKE', "%$request->pesquisa%")
                ->orWhere('codigo_barras3', 'LIKE', "%$request->pesquisa%")
                ->orWhere('numero_sequencial', 'LIKE', "%$request->pesquisa%");
            });
        })
        ->get();

        $countLocais = Localizacao::where('empresa_id', $request->empresa_id)
        ->where('status', 1)->count();

        if($countLocais > 1){
            foreach($data as $p){
                $p = __tributacaoProdutoLocalVenda($p, $request->local_id);
            }
        }

        return view('front_box.partials_form2.pesquisa', compact('data'))->render();
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 404);
    }
}

public function editItem(Request $request){
    try{
        $item = Produto::findOrFail($request->produto_id);
        $code = $request->code;
        $quantidade = $request->quantidade;
        $valor_unitario = $request->valor_unitario;
        return view('front_box.partials_form2._modal_item', 
            compact('item', 'code', 'quantidade', 'valor_unitario'))->render();
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 404);
    }
}

public function qrCodePix(Request $request){
    $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();
    $empresa = Empresa::findOrFail($request->empresa_id);

    $documento = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
    $nome = explode(" ", $empresa->nome);

    if($config == null || !$config->mercadopago_access_token_pix || !$config->mercadopago_public_key_pix){
        return response()->json("Configuração de caixa não cadastrada credencias de PIX", 401);
    }
    try{
        \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token_pix);

        $payment = new \MercadoPago\Payment();

        $payment->transaction_amount = (float)__convert_value_bd($request->total_venda);
        $payment->description = "Venda PDV";
        $payment->payment_method_id = "pix";

        $cep = str_replace("-", "", $config->cep);
        $payment->payer = array(
            "email" => $empresa->email,
            "first_name" => $nome[0],
            "last_name" => $nome[1],
            "identification" => array(
                "type" => strlen($documento) == 14 ? 'CNPJ' : 'CPF',
                "number" => $documento
            ),
            "address"=>  array(
                "zip_code" => str_replace("-", "", $empresa->cep),
                "street_name" => $empresa->rua,
                "street_number" => $empresa->numero,
                "neighborhood" => $empresa->bairro,
                "city" => $empresa->cidade->nome,
                "federal_unit" => $empresa->cidade->uf
            )
        );

        $payment->save();

        if($payment->transaction_details){
            $qrCode = $payment->point_of_interaction->transaction_data->qr_code_base64;
            
            $data = [
                "qrcode" => $qrCode,
                "payment_id" => $payment->id
            ];
            return response()->json($data, 200);

        }else{
            return response()->json($payment->error, 404);
        }
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 404);
    }
}

public function consultaPix(Request $request){

    $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();

    \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token_pix);

    $payStatus = \MercadoPago\Payment::find_by_id($request->payment_id);
    // return response()->json("approved", 200);
    return response()->json($payStatus->status, 200);
}

}
