<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Models\Caixa;
use App\Models\Cidade;
use App\Models\ContaReceber;
use App\Models\Produto;
use App\Models\Inutilizacao;
use App\Models\Nfe;
use App\Models\FaturaNfe;
use App\Models\ItemNfe;
use App\Models\NaturezaOperacao;
use App\Models\PreVenda;
use App\Services\NFeService;
use App\Utils\EstoqueUtil;
use App\Utils\SiegUtil;
use NFePHP\DA\NFe\Danfe;
use Illuminate\Support\Facades\DB;
use NFePHP\DA\NFe\Daevento;
use PhpParser\Node\Expr\Cast\Object_;

class NFeController extends Controller
{

    protected $util;
    protected $siegUtil;

    public function __construct(EstoqueUtil $util, SiegUtil $siegUtil)
    {
        $this->util = $util;
        $this->siegUtil = $siegUtil;

        if (!is_dir(public_path('xml_nfe'))) {
            mkdir(public_path('xml_nfe'), 0777, true);
        }
        if (!is_dir(public_path('xml_nfe_cancelada'))) {
            mkdir(public_path('xml_nfe_cancelada'), 0777, true);
        }
        if (!is_dir(public_path('xml_nfe_correcao'))) {
            mkdir(public_path('xml_nfe_correcao'), 0777, true);
        }
        if (!is_dir(public_path('danfe_temp'))) {
            mkdir(public_path('danfe_temp'), 0777, true);
        }

        if (!is_dir(public_path('danfe'))) {
            mkdir(public_path('danfe'), 0777, true);
        }
        if (!is_dir(public_path('danfe_correcao'))) {
            mkdir(public_path('danfe_correcao'), 0777, true);
        }
        if (!is_dir(public_path('danfe_cancelamento'))) {
            mkdir(public_path('danfe_cancelamento'), 0777, true);
        }
    }

    public function emitir(Request $request)
    {

        $documento = $request->documento;
        $destinatario = $request->destinatario;
        $itens = $request->itens;
        $frete = $request->frete;
        $pagamento = $request->pagamento;
        $fatura = $request->fatura;
        $duplicatas = $request->duplicatas;

        $empresa = Empresa::findOrFail($request->empresa_id);

        if ($empresa->arquivo == null) {
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $nfe_service = new NFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            // "schemes" => "PL_009_V4",
            "schemes" => "PL_010_V1.21",
            "versao" => "4.00",
        ], $empresa);

