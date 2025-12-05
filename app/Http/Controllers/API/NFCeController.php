<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Nfce;
use App\Models\Cliente;
use App\Models\ComissaoVenda;
use App\Models\ContaReceber;
use App\Models\ItemNfce;
use App\Models\Inutilizacao;
use App\Models\Produto;
use App\Models\FaturaNfce;
use App\Models\Funcionario;
use App\Models\Caixa;
use App\Models\NaturezaOperacao;
use App\Models\PreVenda;
use App\Models\MargemComissao;
use App\Models\ConfigGeral;
use App\Services\NFCeService;
use App\Services\NFeService;
use App\Utils\EstoqueUtil;
use NFePHP\DA\NFe\Danfce;
use Illuminate\Support\Facades\DB;
use Svg\Tag\Rect;

class NFCeController extends Controller
{
    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;

        if (!is_dir(public_path('xml_nfce'))) {
            mkdir(public_path('xml_nfce'), 0777, true);
        }
        if (!is_dir(public_path('xml_nfce_cancelada'))) {
            mkdir(public_path('xml_nfce_cancelada'), 0777, true);
        }

        if (!is_dir(public_path('danfce'))) {
            mkdir(public_path('danfce'), 0777, true);
        }
    }

    public function emitir(Request $request)
    {

        $documento = $request->documento;
        $destinatario = $request->destinatario;
        $itens = $request->itens;
        $duplicatas = $request->duplicatas;
        $fatura = $request->fatura;

        $empresa = Empresa::findOrFail($request->empresa_id);

        if ($empresa->arquivo == null) {
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $nfe = DB::transaction(function () use ($empresa, $documento, $destinatario, $itens, $duplicatas, $fatura) {
            $nfe = $this->criaNfce($empresa, $documento, $destinatario, $itens, $duplicatas, $fatura);
            return $nfe;
        });

        $nfce_service = new NFCeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "CSC" => isset($documento['csc']) ? $documento['csc'] : $empresa->csc,
            "CSCid" => isset($documento['csc_id']) ? $documento['csc_id'] : $empresa->csc_id
        ], $empresa);

        $doc = $nfce_service->gerarXml($nfe);

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];
            $chave = $doc['chave'];

            try {
                $signed = $nfce_service->sign($xml);
                $resultado = $nfce_service->transmitir($signed, $doc['chave']);

                if ($resultado['erro'] == 0) {
                    $nfe->chave = $doc['chave'];
                    $nfe->estado = 'aprovado';
                    $nfe->numero = $doc['numero'];
                    $nfe->recibo = $resultado['success'];
                    $nfe->data_emissao = date('Y-m-d H:i:s');

                    if ($empresa->ambiente == 2) {
                        $empresa->numero_ultima_nfce_homologacao = $doc['numero'];
                    } else {
                        $empresa->numero_ultima_nfce_producao = $doc['numero'];
                    }

                    $empresa->save();
                    $nfe->save();

                    $xml = file_get_contents(public_path('xml_nfce/') . $nfe->chave . '.xml');
                    $danfe = new Danfce($xml);
                    $pdf = $danfe->render();
                    file_put_contents(public_path('danfce/') . $nfe->chave . '.pdf', $pdf);
                    $pathPrint = env("APP_URL") . "/danfce/$nfe->chave.pdf";
                    $data = [
                        'recibo' => $resultado['success'],
                        'url_print' => $pathPrint,
                        'chave' => $nfe->chave
                    ];
                    return response()->json($data, 200);
                } else {
                    $error = $resultado['error'];
                    if (isset($error['protNFe']['infProt']['xMotivo'])) {
                        $motivo = $error['protNFe']['infProt']['xMotivo'];
                        $cStat = $error['protNFe']['infProt']['cStat'];
                        $nfe->motivo_rejeicao = substr("[$cStat] $motivo", 0, 200);
                    } else {
                        $nfe->motivo_rejeicao = substr($error, 0, 200);
                    }

                    $nfe->numero = isset($documento['numero_nfce']) ? $documento['numero_nfce'] : Nfce::lastNumero($empresa->id);
                    $nfe->chave = $doc['chave'];
                    $nfe->estado = 'rejeitado';
                    $nfe->save();
                    if (isset($error['protNFe']['infProt']['xMotivo'])) {
                        return response()->json("[$cStat] $motivo", 403);
                    } else {
                        return response()->json($error, 403);
                    }
                    // return response()->json($error, 403);

                }
            } catch (\Exception $e) {
                return response()->json(" error: " . $e->getMessage() . ", line: " . $e->getLine(), 404);
            }
        } else {
            return response()->json($doc['erros_xml'], 401);
        }
    }

    private function criaNfce($empresa, $documento, $destinatario, $itens, $duplicatas, $fatura)
    {

        if (isset($destinatario['cpf_cnpj'])) {
            $cliente_id = $this->criaCliente($destinatario, $empresa->id);
        } else {
            $cliente_id = null;
        }

        $natureza_id = null;
        $natureza_id = $this->criaNatureza($documento['natureza_operacao'], $empresa->id);

        $numero = isset($documento['numero_nfce']) ? $documento['numero_nfce'] : 0;
        if ($numero == 0) {
            $lastNfe = Nfce::where('empresa_id', $empresa->id)->orderBy('id', 'desc')->first();
            $numero = $lastNfe != null ? $lastNfe->lastNumero() : 0;
        }
        $data = [
            'empresa_id' => $empresa->id,
            'natureza_id' => $natureza_id,
            'emissor_nome' => $empresa->nome,
            'emissor_cpf_cnpj' => $empresa->cpf_cnpj,
            'cliente_nome' => $destinatario != null ? $destinatario['nome'] : null,
            'cliente_cpf_cnpj' => $destinatario != null ? $destinatario['cpf_cnpj'] : null,
            'chave' => "",
            'numero_serie' => $documento['numero_serie'],
            'numero' => $numero,
            'estado' => 'novo',
            'total' => $fatura['total_nfce'],
            'desconto' => __convert_value_bd($fatura['desconto']),
            'acrescimo' => __convert_value_bd($fatura['acrescimo']),
            'motivo_rejeicao' => "",
            'ambiente' => $empresa->ambiente,
            'api' => 1
        ];
        $item = Nfce::create($data);
        $this->criaItens($itens, $item->id, $empresa->id);
        $this->criaFatura($duplicatas, $item->id, $empresa->id);

        return $item;
    }

    private function criaItens($itens, $nfe_id, $empresa_id)
    {
        foreach ($itens as $i) {

            $produto_id = $this->criaProduto($i, $empresa_id);
            ItemNfce::create([
                'nfce_id' => $nfe_id,
                'produto_id' => $produto_id,
                'quantidade' => __convert_value_bd($i['quantidade']),
                'valor_unitario' => __convert_value_bd($i['valor_unitario']),
                'sub_total' => __convert_value_bd($i['valor_unitario'] * $i['quantidade']),
                'perc_icms' => isset($i['perc_icms']) ? __convert_value_bd($i['perc_icms']) : 0,
                'perc_pis' => isset($i['perc_pis']) ? __convert_value_bd($i['perc_pis']) : 0,
                'perc_cofins' => isset($i['perc_cofins']) ? __convert_value_bd($i['perc_cofins']) : 0,
                'perc_ipi' => isset($i['perc_ipi']) ? __convert_value_bd($i['perc_ipi']) : 0,
                'cst_csosn' => $i['cst_csosn'],
                'cst_pis' => $i['cst_pis'],
                'cst_cofins' => $i['cst_cofins'],
                'cst_ipi' => $i['cst_ipi'],
                'perc_red_bc' => isset($i['perc_red_bc']) ? __convert_value_bd($i['perc_red_bc']) : 0,
                'cfop' => $i['cfop'],
                'ncm' => $i['ncm'],
                'cEnq' => $i['cEnq'],
                'pST' => isset($i['pST']) ? $i['pST'] : '',
                'vBCSTRet' => isset($i['vBCSTRet']) ? $i['vBCSTRet'] : '',
                'cest' => isset($i['cest']) ? $i['cest'] : '',
            ]);
        }
    }

    private function criaProduto($item, $empresa_id)
    {
        $nome_produto = $item['nome_produto'];
        $cod_barras = $item['cod_barras'];

        $produto = Produto::where(function ($q) use ($nome_produto, $cod_barras) {
            $q->where('nome', $nome_produto)->orWhere('codigo_barras', $cod_barras);
        })
        ->where('empresa_id', $empresa_id)
        ->first();

        if ($produto != null) {
            return $produto->id;
        }

        $cfop = $item['cfop'];
        $cfopEstadual = "5" . substr($cfop, 1, 3);
        $cfopOutroEstado = "6" . substr($cfop, 1, 3);
        $produto = Produto::create([
            'empresa_id' => $empresa_id,
            'nome' => $item['nome_produto'],
            'codigo_barras' => $item['cod_barras'],
            'ncm' => $item['ncm'],
            'cest' => $item['cest'],
            'unidade' => $item['unidade'],
            'perc_icms' => __convert_value_bd($item['perc_icms']),
            'perc_pis' => __convert_value_bd($item['perc_pis']),
            'perc_cofins' => __convert_value_bd($item['perc_cofins']),
            'perc_ipi' => __convert_value_bd($item['perc_ipi']),
            'cst_csosn' => $item['cst_csosn'],
            'cst_pis' => $item['cst_pis'],
            'cst_cofins' => $item['cst_cofins'],
            'cst_ipi' => $item['cst_ipi'],
            'valor_unitario' => __convert_value_bd($item['valor_unitario']),
            'cest' => $item['cest'],
            'origem' => $item['origem'],
            'perc_red_bc' => isset($item['perc_red_bc']) ? __convert_value_bd($item['perc_red_bc']) : 0,
            'cfop_estadual' => $cfopEstadual,
            'cfop_outro_estado' => $cfopOutroEstado,
            'cEnq' => isset($item['cEnq']) ? $item['cEnq'] : '999',
        ]);
        return $produto->id;
    }

    private function criaFatura($duplicatas, $nfe_id, $empresa_id)
    {
        foreach ($duplicatas as $i) {
            FaturaNfce::create([
                'nfce_id' => $nfe_id,
                'tipo_pagamento' => $i['tipo'],
                'data_vencimento' => $i['data_vencimento'],
                'valor' => __convert_value_bd($i['valor'])
            ]);
        }
    }

    private function criaNatureza($descricao, $empresa_id)
    {
        $natureza = NaturezaOperacao::where('descricao', $descricao)->first();
        if ($natureza != null) {
            return $natureza->id;
        }

        $natureza = NaturezaOperacao::create([
            'empresa_id' => $empresa_id,
            'descricao' => $descricao,
        ]);

        return $natureza->id;
    }

    private function criaCliente($destinatario, $empresa_id)
    {
        $cpf_cnpj = preg_replace('/[^0-9]/', '', $destinatario['cpf_cnpj']);
        if (strlen($cpf_cnpj) == 11) {
            $doc = $this->setMask($destinatario["cpf_cnpj"], '###.###.###-##');
        } else {
            $doc = $this->setMask($destinatario["cpf_cnpj"], '##.###.###/####-##');
        }

        $cliente = Cliente::where('cpf_cnpj', $doc)->first();
        if ($cliente != null) {
            return $cliente->id;
        }

        $cidade = Cidade::where('codigo', $destinatario['cod_municipio_ibge'])->first();

        $cliente = Cliente::create([
            'empresa_id' => $empresa_id,
            'razao_social' => $destinatario['nome'],
            'nome_fantasia' => $destinatario['nome'],
            'cpf_cnpj' => $doc,
            'ie' => isset($destinatario['ie']) ? $destinatario['ie'] : '',
            'contribuinte' => $destinatario['contribuinte'],
            'consumidor_final' => $destinatario['consumidor_final'],
            'email' => isset($destinatario['email']) ? $destinatario['email'] : '',
            'telefone' => isset($destinatario['telefone']) ? $destinatario['telefone'] : '',
            'cidade_id' => $cidade->id,
            'rua' => $destinatario['rua'],
            'cep' => $destinatario['cep'],
            'numero' => $destinatario['numero'],
            'bairro' => $destinatario['bairro'],
            'complemento' => isset($destinatario['complemento']) ? $destinatario['complemento'] : '',
        ]);
        return $cliente->id;
    }


    private function setMask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i])) {
                    if ($mask[$i] == $val[$k]) {
                        $k++;
                    }
                    $maskared .= $mask[$i];
                }
            }
        }
        return $maskared;
    }


    public function xmlTemporario(Request $request)
    {

        $documento = $request->documento;
        $destinatario = $request->destinatario;
        $itens = $request->itens;
        $frete = $request->frete;
        $duplicatas = $request->duplicatas;
        $fatura = $request->fatura;

        $empresa = Empresa::findOrFail($request->empresa_id);

        if ($empresa->arquivo == null) {
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $nfce_service = new NFCeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "CSC" => isset($documento['csc']) ? $documento['csc'] : $empresa->csc,
            "CSCid" => isset($documento['csc_id']) ? $documento['csc_id'] : $empresa->csc_id
        ], $empresa);

        $nfe = DB::transaction(function () use ($empresa, $documento, $destinatario, $itens, $duplicatas, $fatura) {
            $nfe = $this->criaNfce($empresa, $documento, $destinatario, $itens, $duplicatas, $fatura);
            return $nfe;
        });

        $doc = $nfce_service->gerarXml($nfe);
        if ($nfe != null) {
            $nfe->fatura()->delete();
            $nfe->itens()->delete();
            $nfe->delete();
        }
        // return response()->json($doc, 200);

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];

            return response()->json($xml, 200);
        } else {
            return response()->json($n['erros_xml'], 401);
        }
    }

    public function consultar(Request $request)
    {
        $chave = $request->chave;
        $nfe = Nfce::where('chave', $chave)->first();
        if ($nfe != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $nfe->emissor_cpf_cnpj);
            $nfce_service = new NFCeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$nfe->ambiente,
                "razaosocial" => $nfe->emissor_nome,
                "siglaUF" => $nfe->empresa->cidade->uf,
                "cnpj" => $cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ], $nfe->empresa);
            $consulta = $nfce_service->consultar($nfe);
            if (!isset($nconsultafe['erro'])) {
                $motivo = $consulta['protNFe']['infProt']['xMotivo'];
                $cStat = $consulta['protNFe']['infProt']['cStat'];
                if ($cStat == 100) {
                    return response()->json("[$cStat] $motivo", 200);
                } else {
                    return response()->json("[$cStat] $motivo", 401);
                }
            } else {
                return response()->json($nfe['data'], $nfe['status']);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function cancelar(Request $request)
    {
        $chave = $request->chave;
        $nfe = Nfce::where('chave', $chave)->first();
        if ($nfe != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $nfe->emissor_cpf_cnpj);
            $nfce_service = new NFCeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$nfe->ambiente,
                "razaosocial" => $nfe->emissor_nome,
                "siglaUF" => $nfe->empresa->cidade->uf,
                "cnpj" => $cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ], $nfe->empresa);
            $doc = $nfce_service->cancelar($nfe, $request->motivo);

            if (!isset($doc['erro'])) {
                $nfe->estado = 'cancelado';
                $nfe->save();
                // return response()->json($doc, 200);
                $motivo = $doc['retEvento']['infEvento']['xMotivo'];
                $cStat = $doc['retEvento']['infEvento']['cStat'];
                if ($cStat == 100) {
                    return response()->json("[$cStat] $motivo", 200);
                } else {
                    return response()->json("[$cStat] $motivo", 401);
                }
            } else {
                $arr = $doc['data'];
                $cStat = $arr['retEvento']['infEvento']['cStat'];
                $motivo = $arr['retEvento']['infEvento']['xMotivo'];

                return response()->json("[$cStat] $motivo", $doc['status']);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function inutilizar(Request $request)
    {
        try {
            $data = [
                'empresa_id' => $request->empresa_id,
                'numero_inicial' => $request->numero_inicial,
                'numero_final' => $request->numero_final,
                'numero_serie' => $request->numero_serie,
                'modelo' => '65',
                'justificativa' => $request->justificativa,
                'estado' => 'novo'
            ];

            $item = Inutilizacao::create($data);
            $empresa = Empresa::findOrFail($item->empresa_id);

            $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$empresa->ambiente,
                "razaosocial" => $empresa->nome,
                "siglaUF" => $empresa->cidade->uf,
                "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ], $empresa);
            $consulta = $nfe_service->inutilizar($item);
            if (!isset($consulta['erro'])) {
                $cStat = $consulta['infInut']['cStat'];
                if ($cStat == 102) {
                    $item->estado = 'aprovado';
                    $item->save();
                    return response()->json($consulta['infInut']['xMotivo'], 200);
                } else {
                    $item->estado = 'rejeitado';
                    $item->save();
                    return response()->json($consulta['infInut']['xMotivo'], 403);
                }
            } else {
                $item->estado = 'rejeitado';
                $item->save();
                return response()->json($consulta['data'], 403);
            }
        } catch (\Exception $e) {
            return response()->json("Algo deu errado: " . $e->getMessage(), 404);
        }
    }

    public function gerarNfce(Request $request)
    {
        $nfce = DB::transaction(function () use ($request) {
            $config = Empresa::find($request->empresa_id);
            $item = PreVenda::findOrFail($request->pre_venda_id);
            $usuario_id = $request->usuario_id;
            
            if ($config->ambiente == 2) {
                $numero = $config->numero_ultima_nfce_homologacao;
            } else {
                $numero = $config->numero_ultima_nfce_producao;
            }
            $caixa = Caixa::where('usuario_id', $usuario_id)->where('status', 1)->first();

            $request->merge([
                'emissor_nome' => $config->nome,
                'emissor_cpf_cnpj' => $config->cpf_cnpj,
                'ambiente' => $config->ambiente,
                'chave' => '',
                'cliente_id' => $item->cliente_id,
                'numero_serie' => $config->numero_serie_nfce,
                'numero' => $numero+1,
                'cliente_nome' => $item->cliente->razao_social ?? '',
                'cliente_cpf_cnpj' => $item->cliente->cpf_cnpj ?? '',
                'estado' => 'novo',
                'total' => $item->valor_total,
                'desconto' => $item->desconto,
                'acrescimo' => $item->acrescimo,
                'valor_produtos' => __convert_value_bd($item->valor_total) ?? 0,
                'valor_frete' => $item->valor_frete ? __convert_value_bd($item->valor_frete) : 0,
                'caixa_id' => $caixa ? $caixa->id : null,
                'local_id' => $item->local_id,
                'tipo_pagamento' => '99',
                'dinheiro_recebido' => 0,
                'troco' => 0,
                'natureza_id' => $config->natureza_id_pdv,
            ]);

            $nfce = Nfce::create($request->all());

            for ($i = 0; $i < sizeof($item->itens); $i++) {
                $product = Produto::findOrFail($item->itens[$i]->produto_id);
                ItemNfce::create([
                    'nfce_id' => $nfce->id,
                    'produto_id' => (int)$product->id,
                    'quantidade' => __convert_value_bd($item->itens[$i]->quantidade),
                    'valor_unitario' => $item->itens[$i]->valor,
                    'valor_custo' => $item->itens[$i]->valor,
                    'sub_total' => __convert_value_bd($item->itens[$i]->quantidade * $item->itens[$i]->valor),
                    'perc_icms' =>  $product->perc_icms,
                    'perc_pis' => $product->perc_icms,
                    'perc_cofins' => $product->perc_cofins,
                    'perc_ipi' => $product->perc_ipi,
                    'cst_csosn' => $product->cst_csosn,
                    'cst_pis' => $product->cst_pis,
                    'cst_cofins' => $product->cst_cofins,
                    'cst_ipi' => $product->cst_ipi,
                    'perc_red_bc' => $request->perc_red_bc ? __convert_value_bd($request->perc_red_bc) : 0,
                    'cfop' => $product->cfop_estadual,
                    'ncm' => $product->ncm,
                    'codigo_beneficio_fiscal' => $request->codigo_beneficio_fiscal ?? 0
                ]);

                if ($product->gerenciar_estoque) {
                    $this->util->reduzEstoque($product->id, __convert_value_bd($item->itens[$i]->quantidade), null, $item->local_id);
                }

                $tipo = 'reducao';
                $codigo_transacao = $nfce->id;
                $tipo_transacao = 'venda_nfce';

                $this->util->movimentacaoProduto($product->id, __convert_value_bd($item->itens[$i]->quantidade), $tipo, $codigo_transacao, $tipo_transacao, $item->usuario_id);
            }

            for ($i = 0; $i < sizeof($request->fatura); $i++) {
                $objeto = (object)$request->fatura[$i];
                FaturaNfce::create([
                    'nfce_id' => $nfce->id,
                    'tipo_pagamento' => $objeto->tipo,
                    'data_vencimento' => $objeto->vencimento,
                    'valor' => __convert_value_bd($objeto->valor)
                ]);
            }

            for ($i = 0; $i < sizeof($request->fatura); $i++) {
                $objeto = (object)$request->fatura[$i];

                $dataAtual = date('Y-m-d');
                if ($request->conta_receber == 1 && strtotime($dataAtual) >= strtotime($objeto->vencimento)) {
                    ContaReceber::create([
                        'empresa_id' => $request->empresa_id,
                        'nfce_id' => $nfce->id,
                        'cliente_id' => $item->cliente_id,
                        'valor_integral' => __convert_value_bd($objeto->valor),
                        'tipo_pagamento' => $objeto->tipo,
                        'data_vencimento' => $objeto->vencimento,
                        'local_id' => $caixa->local_id,
                    ]);
                }
            }

            $item->status = 0;
            $item->venda_id = $nfce->id;
            $item->tipo_finalizado = 'nfce';
            $item->save();

            return $nfce;
        });
return response()->json($nfce->id);
}

private function getLastNumero($empresa_id){
    $last = Nfce::where('empresa_id', $empresa_id)
    ->orderBy('numero_sequencial', 'desc')
    ->where('numero_sequencial', '>', 0)->first();
    $numero = $last != null ? $last->numero_sequencial : 0;
    $numero++;
    return $numero;
}

public function gerarVenda(Request $request)
{

    $nfce = DB::transaction(function () use ($request) {
        $config = Empresa::find($request->empresa_id);
        $item = PreVenda::findOrFail($request->pre_venda_id);

        $config = __objetoParaEmissao($config, $item->local_id);

        if ($config->ambiente == 2) {
            $numero = $config->numero_ultima_nfe_homologacao;
        } else {
            $numero = $config->numero_ultima_nfe_producao;
        }
        $troco = 0;
        $total = 0;
        if(sizeof($request->fatura) == 1){
            $objeto = (object)$request->fatura[0];
            if($objeto->tipo == '01'){
                $valorFatura = __convert_value_bd($objeto->valor);
                // $troco = $valorFatura - $item->valor_total;
            }

            $total += __convert_value_bd($objeto->valor);
        }

        $usuario_id = $item->usuario_id;
        $caixa = Caixa::where('usuario_id', $usuario_id)->where('status', 1)->first();
        $request->merge([
            'emissor_nome' => $config->nome,
            'emissor_cpf_cnpj' => $config->cpf_cnpj,
            'ambiente' => $config->ambiente,
            'chave' => '',
            'cliente_id' => $item->cliente_id,
            'numero_serie' => $config->numero_serie_nfce ?? '0',
            'numero' => $numero,
            'cliente_nome' => $item->cliente->razao_social ?? '',
            'cliente_cpf_cnpj' => $item->cliente->cpf_cnpj ?? '',
            'estado' => 'novo',
            // 'total' => $total,
            'total' => $item->valor_total,
            'desconto' => $item->desconto,
            'acrescimo' => $item->acrescimo,
            'valor_produtos' => __convert_value_bd($item->valor_total) ?? 0,
            'valor_frete' => $item->valor_frete ? __convert_value_bd($item->valor_frete) : 0,
            'caixa_id' => $caixa ? $caixa->id : null,
            'local_id' => $item->local_id,
            'tipo_pagamento' => '99',
            'dinheiro_recebido' => 0,
            'troco' => $troco,
            'natureza_id' => $config->natureza_id_pdv,
            'funcionario_id' => $item->funcionario_id,
            'numero_sequencial' => $this->getLastNumero($request->empresa_id)
        ]);

        $nfce = Nfce::create($request->all());

        for ($i = 0; $i < sizeof($item->itens); $i++) {
            $product = Produto::findOrFail($item->itens[$i]->produto_id);
            ItemNfce::create([
                'nfce_id' => $nfce->id,
                'produto_id' => (int)$product->id,
                'quantidade' => __convert_value_bd($item->itens[$i]->quantidade),
                'valor_unitario' => $item->itens[$i]->valor,
                'valor_custo' => $item->itens[$i]->valor,
                'sub_total' => __convert_value_bd($item->itens[$i]->quantidade * $item->itens[$i]->valor),
                'perc_icms' =>  $product->perc_icms,
                'perc_pis' => $product->perc_icms,
                'perc_cofins' => $product->perc_cofins,
                'perc_ipi' => $product->perc_ipi,
                'cst_csosn' => $product->cst_csosn,
                'cst_pis' => $product->cst_pis,
                'cst_cofins' => $product->cst_cofins,
                'cst_ipi' => $product->cst_ipi,
                'perc_red_bc' => $request->perc_red_bc ? __convert_value_bd($request->perc_red_bc) : 0,
                'cfop' => $product->cfop_estadual,
                'ncm' => $product->ncm,
                'codigo_beneficio_fiscal' => $request->codigo_beneficio_fiscal ?? 0
            ]);

            if ($product->gerenciar_estoque) {
                $this->util->reduzEstoque($product->id, __convert_value_bd($item->itens[$i]->quantidade), null, $item->local_id);
            }

            $tipo = 'reducao';
            $codigo_transacao = $nfce->id;
            $tipo_transacao = 'venda_nfce';

            $this->util->movimentacaoProduto($product->id, __convert_value_bd($item->itens[$i]->quantidade), $tipo, 
                $codigo_transacao, $tipo_transacao, $item->usuario_id);
        }

        for ($i = 0; $i < sizeof($request->fatura); $i++) {
            $objeto = (object)$request->fatura[$i];
            FaturaNfce::create([
                'nfce_id' => $nfce->id,
                'tipo_pagamento' => $objeto->tipo,
                'data_vencimento' => $objeto->vencimento ?? date('Y-m-d'),
                'valor' => __convert_value_bd($objeto->valor)
            ]);

            $dataAtual = date('Y-m-d');
            if ($request->conta_receber == 1 && strtotime($dataAtual) >= strtotime($objeto->vencimento)) {
                ContaReceber::create([
                    'empresa_id' => $item->empresa_id,
                    'nfce_id' => $nfce->id,
                    'cliente_id' => $item->cliente_id,
                    'valor_integral' => __convert_value_bd($objeto->valor),
                    'tipo_pagamento' => $objeto->tipo,
                    'data_vencimento' => $objeto->vencimento,
                    'local_id' => $caixa->local_id
                ]);
            }
        }

        

        if ($item->funcionario_id != null) {
            $funcionario = Funcionario::findOrFail($request->funcionario_id);
            $funcionario->comissao;
            $comissao = $funcionario->comissao;
            $valorRetorno = $this->calcularComissaoVenda($nfce, $comissao, $request->empresa_id);
            ComissaoVenda::create([
                'funcionario_id' => $request->funcionario_id,
                'nfe_id' => null,
                'nfce_id' => $nfce->id,
                'tabela' => 'nfce',
                'valor' => $valorRetorno,
                'valor_venda' => $total,
                'status' => 0,
                'empresa_id' => $request->empresa_id
            ]);
        }

        $item->status = 0;
        $item->venda_id = $nfce->id;
        $item->tipo_finalizado = 'nfce';
        $item->save();

        return $nfce;
    });
return response()->json($nfce->id);
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

}
