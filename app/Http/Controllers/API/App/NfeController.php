<?php

namespace App\Http\Controllers\API\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\ItemNfe;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Models\ComissaoVenda;
use App\Models\Produto;
use App\Models\MargemComissao;
use App\Models\FaturaNfe;
use App\Models\ConfigGeral;
use App\Models\ContaReceber;
use App\Models\Caixa;
use App\Models\Funcionario;
use App\Models\NaturezaOperacao;
use App\Models\Transportadora;
use Illuminate\Support\Facades\DB;
use App\Utils\EstoqueUtil;
use App\Utils\EmailUtil;
use App\Utils\SiegUtil;
use NFePHP\DA\NFe\Danfe;
use App\Services\NFeService;
use Dompdf\Dompdf;
use NFePHP\DA\NFe\Daevento;

class NfeController extends Controller
{

    protected $estoqueUtil;
    protected $emailUtil;
    protected $siegUtil;

    public function __construct(EmailUtil $emailUtil, SiegUtil $siegUtil, EstoqueUtil $estoqueUtil){
        $this->emailUtil = $emailUtil;
        $this->siegUtil = $siegUtil;
        $this->estoqueUtil = $estoqueUtil;
    }

    public function find(Request $request){
        $item = Nfe::with(['itens', 'cliente', 'fatura'])->findOrFail($request->id);
        return response()->json($item, 200);
    }