        $nfe = DB::transaction(function () use ($empresa, $documento, $destinatario, $itens, $frete, $fatura, $duplicatas) {

            $nfe = $this->criaNfe($empresa, $documento, $destinatario, $itens, $frete, $fatura, $duplicatas);
            return $nfe;
        });
        $doc = $nfe_service->gerarXml($nfe);

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];

            $chave = $doc['chave'];
            die;

            try {
                $signed = $nfe_service->sign($xml);
                $resultado = $nfe_service->transmitir($signed, $doc['chave']);

                if ($resultado['erro'] == 0) {
                    $nfe->chave = $doc['chave'];
                    $nfe->estado = 'aprovado';
                    $nfe->numero = $doc['numero'];
                    $nfe->recibo = $resultado['success'];
                    $nfe->data_emissao = date('Y-m-d H:i:s');

                    if ($empresa->ambiente == 2) {
                        $empresa->numero_ultima_nfe_homologacao = $doc['numero'];
                    } else {
                        $empresa->numero_ultima_nfe_producao = $doc['numero'];
                    }

                    $empresa->save();

                    $nfe->save();

                    $xml = file_get_contents(public_path('xml_nfe/') . $nfe->chave . '.xml');
                    $danfe = new Danfe($xml);
                    $pdf = $danfe->render();
                    file_put_contents(public_path('danfe/') . $nfe->chave . '.pdf', $pdf);
                    $pathPrint = env("APP_URL") . "/danfe/$nfe->chave.pdf";
                    $data = [
                        'recibo' => $resultado['success'],
                        'url_print' => $pathPrint,
                        'chave' => $nfe->chave
                    ];
                    return response()->json($data, 200);
                } else {
                    $error = $resultado['error'];
                    $recibo = isset($resultado['recibo']) ? $resultado['recibo'] : null;
                    // return response()->json($resultado, 401);

                    if (isset($error['protNFe'])) {
                        $motivo = $error['protNFe']['infProt']['xMotivo'];
                        $cStat = $error['protNFe']['infProt']['cStat'];
                        $nfe->motivo_rejeicao = substr("[$cStat] $motivo", 0, 200);
                    }

                    $nfe->numero = isset($documento['numero_nfe']) ? $documento['numero_nfe'] :
                    Nfe::lastNumero($empresa);

                    if($nfe->signed_xml == null){
                        $nfe->signed_xml = $signed;
                    }
                    $nfe->chave = $doc['chave'];
                    $nfe->estado = 'rejeitado';
                    $nfe->save();

                    if (isset($error['protNFe'])) {
                        return response()->json("[$cStat] $motivo", 403);
                    } else {
                        return response()->json($error, 403);
                    }
                }
            } catch (\Exception $e) {

                return response()->json(__getError($e), 404);
            }
        } else {
            return response()->json($n['erros_xml'], 401);
        }
    }

    private function criaNfe($empresa, $documento, $destinatario, $itens, $frete, $fatura, $duplicatas)
    {

        $cliente_id = $this->criaCliente($destinatario, $empresa->id);

        $natureza_id = null;
        $natureza_id = $this->criaNatureza($documento['natureza_operacao'], $empresa->id);

        $numero = isset($documento['numero_nfe']) ? $documento['numero_nfe'] : 0;
        if ($numero == 0) {
            $empresa = Empresa::findOrFail($empresa->id);
            $numero = Nfe::lastNumero($empresa);
        }
        $data = [
            'empresa_id' => $empresa->id,
            'cliente_id' => $cliente_id,
            'natureza_id' => $natureza_id,
            'emissor_nome' => $empresa->nome,
            'emissor_cpf_cnpj' => $empresa->cpf_cnpj,
            'chave' => "",
            'numero_serie' => $documento['numero_serie'],
            'numero' => $numero,
            'estado' => 'novo',
            'total' => $fatura['total_nf'],
            'sequencia_cce' => 0,
            'motivo_rejeicao' => "",
            'ambiente' => $empresa->ambiente,
            'api' => 1,
            'aut_xml' => isset($documento['aut_xml']) ? $documento['aut_xml'] : '',
            'total' => __convert_value_bd($fatura['total_nf']),
            'desconto' => __convert_value_bd($fatura['desconto']),
            'acrescimo' => __convert_value_bd($fatura['acrescimo']),
            'valor_produtos' => $this->somaProdutos($itens),
            'valor_frete' => $frete ? __convert_value_bd($frete['valor']) : 0,

            'placa' => isset($frete['placa']) ? $frete['placa'] : null,
            'uf' => isset($frete['uf']) ? $frete['uf'] : null,
            'qtd_volumes' => isset($frete['quantidade_volumes']) ? $frete['quantidade_volumes'] : null,
            'numeracao_volumes' => isset($frete['numero_volumes']) ? $frete['numero_volumes'] : null,
            'especie' => isset($frete['especie']) ? $frete['especie'] : null,
            'peso_liquido' => isset($frete['peso_liquido']) ? __convert_value_bd($frete['peso_liquido']) : null,
            'peso_bruto' => isset($frete['peso_bruto']) ? __convert_value_bd($frete['peso_bruto']) : null,
            'referencia' => isset($documento['referencia']) ? $documento['referencia'] : '',
            'tpNF' => $documento['tpNF'],
            'finNFe' => $documento['finNFe'],
        ];

        $item = Nfe::create($data);

        $this->criaItens($itens, $item->id, $empresa->id);
        $this->criaFatura($duplicatas, $item->id, $empresa->id);
        return $item;
    }

    private function somaProdutos($itens)
    {
        $soma = 0;
        foreach ($itens as $i) {
            $soma += $i['quantidade'] * $i['valor_unitario'];
        }
        return $soma;
    }

    private function criaFatura($duplicatas, $nfe_id, $empresa_id)
    {
        foreach ($duplicatas as $i) {
            FaturaNfe::create([
                'nfe_id' => $nfe_id,
                'tipo_pagamento' => $i['tipo'],
                'data_vencimento' => $i['data_vencimento'],
                'valor' => __convert_value_bd($i['valor'])
            ]);
        }
    }

    private function criaItens($itens, $nfe_id, $empresa_id)
    {
        foreach ($itens as $i) {

            $produto_id = $this->criaProduto($i, $empresa_id);
            ItemNfe::create([
                'nfe_id' => $nfe_id,
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
                'codigo_beneficio_fiscal' => isset($i['codigo_beneficio_fiscal']) ? __convert_value_bd($i['codigo_beneficio_fiscal']) : 0,
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
        $pagamento = $request->pagamento;
        $fatura = $request->fatura;
        $duplicatas = $request->duplicatas;

        $empresa = Empresa::findOrFail($request->empresa_id);

        if ($empresa->arquivo == null) {
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $nfe = DB::transaction(function () use ($empresa, $documento, $destinatario, $itens, $frete, $fatura, $duplicatas) {
            $nfe = $this->criaNfe($empresa, $documento, $destinatario, $itens, $frete, $fatura, $duplicatas);
            return $nfe;
        });

        $nfe_service = new NFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            // "schemes" => "PL_009_V4",
            "schemes" => "PL_010_V1.21",
            "versao" => "4.00",
        ], $empresa);

        $doc = $nfe_service->gerarXml($nfe);

        if ($nfe != null) {
            $nfe->fatura()->delete();
            $nfe->itens()->delete();
            $nfe->delete();
        }

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];

            return response()->json($xml, 200);
        } else {
            return response()->json($doc['erros_xml'], 401);
        }
    }

    public function danfeTemporario(Request $request)
    {

        $documento = $request->documento;
        $destinatario = $request->destinatario;
        $itens = $request->itens;
        $frete = $request->frete;
        $pagamento = $request->pagamento;
        $fatura = $request->fatura;
        $duplicatas = $request->duplicatas;

        $empresa = Empresa::findOrFail($request->empresa_id);

        if ($empresa->arquivo == null) {
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $nfe = DB::transaction(function () use ($empresa, $documento, $destinatario, $itens, $frete, $fatura, $duplicatas) {
            $nfe = $this->criaNfe($empresa, $documento, $destinatario, $itens, $frete, $fatura, $duplicatas);
            return $nfe;
        });


        $nfe_service = new NFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            // "schemes" => "PL_009_V4",
            "schemes" => "PL_010_V1.21",
            "versao" => "4.00",
        ], $empresa);

        $doc = $nfe_service->gerarXml($nfe);
        if ($nfe != null) {
            $nfe->fatura()->delete();
            $nfe->itens()->delete();
            $nfe->delete();
        }

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];
            $chave = $doc['chave'];
            $danfe = new Danfe($xml);
            $pdf = $danfe->render();
            file_put_contents(public_path('danfe_temp/') . $chave . '.pdf', $pdf);
            $pathPrint = env("APP_URL") . "/danfe_temp/$chave.pdf";
            return response()->json($pathPrint, 200);
        } else {
            return response()->json($doc['erros_xml'], 401);
        }
    }

    public function consultar(Request $request)
    {
        $chave = $request->chave;
        $nfe = Nfe::where('chave', $chave)->first();
        if ($nfe != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $nfe->emissor_cpf_cnpj);
            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$nfe->ambiente,
                "razaosocial" => $nfe->emissor_nome,
                "siglaUF" => $nfe->empresa->cidade->uf,
                "cnpj" => $cnpj,
                // "schemes" => "PL_009_V4",
                "schemes" => "PL_010_V1.21",
                "versao" => "4.00",
            ], $nfe->empresa);
            $consulta = $nfe_service->consultar($nfe);
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

    public function corrigir(Request $request)
    {
        $chave = $request->chave;
        $nfe = Nfe::where('chave', $chave)->first();
        if ($nfe != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $nfe->emissor_cpf_cnpj);
            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$nfe->ambiente,
                "razaosocial" => $nfe->emissor_nome,
                "siglaUF" => $nfe->empresa->cidade->uf,
                "cnpj" => $cnpj,
                // "schemes" => "PL_009_V4",
                "schemes" => "PL_010_V1.21",
                "versao" => "4.00",
            ], $nfe->empresa);
            $doc = $nfe_service->correcao($nfe, $request->motivo);

            if (!isset($doc['erro'])) {

                // return response()->json($doc, 200);
                $motivo = $doc['retEvento']['infEvento']['xMotivo'];
                $cStat = $doc['retEvento']['infEvento']['cStat'];

                $xml = file_get_contents(public_path('xml_nfe_correcao/') . $nfe->chave . '.xml');
                $dadosEmitente = $this->getEmitente($nfe->empresa);
                $daevento = new Daevento($xml, $dadosEmitente);
                $pdf = $daevento->render();

                file_put_contents(public_path('danfe_correcao/') . $nfe->chave . '.pdf', $pdf);
                $pathPrint = env("APP_URL") . "/danfe_correcao/$nfe->chave.pdf";

                $data = [
                    'url_print' => $pathPrint,
                    'status' => "[$cStat] $motivo"
                ];

                if ($cStat == 135) {
                    return response()->json($data, 200);
                } else {
                    return response()->json("[$cStat] $motivo", 401);
                }
            } else {
                return response()->json($doc['data'], $doc['status']);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    private function getEmitente($empresa)
    {

        return [
            'razao' => $empresa->nome,
            'logradouro' => $empresa->rua,
            'numero' => $empresa->numero,
            'complemento' => '',
            'bairro' => $empresa->bairro,
            'CEP' => preg_replace('/[^0-9]/', '', $empresa->cep),
            'municipio' => $empresa->cidade->nome,
            'UF' => $empresa->cidade->uf,
            'telefone' => $empresa->telefone,
            'email' => ''
        ];
    }

    public function cancelar(Request $request)
    {
        $chave = $request->chave;
        $nfe = Nfe::where('chave', $chave)->first();
        if ($nfe != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $nfe->emissor_cpf_cnpj);
            $nfe_service = new NFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$nfe->ambiente,
                "razaosocial" => $nfe->emissor_nome,
                "siglaUF" => $nfe->empresa->cidade->uf,
                "cnpj" => $cnpj,
                // "schemes" => "PL_009_V4",
                "schemes" => "PL_010_V1.21",
                "versao" => "4.00",
            ], $nfe->empresa);
            $doc = $nfe_service->cancelar($nfe, $request->motivo);

            if (!isset($doc['erro'])) {
                $nfe->estado = 'cancelado';
                $nfe->save();
                // return response()->json($doc, 200);
                // if(!isset($doc['retEvento'])){
                //     return response()->json($doc, 401);
                // }
                // $motivo = $doc['retEvento']['infEvento']['xMotivo'];
                // $cStat = $doc['retEvento']['infEvento']['cStat'];

                $xml = file_get_contents(public_path('xml_nfe_cancelada/') . $nfe->chave . '.xml');
                $dadosEmitente = $this->getEmitente($nfe->empresa);
                $daevento = new Daevento($xml, $dadosEmitente);
                $pdf = $daevento->render();

                file_put_contents(public_path('danfe_cancelamento/') . $nfe->chave . '.pdf', $pdf);
                $pathPrint = env("APP_URL") . "/danfe_cancelamento/$nfe->chave.pdf";

                $data = [
                    'url_print' => $pathPrint,
                    'status' => "[$cStat] $motivo"
                ];
                if ($doc == '[135] Evento registrado e vinculado a NF-e') {
                    return response()->json($doc, 200);
                } else {
                    return response()->json($doc, 401);
                }
            } else {
                $arr = $doc['data'];
                if (isset($arr['retEvento'])) {

                    $cStat = $arr['retEvento']['infEvento']['cStat'];
                    $motivo = $arr['retEvento']['infEvento']['xMotivo'];

                    return response()->json("[$cStat] $motivo", $doc['status']);
                } else {
                    return response()->json($arr, $doc['status']);
                }
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
                'modelo' => '55',
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

    public function gerarNfe(Request $request)
    {
        // return sizeof($request->fatura);
        $nfe = DB::transaction(function () use ($request) {

            $item = PreVenda::findOrFail($request->pre_venda_id);
            $usuario_id = $request->usuario_id;
            $config = Empresa::find($item->empresa_id);
            // $caixa = __isCaixaAberto();
            if ($config->ambiente == 2) {
                $numero = $config->numero_ultima_nfe_homologacao;
            } else {
                $numero = $config->numero_ultima_nfe_producao;
            }

            $caixa = Caixa::where('usuario_id', $usuario_id)->where('status', 1)->first();

            $request->merge([
                'natureza_id' => $config->natureza_id_pdv,
                'emissor_nome' => $config->nome,
                'emissor_cpf_cnpj' => $config->cpf_cnpj,
                'ambiente' => $config->ambiente,
                'chave' => '',
                'cliente_id' => $item->cliente_id,
                'numero_serie' => $config->numero_serie_nfe,
                'numero' => $numero+1,
                'estado' => 'novo',
                'orcamento' => 0,
                'total' => $item->valor_total,
                'desconto' => $item->desconto,
                'acrescimo' => $item->acrescimo,
                'valor_produtos' => $item->valor_total,
                'empresa_id' => $item->empresa_id,
                'caixa_id' => $caixa ? $caixa->id : null,
                'local_id' => $caixa->local_id,
            ]);

            $nfe = Nfe::create($request->all());
            $cliente = Cliente::findOrFail($item->cliente_id);

            for ($i = 0; $i < sizeof($item->itens); $i++) {
                $product = Produto::findOrFail($item->itens[$i]->produto_id);

                $cfop = $product->cfop_estadual;
                if($cliente->cidade->uf != $config->cidade->uf){
                    $cfop = $product->cfop_outro_estado;
                }

                ItemNfe::create([
                    'nfe_id' => $nfe->id,
                    'produto_id' => (int)$product->id,
                    'quantidade' => __convert_value_bd($item->itens[$i]->quantidade),
                    'valor_unitario' => $item->itens[$i]->valor,
                    'sub_total' => __convert_value_bd($item->itens[$i]->quantidade * $item->itens[$i]->valor),
                    'perc_icms' => $product->perc_icms,
                    'perc_pis' => $product->perc_pis,
                    'perc_cofins' => $product->perc_cofins,
                    'perc_ipi' => $product->perc_ipi,
                    'cst_csosn' => $product->cst_csosn,
                    'cst_pis' => $product->cst_pis,
                    'cst_cofins' => $product->cst_cofins,
                    'cst_ipi' => $product->cst_ipi,
                    'perc_red_bc' => $product->perc_red_bc ? __convert_value_bd($product->perc_red_bc) : 0,
                    'cfop' => $cfop,
                    'ncm' => $product->ncm,
                    'codigo_beneficio_fiscal' => $product->codigo_beneficio_fiscal ?? 0
                ]);

                if ($product->gerenciar_estoque) {
                    if (isset($request->is_compra)) {
                        $this->util->incrementaEstoque($product->id, __convert_value_bd($item->itens[$i]->quantidade), $caixa->local_id);
                    } else {
                        $this->util->reduzEstoque($product->id, __convert_value_bd($item->itens[$i]->quantidade), $caixa->local_id);
                    }
                    $tipo = 'reducao';
                    $codigo_transacao = $nfe->id;
                    $tipo_transacao = 'venda_nfe';
                    $this->util->movimentacaoProduto($product->id, __convert_value_bd($item->itens[$i]->quantidade), $tipo, $codigo_transacao, $tipo_transacao, $usuario_id);
                }
            }

            for ($i = 0; $i < sizeof($request->fatura); $i++) {
                $objeto = (object)$request->fatura[$i];
                FaturaNfe::create([
                    'nfe_id' => $nfe->id,
                    'tipo_pagamento' => $objeto->tipo,
                    'data_vencimento' => $objeto->vencimento,
                    'valor' => __convert_value_bd($objeto->valor)
                ]);
            }

            for ($i = 0; $i < sizeof($request->fatura); $i++) {
                $objeto = (object)$request->fatura[$i];
                if ($request->conta_receber == 1) {
                    ContaReceber::create([
                        'empresa_id' => $request->empresa_id,
                        'nfe_id' => $nfe->id,
                        'cliente_id' => $item->cliente_id,
                        'valor_integral' => __convert_value_bd($objeto->valor),
                        'tipo_pagamento' => $objeto->tipo,
                        'data_vencimento' => $objeto->vencimento,
                        'local_id' => $caixa->local_id
                    ]);
                }
            }
            $item->status = 0;
            $item->venda_id = $nfe->id;
            $item->save();

            return $nfe;
        });
return  response()->json($nfe->id);
}
}
