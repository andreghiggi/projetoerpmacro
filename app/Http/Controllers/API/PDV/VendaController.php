<?php

namespace App\Http\Controllers\API\PDV;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\ItemNfce;
use App\Models\FaturaNfce;
use App\Models\Produto;
use App\Models\ContaEmpresa;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Models\SangriaCaixa;
use App\Models\ItemContaEmpresa;
use App\Models\SuprimentoCaixa;
use App\Models\UsuarioEmissao;
use App\Models\Caixa;
use App\Models\User;
use App\Models\Localizacao;
use Illuminate\Support\Facades\DB;
use App\Utils\EstoqueUtil;
use App\Utils\ContaEmpresaUtil;
use App\Models\ComissaoVenda;
use App\Models\Funcionario;
use App\Models\ConfigGeral;
use App\Models\MargemComissao;

class VendaController extends Controller
{
    protected $util;
    protected $utilConta;

    public function __construct(EstoqueUtil $util, ContaEmpresaUtil $utilConta)
    {
        $this->util = $util;
        $this->utilConta = $utilConta;
    }

    public function store(Request $request){
        try{

            $nfce = DB::transaction(function () use ($request) {
                $empresa = Empresa::findOrFail($request->empresa_id);
                $cliente = null;
                if($request->cliente_id){
                    $cliente = Cliente::findOrFail($request->cliente_id);
                }

                if ($empresa->ambiente == 2) {
                    $numero = $empresa->numero_ultima_nfce_homologacao+1;
                } else {
                    $numero = $empresa->numero_ultima_nfce_producao+1;
                }

                $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();

                $chaveSat = "";
                // if(isset($request->chave_sat) && isset($request->xml_sat)){
                //     if (!is_dir(public_path('xml_sat'))) {
                //         mkdir(public_path('xml_sat'), 0777, true);
                //     }
                //     $chaveSat = $request->chave_sat;
                //     $xml = $request->xml_sat;
                //     file_put_contents(public_path('xml_sat/').$chaveSat.'.xml', $xml);
                // }

                $chaveNfce = "";
                $estado = 'novo';
                // if(isset($request->chave_nfce) && isset($request->xml_nfce) && isset($request->numero_nfce)){
                //     $chaveNfce = $request->chave_nfce;
                //     $xml = $request->xml_nfce;
                //     $numero = $request->numero_nfce;
                //     $estado = 'aprovado';
                //     file_put_contents(public_path('xml_nfce/').$chaveNfce.'.xml', $xml);
                // }

                $numeroSerieNfce = $empresa->numero_serie_nfce ? $empresa->numero_serie_nfce : 1;
                $configUsuarioEmissao = UsuarioEmissao::where('usuario_empresas.empresa_id', request()->empresa_id)
                ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
                ->select('usuario_emissaos.*')
                ->where('usuario_emissaos.usuario_id', $request->usuario_id)
                ->first();

                if($configUsuarioEmissao != null){
                    $numeroSerieNfce = $configUsuarioEmissao->numero_serie_nfce;
                    $numero = $configUsuarioEmissao->numero_ultima_nfce+1;
                }

                $dataNfce = [
                    'empresa_id' => $request->empresa_id,
                    'emissor_nome' => $empresa->nome,
                    'ambiente' => $empresa->ambiente,
                    'emissor_cpf_cnpj' => $empresa->cpf_cnpj,
                    'cliente_id' => $cliente != null ? $cliente->id : null,
                    'cliente_nome' => $cliente != null ? $cliente->razao_social : null,
                    'cliente_cpf_cnpj' => $cliente != null ? $cliente->cpf_cnpj : null,
                    'chave_sat' => $chaveSat,
                    'chave' => $chaveNfce,
                    'numero_serie' => $numeroSerieNfce,
                    'numero' => $numero,
                    'estado' => $estado,
                    'lista_id' => $request->lista_id,
                    'total' => $request->total,
                    'desconto' => $request->desconto,
                    'acrescimo' => $request->acrescimo,
                    'valor_produtos' => $request->total_produtos,
                    'valor_frete' => 0,
                    'caixa_id' => $request->caixa_id ? $request->caixa_id : $caixa->id,
                    'local_id' => $caixa->local_id,
                    'tipo_pagamento' => sizeof($request->fatura) == 0 ? $request->tipo_pagamento : '99',
                    'dinheiro_recebido' => $request->valor_recebido,
                    'troco' => 0,
                    'natureza_id' => $empresa->natureza_id_pdv,
                    'bandeira_cartao' => isset($request->dados_cartao['bandeira']) ? $request->dados_cartao['bandeira'] : '',
                    'cAut_cartao' => isset($request->dados_cartao['codigo']) ? $request->dados_cartao['codigo'] : '',
                    'cnpj_cartao' => isset($request->dados_cartao['cnpj']) ? $request->dados_cartao['cnpj'] : '',
                    'user_id' => $request->usuario_id,
                    'funcionario_id' => $request->funcionario_id
                ];

                if($request->cliente_nome){
                    $dataNfce['cliente_nome'] = $request->cliente_nome;
                }

                if($request->cliente_cpf_cnpj){
                    $dataNfce['cliente_cpf_cnpj'] = $request->cliente_cpf_cnpj;
                }

                $nfce = Nfce::create($dataNfce);

                foreach($request->itens as $item){
                    $product = Produto::findOrFail($item['produto_id']);
                    $dataItem = [
                        'nfce_id' => $nfce->id,
                        'produto_id' => $product->id,
                        'quantidade' => $item['quantidade'],
                        'valor_unitario' => $item['valor_unitario'],
                        'valor_custo' => 0,
                        'sub_total' => $item['sub_total'],
                        'perc_icms' =>  $product->perc_icms,
                        'perc_pis' => $product->perc_icms,
                        'perc_cofins' => $product->perc_cofins,
                        'perc_ipi' => $product->perc_ipi,
                        'cst_csosn' => $product->cst_csosn,
                        'cst_pis' => $product->cst_pis,
                        'cst_cofins' => $product->cst_cofins,
                        'cst_ipi' => $product->cst_ipi,
                        'perc_red_bc' => $product->perc_red_bc ?? 0,
                        'cfop' => $product->cfop_estadual,
                        'ncm' => $product->ncm,
                        'codigo_beneficio_fiscal' => $product->codigo_beneficio_fiscal
                    ];
                    $itemNfce = ItemNfce::create($dataItem);

                    if ($product->gerenciar_estoque) {
                        $this->util->reduzEstoque($product->id, $item['quantidade'], null, $caixa->local_id);
                    }

                    $tipo = 'reducao';
                    $codigo_transacao = $nfce->id;
                    $tipo_transacao = 'venda_nfce';

                    $this->util->movimentacaoProduto($product->id, $item['quantidade'], $tipo, $codigo_transacao, $tipo_transacao, $request->usuario_id);

                }

                if ($request->funcionario_id != null) {
                    $funcionario = Funcionario::findOrFail($request->funcionario_id);
                    $comissao = $funcionario->comissao;
                    $valorRetorno = $this->calcularComissaoVenda($nfce, $comissao, $nfce->empresa_id);

                    if($valorRetorno > 0){
                        ComissaoVenda::create([
                            'funcionario_id' => $request->funcionario_id,
                            'nfe_id' => null,
                            'nfce_id' => $nfce->id,
                            'tabela' => 'nfce',
                            'valor' => $valorRetorno,
                            'valor_venda' => __convert_value_bd($request->total),
                            'status' => 0,
                            'empresa_id' => $nfce->empresa_id
                        ]);
                    }
                }

                if(sizeof($request->fatura) > 0){
                    foreach($request->fatura as $fat){
                        FaturaNfce::create([
                            'nfce_id' => $nfce->id,
                            'tipo_pagamento' => $fat['tipo'],
                            'data_vencimento' => $fat['data'],
                            'valor' => $fat['valor']
                        ]);
                    }
                }else{
                    FaturaNfce::create([
                        'nfce_id' => $nfce->id,
                        'tipo_pagamento' => $request->tipo_pagamento,
                        'data_vencimento' => date('Y-m-d'),
                        'valor' => $request->total
                    ]);
                }

                return $nfce;
            });

$nfce = Nfce::where('id', $nfce->id)
->with(['itens', 'fatura', 'cliente'])
->first();


foreach($nfce->fatura as $f){
    $f->tipo_pagamento = Nfce::getTipoPagamento($f->tipo_pagamento);
}


return response()->json($nfce, 200);

}catch(\Exception $e){
    return response()->json($e->getMessage(), 403);
}
}

private function calcularComissaoVenda($nfce, $comissao, $empresa_id)
{
    $valorRetorno = 0;
    $config = ConfigGeral::where('empresa_id', $empresa_id)->first();

    $tipoComissao = 'percentual_vendedor';
    if($config != null && $config->tipo_comissao == 'percentual_margem'){
        $tipoComissao = 'percentual_margem';
    }
    if($tipoComissao == 'percentual_vendedor'){
        $valorRetorno = ($nfce->total * $comissao) / 100;
    }else{
        foreach ($nfce->itens as $i) {

            $percentualLucro = ((($i->produto->valor_compra-$i->valor_unitario)/$i->produto->valor_compra)*100)*-1;
            $margens = MargemComissao::where('empresa_id', $empresa_id)->get();
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

public function bandeirasCartao(){
    $bandeiras = Nfce::bandeiras();
    $data = [];

    array_push($data, [
        'id' => '',
        'nome' => 'Selecione'
    ]);
    foreach($bandeiras as $key => $b){
        array_push($data, [
            'id' => $key,
            'nome' => $b
        ]);
    }
    return response()->json($data, 200);
}

public function tiposPagamento(){
    $tipos = Nfce::tiposPagamento();
    $data = [];

    array_push($data, [
        'id' => '',
        'nome' => 'Selecione'
    ]);
    foreach($tipos as $key => $t){
        array_push($data, [
            'id' => $key,
            'nome' => $t
        ]);
    }
    return response()->json($data, 200);
}

public function getCaixa(Request $request){
    $item = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
    return response()->json($item, 200);
}

public function contasEmpresa(Request $request){
    $data = ContaEmpresa::where('empresa_id', $request->empresa_id)
    ->with(['plano'])
    ->where('status', 1)->get();
    return response()->json($data, 200);
}

public function locaisUsuario(Request $request){
    $usuario = User::findOrFail($request->usuario_id);
    $locais = [];
    foreach($usuario->locais as $l){
        if($l->localizacao->status){
            array_push($locais, [
                'id' => $l->localizacao_id,
                'descricao' => $l->localizacao->descricao
            ]);
        }
    }
    return response()->json($locais, 200);
}

public function storeCaixa(Request $request){
    try{
        $local_id = null;
        $user = User::findOrFail($request->usuario_id);
        if(!$request->local_id){
            
            if(sizeof($user->locais) > 0){
                $local_id = $user->locais[0]->localizacao_id;
            }
        }else{
            $local_id = $request->local_id;
        }

        $empresa_id = $user->empresa->empresa_id;
        $data = [
            'usuario_id' => $request->usuario_id,
            'valor_abertura' => $request->valor ? __convert_value_bd($request->valor) : 0,
            'observacao' => $request->observacao ?? '',
            'conta_empresa_id' => $request->conta_id ?? null,
            'local_id' => $local_id,
            'status' => 1,
            'valor_fechamento' => 0,
            'empresa_id' => $empresa_id
        ];
        $item = Caixa::create($data);
        return response()->json($item, 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 403);
    }
}

public function storeSangria(Request $request){
    try{
        $data = [
            'caixa_id' => $request->caixa_id,
            'valor' => __convert_value_bd($request->valor),
            'observacao' => $request->observacao ?? '',
            'conta_empresa_id' => $request->conta_id ?? null,
        ];
        $item = SangriaCaixa::create($data);

        if($request->conta_id){
            $caixa = Caixa::findOrFail($request->caixa_id);
            $data = [
                'conta_id' => $caixa->conta_empresa_id,
                'descricao' => "Sangria de caixa",
                'tipo_pagamento' => '01',
                'valor' => __convert_value_bd($request->valor),
                'caixa_id' => $caixa->id,
                'tipo' => 'saida'
            ];
            $itemContaEmpresa = ItemContaEmpresa::create($data);
            $this->utilConta->atualizaSaldo($itemContaEmpresa);

            $data = [
                'conta_id' => $request->conta_id,
                'descricao' => "Sangria de caixa",
                'tipo_pagamento' => '01',   
                'valor' => __convert_value_bd($request->valor),
                'caixa_id' => $caixa->id,
                'tipo' => 'entrada'
            ];
            $itemContaEmpresa = ItemContaEmpresa::create($data);
            $this->utilConta->atualizaSaldo($itemContaEmpresa);
        }
        return response()->json($item, 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 403);
    }
}

public function storeSuprimento(Request $request){
    try{
        $data = [
            'caixa_id' => $request->caixa_id,
            'valor' => __convert_value_bd($request->valor),
            'observacao' => $request->observacao ?? '',
            'conta_empresa_id' => $request->conta_id ?? null,
            'tipo_pagamento' => $request->tipo_pagamento
        ];
        $item = SuprimentoCaixa::create($data);

        if($request->conta_id){
            $caixa = Caixa::findOrFail($request->caixa_id);
            $data = [
                'conta_id' => $caixa->conta_empresa_id,
                'descricao' => "Suprimento de caixa",
                'tipo_pagamento' => $request->tipo_pagamento,
                'valor' => __convert_value_bd($request->valor),
                'caixa_id' => $caixa->id,
                'tipo' => 'entrada'
            ];
            $itemContaEmpresa = ItemContaEmpresa::create($data);
            $this->utilConta->atualizaSaldo($itemContaEmpresa);

            $data = [
                'conta_id' => $request->conta_id,
                'descricao' => "Suprimento de caixa",
                'tipo_pagamento' => $request->tipo_pagamento,   
                'valor' => __convert_value_bd($request->valor),
                'caixa_id' => $caixa->id,
                'tipo' => 'saida'
            ];
            $itemContaEmpresa = ItemContaEmpresa::create($data);
            $this->utilConta->atualizaSaldo($itemContaEmpresa);
        }
        return response()->json($item, 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 403);
    }
}

public function getVendasCaixa(Request $request){
    try{
        $vendas = Nfce::where('caixa_id', $request->caixa_id)
        ->with(['itens', 'cliente', 'fatura'])
        ->orderBy('id', 'desc')
        ->get();
        
        foreach($vendas as $v){
            $v->tipo_pagamento = Nfce::getTipoPagamento($v->tipo_pagamento);
        }

        $suprimentos = SuprimentoCaixa::where('caixa_id', $request->caixa_id)
        ->get();

        $sangrias = SangriaCaixa::where('caixa_id', $request->caixa_id)
        ->get();

        $caixa = Caixa::findOrFail($request->caixa_id)->first();

        $totalDeVendas = $vendas->sum('total');
        $totalSangrias = $sangrias->sum('valor');
        $totalSuprimentos = $suprimentos->sum('valor');
        $data = [
            'caixa' => $caixa,
            'vendas' => $vendas,
            'suprimentos' => $suprimentos,
            'sangrias' => $sangrias,
            'totalDeVendas' => $totalDeVendas,
            'totalSangrias' => $totalSangrias,
            'totalSuprimentos' => $totalSuprimentos,
        ];

        return response()->json($data, 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 403);
    }
}

public function dataHome(Request $request){
    $empresa_id = $request->empresa_id;
    $usuario_id = $request->usuario_id;
    $caixa = Caixa::where('usuario_id', $usuario_id)->where('status', 1)->first();

    try{

        $locais = Localizacao::where('usuario_localizacaos.usuario_id', $usuario_id)
        ->select('localizacaos.*')
        ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
        ->where('localizacaos.status', 1)->get();

        $locais = $locais->pluck(['id']);

        $produtos = Produto::where('empresa_id', $empresa_id)
        ->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
        ->whereIn('produto_localizacaos.localizacao_id', $locais)
        ->count();

        $clientes = Cliente::where('empresa_id', $empresa_id)
        ->count();
        $somaVendas = 0;
        if($caixa){
            $nfce = Nfce::where('empresa_id', $empresa_id)->where('caixa_id', $caixa->id)
            ->sum('total');
            $nfe = Nfe::where('empresa_id',  $empresa_id)->where('caixa_id', $caixa->id)
            ->where('tpNF', 1)
            ->sum('total');
            $somaVendas = $nfce + $nfe;
        }

        $chart = $this->dataChart($empresa_id, $usuario_id);
        $empresa = Empresa::findOrFail($empresa_id);
        $data = [
            'produtos' => $produtos,
            'clientes' => $clientes,
            'soma_vendas' => $somaVendas,
            'chart' => $chart,
            'empresa_ativa' => $empresa->status
        ];

        return response()->json($data, 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 403);
    }
}

private function dataChart($empresa_id, $usuario_id){
    $horarios = [];
    $labels = [];
    $values = [];

    for($i=0; $i<=23; $i++){

        $hora = (($i<10) ? "0$i" : $i) . ":00";
        $horaFutura = (($i<10) ? "0$i" : $i) . ":59";
        $labels[] = $hora;

        $dataAtual = date('Y-m-d');
        $nfce = Nfce::where('empresa_id', $empresa_id)
        ->whereBetween('created_at', [
            $dataAtual . " " . $hora,
            $dataAtual . " " . $horaFutura,
        ])
        ->sum('total');

        $nfe = Nfe::where('empresa_id', $empresa_id)->sum('total');

        $values[] = $nfce;

    }

    return [
        'labels' => $labels,
        'values' => $values,
    ];

}

}