    public function all(Request $request){
        $estado = $request->estado;
        $cliente = $request->cliente;
        $start_date = $request->dataInicio;
        $end_date = $request->dataFim;
        $data = Nfe::where('empresa_id', $request->empresa_id)
        ->with(['itens', 'cliente', 'fatura'])
        ->orderBy('id', 'desc')
        ->select('numero_sequencial', 'total', 'cliente_id', 'desconto', 'acrescimo', 'created_at', 'estado', 'id')
        ->when($estado, function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($cliente, function ($query) use ($cliente) {
            return $query->where('cliente_id', $cliente);
        })
        ->when(($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', \Carbon\Carbon::parse($start_date)->format('Y-m-d'));
        })
        ->when(($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', \Carbon\Carbon::parse($end_date)->format('Y-m-d'));
        })
        // ->get();
        ->paginate($request->get('per_page', env("PAGINACAO")));

        return response()->json($data, 200);
    }

    public function store(Request $request){
        try{
            $nfe = DB::transaction(function () use ($request) {

                $empresa = Empresa::find($request->empresa_id);
                $cliente = Cliente::find($request->cliente);

                $numero_nfe = $empresa->numero_ultima_nfe_producao+1;
                if ($empresa->ambiente == 2) {
                    $numero_nfe = $empresa->numero_ultima_nfe_homologacao+1;
                }

                $total = __convert_value_bd(str_replace("R$ ", "", $request->total));
                $desconto = __convert_value_bd(str_replace("R$ ", "", $request->desconto));
                $acrescimo = __convert_value_bd(str_replace("R$ ", "", $request->acrescimo));
                $valor_recebido = __convert_value_bd(str_replace("R$ ", "", $request->valor_recebido));

                $funcionario = Funcionario::where('codigo', $request->codigo_operador)
                ->where('empresa_id', $request->empresa_id)->first();

                if(!$funcionario->usuario_id){
                    return response()->json("Usuário não vinculado ao funcionário!", 401);
                }

                $caixa = Caixa::where('usuario_id', $funcionario->usuario_id)
                ->where('status', 1)
                ->first();

                if($caixa == null){
                    return response()->json("Caixa está fechado!", 401);
                }

                $objetoVenda = [
                    'cliente_id' => $request->cliente,
                    'natureza_id' => $request->natureza_id,
                    'ambiente' => $empresa->ambiente,
                    'total' => $total,
                    'desconto' => $desconto,
                    'acrescimo' => $acrescimo,
                    'estado' => 'novo',
                    'numero' => $numero_nfe,
                    'chave' => '',
                    'observacao' => $request->observacao,
                    'local_id' => $caixa->local_id,
                    'empresa_id' => $request->empresa_id,
                    'numero_serie' => $empresa->numero_serie_nfce ?? 1,
                    'cliente_cpf_cnpj' => $request->cpf_nota,
                    'cliente_nome' => '',
                    'troco' => $valor_recebido > 0 ? ($valor_recebido - $total) : 0,

                    'emissor_nome' => $empresa->nome,
                    'emissor_cpf_cnpj' => $empresa->cpf_cnpj,

                    'caixa_id' => $caixa->id,
                    'local_id' => $caixa->local_id,
                    'user_id' => $funcionario->usuario_id,
                    'numero_sequencial' => $this->getLastNumero($request->empresa_id)
                ];

                $nfe = Nfe::create($objetoVenda);

                foreach($request->itens as $item){
                    $product = Produto::findOrFail($item['produto_id']);

                    $variacao_id = isset($item['variacao_id']) ? $item['variacao_id'] : null;
                    $cfop = $product->cfop_estadual;
                    if($empresa->cidade->uf != $cliente->cidade->uf){
                        $cfop = $product->cfop_outro_estado;
                    }
                    $itemNfe = ItemNfe::create([
                        'nfe_id' => $nfe->id,
                        'produto_id' => $product->id,
                        'quantidade' => $item['quantidade'],
                        'valor_unitario' => $product->valor_unitario,
                        'sub_total' => $item['sub_total'],
                        'perc_icms' => __convert_value_bd($product->perc_icms),
                        'perc_pis' => __convert_value_bd($product->perc_pis),
                        'perc_cofins' => __convert_value_bd($product->perc_cofins),
                        'perc_ipi' => __convert_value_bd($product->perc_ipi),
                        'cst_csosn' => $product->cst_csosn,
                        'cst_pis' => $product->cst_pis,
                        'cst_cofins' => $product->cst_cofins,
                        'cst_ipi' => $product->cst_ipi,
                        'cfop' => $cfop,
                        'ncm' => $product->ncm,
                        'variacao_id' => $variacao_id,
                    ]);

                    if ($product->gerenciar_estoque) {
                        $this->estoqueUtil->reduzEstoque($product->id, $item['quantidade'], $variacao_id, $caixa->local_id);
                    }

                    $tipo = 'reducao';
                    $codigo_transacao = $nfe->id;
                    $tipo_transacao = 'venda_nfe';
                    $this->estoqueUtil->movimentacaoProduto($product->id, $item['quantidade'], $tipo, $codigo_transacao, $tipo_transacao, $funcionario->usuario_id, $variacao_id);
                }

                foreach($request->fatura as $key => $fatura){
                    FaturaNfe::create([
                        'nfe_id' => $nfe->id,
                        'tipo_pagamento' => $fatura['tipo_pagamento'],
                        'data_vencimento' => $fatura['vencimento'],
                        'valor' => $item['valor']
                    ]);

                    if(strtotime($fatura['vencimento']) > strtotime(date('Y-m-d'))){
                        ContaReceber::create([
                            'empresa_id' => $nfe->empresa_id,
                            'nfe_id' => $nfe->id,
                            'cliente_id' => $request->cliente,
                            'valor_integral' => $item['valor'],
                            'tipo_pagamento' => $fatura['tipo_pagamento'],
                            'data_vencimento' => $fatura['vencimento'],
                            'local_id' => $caixa->local_id,
                            'descricao' => "Parcela " . $key+1 . " de " . sizeof($request->fatura)
                        ]);
                    }
                }


                $comissao = $funcionario->comissao;
                $valorRetorno = $this->calcularComissaoVenda($nfe, $comissao, $nfe->empresa_id);

                if($valorRetorno > 0){
                    ComissaoVenda::create([
                        'funcionario_id' => $funcionario->id,
                        'nfce_id' => null,
                        'nfe_id' => $nfe->id,
                        'tabela' => 'nfe',
                        'valor' => $valorRetorno,
                        'valor_venda' => $nfe->total,
                        'status' => 0,
                        'empresa_id' => $nfe->empresa_id
                    ]);
                }

                return $nfe;
            });
return response()->json($nfe, 200);
}catch(\Exception $e){
    return response()->json($e->getMessage(), 401);
}
}
public function update(Request $request){
    try{
        $nfe = DB::transaction(function () use ($request) {

            $nfe = Nfe::findOrFail($request->id);

            $empresa = $nfe->empresa;
            $cliente = $nfe->cliente;

            $total = __convert_value_bd(str_replace("R$ ", "", $request->total));
            $desconto = __convert_value_bd(str_replace("R$ ", "", $request->desconto));
            $acrescimo = __convert_value_bd(str_replace("R$ ", "", $request->acrescimo));
            $valor_recebido = __convert_value_bd(str_replace("R$ ", "", $request->valor_recebido));

            $funcionario = Funcionario::where('codigo', $request->codigo_operador)
            ->where('empresa_id', $request->empresa_id)->first();

            if(!$funcionario->usuario_id){
                return response()->json("Usuário não vinculado ao funcionário!", 401);
            }

            $caixa = Caixa::where('usuario_id', $funcionario->usuario_id)
            ->where('status', 1)
            ->first();

            if($caixa == null){
                return response()->json("Caixa está fechado!", 401);
            }

            $objetoVenda = [
                'cliente_id' => $request->cliente,
                'natureza_id' => $request->natureza_id,
                'ambiente' => $empresa->ambiente,
                'total' => $total,
                'desconto' => $desconto,
                'acrescimo' => $acrescimo,
                'observacao' => $request->observacao,
                'cliente_cpf_cnpj' => $request->cpf_nota,
                'cliente_nome' => '',
                'troco' => $valor_recebido > 0 ? ($valor_recebido - $total) : 0
            ];

            $nfe->update($objetoVenda)->save();

            foreach($nfe->itens as $it){
                $it->itensDimensao()->delete();
                $it->delete();
            }
            $item->fatura()->delete();
            ContaReceber::where('nfe_id', $item->id)->delete();

            foreach($request->itens as $item){
                $product = Produto::findOrFail($item['produto_id']);

                $variacao_id = isset($item['variacao_id']) ? $item['variacao_id'] : null;
                $cfop = $product->cfop_estadual;
                if($empresa->cidade->uf != $cliente->cidade->uf){
                    $cfop = $product->cfop_outro_estado;
                }
                $itemNfe = ItemNfe::create([
                    'nfe_id' => $nfe->id,
                    'produto_id' => $product->id,
                    'quantidade' => $item['quantidade'],
                    'valor_unitario' => $product->valor_unitario,
                    'sub_total' => $item['sub_total'],
                    'perc_icms' => __convert_value_bd($product->perc_icms),
                    'perc_pis' => __convert_value_bd($product->perc_pis),
                    'perc_cofins' => __convert_value_bd($product->perc_cofins),
                    'perc_ipi' => __convert_value_bd($product->perc_ipi),
                    'cst_csosn' => $product->cst_csosn,
                    'cst_pis' => $product->cst_pis,
                    'cst_cofins' => $product->cst_cofins,
                    'cst_ipi' => $product->cst_ipi,
                    'cfop' => $cfop,
                    'ncm' => $product->ncm,
                    'variacao_id' => $variacao_id,
                ]);

                if ($product->gerenciar_estoque) {
                    $this->estoqueUtil->reduzEstoque($product->id, $item['quantidade'], $variacao_id, $caixa->local_id);
                }

                $tipo = 'reducao';
                $codigo_transacao = $nfe->id;
                $tipo_transacao = 'venda_nfe';
                $this->estoqueUtil->movimentacaoProduto($product->id, $item['quantidade'], $tipo, $codigo_transacao, $tipo_transacao, $funcionario->usuario_id, $variacao_id);
            }

            foreach($request->fatura as $key => $fatura){
                FaturaNfe::create([
                    'nfe_id' => $nfe->id,
                    'tipo_pagamento' => $fatura['tipo_pagamento'],
                    'data_vencimento' => $fatura['vencimento'],
                    'valor' => $item['valor']
                ]);

                if(strtotime($fatura['vencimento']) > strtotime(date('Y-m-d'))){
                    ContaReceber::create([
                        'empresa_id' => $nfe->empresa_id,
                        'nfe_id' => $nfe->id,
                        'cliente_id' => $request->cliente,
                        'valor_integral' => $item['valor'],
                        'tipo_pagamento' => $fatura['tipo_pagamento'],
                        'data_vencimento' => $fatura['vencimento'],
                        'local_id' => $caixa->local_id,
                        'descricao' => "Parcela " . $key+1 . " de " . sizeof($request->fatura)
                    ]);
                }
            }

            $comissao = ComissaoVenda::where('empresa_id', $nfe->empresa_id)
            ->where('nfe_id', $nfe->id)->first();

            if($comissao){
                $comissao->delete();
            }

            $comissao = $funcionario->comissao;
            $valorRetorno = $this->calcularComissaoVenda($nfe, $comissao, $nfe->empresa_id);

            if($valorRetorno > 0){
                ComissaoVenda::create([
                    'funcionario_id' => $funcionario->id,
                    'nfce_id' => null,
                    'nfe_id' => $nfe->id,
                    'tabela' => 'nfe',
                    'valor' => $valorRetorno,
                    'valor_venda' => $nfe->total,
                    'status' => 0,
                    'empresa_id' => $nfe->empresa_id
                ]);
            }

            return $nfe;
        });
return response()->json($nfe, 200);
}catch(\Exception $e){
    return response()->json($e->getMessage(), 401);
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
        $valorRetorno = ((float)$nfce->total * (float)$comissao) / 100;
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

private function getLastNumero($empresa_id){
    $last = Nfe::where('empresa_id', $empresa_id)
    ->orderBy('numero_sequencial', 'desc')
    ->where('numero_sequencial', '>', 0)->first();
    $numero = $last != null ? $last->numero_sequencial : 0;
    $numero++;
    return $numero;
}

public function naturezaOperacao(Request $request){
    $data = NaturezaOperacao::where('empresa_id', $request->empresa_id)
    ->select('id', 'descricao', 'padrao')
    ->get();
    return response()->json($data, 200);
}

public function transportadoras(Request $request){
    $data = Transportadora::where('empresa_id', $request->empresa_id)
    ->select('id', 'razao_social', 'cpf_cnpj')
    ->get();
    return response()->json($data, 200);
}

public function emitir(Request $request)
{

    $nfe = Nfe::findOrFail($request->id);
    $empresa = Empresa::findOrFail($nfe->empresa_id);

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

    $doc = $nfe_service->gerarXml($nfe);

    if(!isset($doc['erros_xml'])){

        $xml = $doc['xml'];
        $chave = $doc['chave'];

        $xmlTemp = simplexml_load_string($xml);

        $itensComErro = "";
        $regime = $empresa->tributacao;
        foreach ($xmlTemp->infNFe->det as $item) {
            if (isset($item->imposto->ICMS)) {
                $icms = (array_values((array)$item->imposto->ICMS));
                if(sizeof($icms) == 0){
                    $itensComErro .= " Produto " . $item->prod->xProd . " não formando a TAG ICMS, confira se o CST do item corresponde a tributação, regime configurado: $regime";
                }
            }
        }

        if($itensComErro){
            return response()->json($itensComErro, 403);
        }
        try{
            $signed = $nfe_service->sign($xml);
            $resultado = $nfe_service->transmitir($signed, $doc['chave']);

            if ($resultado['erro'] == 0) {
                $nfe->chave = $doc['chave'];
                $nfe->estado = 'aprovado';

                if($empresa->ambiente == 2){
                    $empresa->numero_ultima_nfe_homologacao = $doc['numero'];
                }else{
                    $empresa->numero_ultima_nfe_producao = $doc['numero'];
                }
                $nfe->numero = $doc['numero'];
                $nfe->recibo = $resultado['success'];
                $nfe->data_emissao = date('Y-m-d H:i:s');

                $nfe->save();
                $empresa->save();
                $data = [
                    'recibo' => $resultado['success'],
                    'chave' => $nfe->chave
                ];
                $descricaoLog = "Emitida número $nfe->numero - $nfe->chave APROVADA";
                __createLog($nfe->empresa_id, 'NFe', 'transmitir', $descricaoLog);

                try{
                    $fileDir = public_path('xml_nfe/').$nfe->chave.'.xml';
                    $this->emailUtil->enviarXmlContador($nfe->empresa_id, $fileDir, 'NFe', $nfe->chave);
                }catch(\Exception $e){
                }

                try{
                    $fileDir = public_path('xml_nfe/').$nfe->chave.'.xml';
                    $this->siegUtil->enviarXml($nfe->empresa_id, $fileDir);
                }catch(\Exception $e){
                }

                return response()->json($data, 200);
            }else{
                $error = $resultado['error'];
                $recibo = isset($resultado['recibo']) ? $resultado['recibo'] : null;

                $motivo = '';
                if(isset($error['protNFe'])){
                    $motivo = $error['protNFe']['infProt']['xMotivo'];
                    $cStat = $error['protNFe']['infProt']['cStat'];
                    $nfe->motivo_rejeicao = substr("[$cStat] $motivo", 0, 200);
                }

                if($nfe->chave == ''){
                    $nfe->chave = $doc['chave'];
                }

                $descricaoLog = "REJEITADA $nfe->chave - $motivo";
                __createLog($nfe->empresa_id, 'NFe', 'erro', $descricaoLog);
                if($nfe->signed_xml == null){
                    $nfe->signed_xml = $signed;
                }
                if($nfe->recibo == null){
                    $nfe->recibo = $recibo;
                }
                $nfe->estado = 'rejeitado';
                $nfe->save();

                if(isset($error['protNFe'])){
                    return response()->json("[$cStat] $motivo", 403);
                }else{
                    return response()->json($error, 403);
                }
            }
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 404);
        }
    }else{
        return response()->json($doc['erros_xml'], 401);
    }
    return response()->json($doc, 200);
}

public function cancelar(Request $request)
{
    $nfe = Nfe::findOrFail($request->id);
    $empresa = Empresa::findOrFail($nfe->empresa_id);
    if ($nfe != null) {
        $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
        $nfe_service = new NFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$nfe->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            // "schemes" => "PL_009_V4",
            "schemes" => "PL_010_V1.21",
            "versao" => "4.00",
        ], $empresa);
        $doc = $nfe_service->cancelar($nfe, $request->motivo);

        if (!isset($doc['erro'])) {
            $nfe->estado = 'cancelado';
            $nfe->save();

            foreach ($nfe->itens as $i) {
                if ($i->produto->gerenciar_estoque) {
                    if ($nfe->tpNF == 1) {
                        $this->estoqueUtil->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $nfe->local_id);
                    }else{
                        $this->estoqueUtil->reduzEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $nfe->local_id);
                    }
                }
            }
                // return response()->json($doc, 200);
            $motivo = $doc['retEvento']['infEvento']['xMotivo'];
            $cStat = $doc['retEvento']['infEvento']['cStat'];
            if($cStat == 135){
                $descricaoLog = "CANCELADA $nfe->chave";
                __createLog($nfe->empresa_id, 'NFe', 'cancelar', $descricaoLog);

                try{
                    $fileDir = public_path('xml_nfe_cancelada/').$nfe->chave.'.xml';
                    $this->siegUtil->enviarXml($nfe->empresa_id, $fileDir);
                }catch(\Exception $e){
                }

                return response()->json("[$cStat] $motivo", 200);
            }else{
                $descricaoLog = "ERRO CANCELAR: $nfe->chave";
                __createLog($nfe->empresa_id, 'NFe', 'erro', $descricaoLog);
                return response()->json("[$cStat] $motivo", 401);
            }
        } else {
            $arr = $doc['data'];
            $cStat = $arr['retEvento']['infEvento']['cStat'];
            $motivo = $arr['retEvento']['infEvento']['xMotivo'];
            $descricaoLog = "ERRO CANCELAR: $nfe->chave - $motivo";
            __createLog($nfe->empresa_id, 'NFe', 'erro', $descricaoLog);
            return response()->json("[$cStat] $motivo", $doc['status']);
        }
    } else {
        return response()->json('Consulta não encontrada', 404);
    }
}

public function danfe(Request $request){
    $item = Nfe::findOrFail($request->id);
    try{

        if (!is_dir(public_path('comprovantes_nfe'))) {
            mkdir(public_path('comprovantes_nfe'), 0777, true);
        }else{
            $destino = public_path('comprovantes_nfe');
            $this->clearFolder($destino);
        }

        if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfe/') . $item->chave . '.xml');
            $empresa = $item->empresa;

            $danfe = new Danfe($xml);
            if($empresa->logo){
                $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . 
                    $empresa->logo));
                $danfe->logoParameters($logo, 'L');
            }
            $danfe->exibirTextoFatura = 1;
            $pdf = $danfe->render();

            $fileName = "nfe_$item->id.pdf";
            file_put_contents(public_path('comprovantes_nfe/') . $fileName , $pdf);

            $url = env("APP_URL"). "/comprovantes_nfe/".$fileName;
            
            return response()->json($url, 200);

        } else {
            return response()->json("Arquivo não encontrado", 401);
        }

    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function danfeCancela(Request $request){

    if (!is_dir(public_path('comprovantes_nfe'))) {
        mkdir(public_path('comprovantes_nfe'), 0777, true);
    }else{
        $destino = public_path('comprovantes_nfe');
        $this->clearFolder($destino);
    }

    $item = Nfe::findOrFail($request->id);
    try{

        $xml = file_get_contents(public_path('xml_nfe_cancelada/') . $item->chave . '.xml');

        $dadosEmitente = $this->getEmitente($item->empresa);

        $daevento = new Daevento($xml, $dadosEmitente);
        $daevento->debugMode(true);
        $pdf = $daevento->render();

        $fileName = "nfe_$item->id.pdf";
        file_put_contents(public_path('comprovantes_nfe/') . $fileName , $pdf);

        $url = env("APP_URL"). "/comprovantes_nfe/".$fileName;
        return response()->json($url, 200);

    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
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

public function imprimirPedido(Request $request){
    $item = Nfe::findOrFail($request->id);
    if (!is_dir(public_path('comprovantes_nfe'))) {
        mkdir(public_path('comprovantes_nfe'), 0777, true);
    }else{
        $destino = public_path('comprovantes_nfe');
        $this->clearFolder($destino);
    }
    $config = $item->empresa;

    $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

    if($configGeral && $configGeral->mensagem_padrao_impressao_venda){
        $item->observacao .= " " . $configGeral->mensagem_padrao_impressao_venda;
    }

    $p = view('nfe.imprimir', compact('config', 'item', 'configGeral'));

    $domPdf = new Dompdf(["enable_remote" => true]);
    $domPdf->loadHtml($p);
    $domPdf->setPaper("A4");
    $domPdf->render();

    $fileName = "pedido_$item->id.pdf";
    file_put_contents(public_path('comprovantes_nfe/') . $fileName , $domPdf->output());

    $url = env("APP_URL"). "/comprovantes_nfe/".$fileName;

    return response()->json($url, 200);
}

private function clearFolder($destino){
    $files = glob($destino."/*");
    foreach($files as $file){ 
        if(is_file($file)) unlink($file); 
    }
}

}
