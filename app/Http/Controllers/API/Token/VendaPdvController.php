<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\ItemNfce;
use App\Models\FaturaNfce;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Caixa;
use Illuminate\Support\Facades\DB;
use App\Utils\EstoqueUtil;

class VendaPdvController extends Controller
{
    protected $prefix = 'vendas_pdv';
    protected $utilEstoque;

    public function __construct(EstoqueUtil $utilEstoque){
        $this->utilEstoque = $utilEstoque;
    }

    public function index(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $data = Nfce::where('empresa_id', $request->empresa_id)
        ->with(['itens', 'fatura'])
        ->get()
        ->each(function($row)
        {
            $row->setHidden(['local_id', 'reenvio_contigencia', 'contigencia', 'user_id', 'signed_xml', 'ambiente',
                'emissor_nome', 'emissor_cpf_cnpj', 'lista_id']);
        });
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $empresa_id = $request->empresa_id;
        $item = Nfce::where('empresa_id', $empresa_id)
        ->with(['itens', 'fatura'])->findOrFail($id);

        $item->setHidden(['local_id', 'reenvio_contigencia', 'contigencia', 'user_id', 'signed_xml', 'ambiente',
            'emissor_nome', 'emissor_cpf_cnpj', 'lista_id']);
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);

        return response()->json($item, 200);
    }

    public function store(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".create")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $validaRequest = $this->validaRequest($request);
            if(sizeof($validaRequest) > 0){
                return response()->json($validaRequest, 403);
            }

            if(!$request->usuario_id){
                return response()->json("É necessário informar o usuário!", 403);
            }

            $caixa = Caixa::where('usuario_id', $request->usuario_id)
            ->where('status', 1)
            ->where('empresa_id', $request->empresa_id)
            ->first();

            if($caixa == null){
                return response()->json("É necessário abrir o caixa!", 403);
            }

            $nfce = DB::transaction(function () use ($request, $caixa) {
                $empresa = Empresa::findOrFail($request->empresa_id);
                $numero = $empresa->numero_ultima_nfce_producao;
                if ($empresa->ambiente == 2) {
                    $numero = $empresa->numero_ultima_nfce_homologacao;
                }

                $chaveSat = "";
                if(isset($request->chave_sat) && isset($request->xml_sat)){
                    if (!is_dir(public_path('xml_sat'))) {
                        mkdir(public_path('xml_sat'), 0777, true);
                    }
                    $chaveSat = $request->chave_sat;
                    $xml = $request->xml_sat;
                    file_put_contents(public_path('xml_sat/').$chaveSat.'.xml', $xml);
                }

                $chaveNfce = "";
                $estado = 'novo';
                if(isset($request->chave_nfce) && isset($request->xml_nfce) && isset($request->numero_nfce)){
                    $chaveNfce = $request->chave_nfce;
                    $xml = $request->xml_nfce;
                    $numero = $request->numero_nfce;
                    $estado = 'aprovado';
                    file_put_contents(public_path('xml_nfce/').$chaveNfce.'.xml', $xml);
                }

                $data = [
                    "cliente_nome" => $request->cliente_nome ?? "",
                    "cliente_cpf_cnpj" => $request->cliente_cpf_cnpj ?? "",
                    "cliente_id" => $request->cliente_id ?? null,
                    "total" => __convert_value_bd($request->total),
                    "desconto" => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                    "acrescimo" => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                    "observacao" => $request->observacao ?? "",
                    "empresa_id" => $request->empresa_id,
                    "local_id" => $caixa->local_id,
                    "natureza_id" => $empresa->natureza_id_pdv,
                    "numero_serie" => $empresa->numero_serie_nfce ?? 1,
                    "numero" => $numero,
                    "caixa_id" => $caixa->id,
                    "user_id" => $request->usuario_id,
                    'chave_sat' => $chaveSat,
                    'chave' => $chaveNfce,
                    "estado" => $estado,
                ];

                $nfce = Nfce::create($data);

                foreach($request->itens as $key => $i){
                    $produto = Produto::
                    where('empresa_id', $request->empresa_id)
                    ->findOrFail($i['produto_id']);

                    $cfop = $produto->cfop_estadual;
                    if($request->cliente_id){


                        $cliente = Cliente::
                        where('empresa_id', $request->empresa_id)
                        ->findOrFail($request->cliente_id);

                        if($empresa->cidade->uf != $cliente->cidade->uf){
                            $cfop = $produto->cfop_outro_estado;
                        }
                    }
                    ItemNfce::create([
                        'nfce_id' => $nfce->id,
                        'quantidade' => __convert_value_bd($i['quantidade']),
                        'produto_id' => $i['produto_id'],
                        'valor_unitario' => __convert_value_bd($i['valor_unitario']),
                        'sub_total' => __convert_value_bd($i['valor_unitario']) * __convert_value_bd($i['quantidade']),
                        'perc_icms' => $produto->perc_icms,
                        'perc_pis' => $produto->perc_pis,
                        'perc_cofins' => $produto->perc_cofins,
                        'perc_ipi' => $produto->perc_ipi,
                        'cst_csosn' => $produto->cst_csosn,
                        'cst_pis' => $produto->cst_pis,
                        'cst_cofins' => $produto->cst_cofins,
                        'cst_ipi' => $produto->cst_ipi,
                        'perc_red_bc' => $produto->perc_red_bc ?? 0,
                        'ncm' => $produto->ncm,
                        'origem' => $produto->origem,
                        'cEnq' => $produto->cEnq,
                        'pST' => $produto->pST,
                        'vBCSTRet' => $produto->vBCSTRet,
                        'cest' => $produto->cest,
                        'codigo_beneficio_fiscal' => $produto->codigo_beneficio_fiscal,
                        'cfop' => $cfop,
                    ]);
                }

                foreach($request->fatura as $key => $f){
                    FaturaNfce::create([
                        'nfce_id' => $nfce->id,
                        'tipo_pagamento' => $f['tipo_pagamento'],
                        'data_vencimento' => !isset($f['vencimento']) ? $f['vencimento'] : date('Y-m-d'),
                        'valor' => __convert_value_bd($f['valor'])
                    ]);
                }

                return $nfce;
            });

$item = Nfce::with(['itens', 'fatura'])->findOrFail($nfce->id);

$item->setHidden(['local_id', 'reenvio_contigencia', 'contigencia', 'user_id', 'signed_xml', 'ambiente',
    'emissor_nome', 'emissor_cpf_cnpj', 'lista_id']);

__createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'create', $this->prefix);
return response()->json($item, 200);

}catch(\Exception $e){
    __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'create', $this->prefix);
    return response()->json("Algo deu errado: " . $e->getMessage(), 403);
}
}

public function update(Request $request){
    if(!__validaPermissaoToken($request->token, $this->prefix.".update")){
        return response()->json("Permissão negada!", 403);
    }
    try{
        $empresa_id = $request->empresa_id;

        $item = Nfce::where('empresa_id', $empresa_id)
        ->findOrFail($request->id);

        if($item == null){
            return response()->json("Venda não encontrada!", 403);
        }

        if($item->empresa_id !=  $empresa_id){
            return response()->json("Não permitido!", 403);
        }

        DB::transaction(function () use ($request, $item) {
            $empresa = Empresa::findOrFail($request->empresa_id);
            $numero = $empresa->numero_ultima_nfce_producao;
            if ($empresa->ambiente == 2) {
                $numero = $empresa->numero_ultima_nfce_homologacao;
            }
            $data = [
                "cliente_nome" => $request->cliente_nome ?? "",
                "cliente_cpf_cnpj" => $request->cliente_cpf_cnpj ?? "",
                "cliente_id" => $request->cliente_id ?? null,
                "total" => __convert_value_bd($request->total),
                "desconto" => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                "acrescimo" => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                "observacao" => $request->observacao ?? "",
            ];

            $item->update($data);

            if(isset($request->itens)){
                $validaRequest = $this->validaRequestItens($request);
                if(sizeof($validaRequest) > 0){
                    return response()->json($validaRequest, 403);
                }

                $item->itens()->delete();
            }

            if(isset($request->fatura)){
                $validaRequest = $this->validaRequestFatura($request);
                if(sizeof($validaRequest) > 0){
                    return response()->json($validaRequest, 403);
                }

                $item->fatura()->delete();
            }

            foreach($request->itens as $key => $i){
                $produto = Produto::
                where('empresa_id', $request->empresa_id)
                ->findOrFail($i['produto_id']);

                $cfop = $produto->cfop_estadual;
                if($request->cliente_id){


                    $cliente = Cliente::
                    where('empresa_id', $request->empresa_id)
                    ->findOrFail($request->cliente_id);

                    if($empresa->cidade->uf != $cliente->cidade->uf){
                        $cfop = $produto->cfop_outro_estado;
                    }
                }
                ItemNfce::create([
                    'nfce_id' => $item->id,
                    'quantidade' => __convert_value_bd($i['quantidade']),
                    'produto_id' => $i['produto_id'],
                    'valor_unitario' => __convert_value_bd($i['valor_unitario']),
                    'sub_total' => __convert_value_bd($i['valor_unitario']) * __convert_value_bd($i['quantidade']),
                    'perc_icms' => $produto->perc_icms,
                    'perc_pis' => $produto->perc_pis,
                    'perc_cofins' => $produto->perc_cofins,
                    'perc_ipi' => $produto->perc_ipi,
                    'cst_csosn' => $produto->cst_csosn,
                    'cst_pis' => $produto->cst_pis,
                    'cst_cofins' => $produto->cst_cofins,
                    'cst_ipi' => $produto->cst_ipi,
                    'perc_red_bc' => $produto->perc_red_bc,
                    'ncm' => $produto->ncm,
                    'origem' => $produto->origem,
                    'cEnq' => $produto->cEnq,
                    'pST' => $produto->pST,
                    'vBCSTRet' => $produto->vBCSTRet,
                    'cest' => $produto->cest,
                    'codigo_beneficio_fiscal' => $produto->codigo_beneficio_fiscal,
                    'cfop' => $cfop,
                ]);
            }


            foreach($request->fatura as $key => $f){
                FaturaNfce::create([
                    'nfce_id' => $item->id,
                    'tipo_pagamento' => $f['tipo_pagamento'],
                    'data_vencimento' => !isset($f['vencimento']) ? $f['vencimento'] : date('Y-m-d'),
                    'valor' => __convert_value_bd($f['valor'])
                ]);
            }

        });

        $item = Nfce::with(['itens', 'fatura'])->findOrFail($item->id);

        $item->setHidden(['local_id', 'reenvio_contigencia', 'contigencia', 'user_id', 'signed_xml', 'ambiente',
            'emissor_nome', 'emissor_cpf_cnpj', 'lista_id']);

        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'update', $this->prefix);
        return response()->json($item, 200);

    }catch(\Exception $e){
        __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'update', $this->prefix);
        return response()->json("Algo deu errado: " . $e->getMessage(), 403);
    }
}

private function validaRequestItens($request){
    $dataMessage = [];
    foreach($request->itens as $key => $i){
        if(!isset($i['produto_id'])){
            array_push($dataMessage, "Informe o produto_id do item " . $key+1);
        }
        if(!isset($i['quantidade'])){
            array_push($dataMessage, "Informe a quantidade do item " . $key+1);
        }
        if(!isset($i['valor_unitario'])){
            array_push($dataMessage, "Informe o valor_unitario do item " . $key+1);
        }
    }
    return $dataMessage;
}

private function validaRequestFatura($request){
    $dataMessage = [];
    foreach($request->fatura as $key => $i){
        if(!isset($i['tipo_pagamento'])){
            array_push($dataMessage, "Informe o tipo_pagamento da fatura " . $key+1);
        }
        if(!isset($i['valor'])){
            array_push($dataMessage, "Informe o valor da fatura " . $key+1);
        }
    }
    return $dataMessage;
}

private function validaRequest($request){
    $dataMessage = [];
    if(!isset($request->total) || $request->total <= 0){
        array_push($dataMessage, "Informe o total da venda");
    }
    if(!isset($request->itens) || sizeof($request->itens) == 0){
        array_push($dataMessage, "Informe os itens da venda");
    }
    if(!isset($request->fatura) || sizeof($request->fatura) == 0){
        array_push($dataMessage, "Informe a fatura da venda");
    }

    if(isset($request->itens)){
        foreach($request->itens as $key => $i){
            if(!isset($i['produto_id'])){
                array_push($dataMessage, "Informe o produto_id do item " . $key+1);
            }
            if(!isset($i['quantidade'])){
                array_push($dataMessage, "Informe a quantidade do item " . $key+1);
            }
            if(!isset($i['valor_unitario'])){
                array_push($dataMessage, "Informe o valor_unitario do item " . $key+1);
            }
        }
    }

    if(isset($request->fatura)){
        foreach($request->fatura as $key => $i){
            if(!isset($i['tipo_pagamento'])){
                array_push($dataMessage, "Informe o tipo_pagamento da fatura " . $key+1);
            }
            if(!isset($i['valor'])){
                array_push($dataMessage, "Informe o valor da fatura " . $key+1);
            }
        }
    }

    return $dataMessage;
}

public function delete(Request $request){
    if(!__validaPermissaoToken($request->token, $this->prefix.".delete")){
        return response()->json("Permissão negada!", 403);
    }
    try{
        $empresa_id = $request->empresa_id;

        $item = Nfce::where('empresa_id', $empresa_id)
        ->with(['itens', 'fatura'])->find($request->id);
        if($item == null){
            return response()->json("Venda não encontrada!", 403);
        }

        if($item->empresa_id !=  $empresa_id){
            return response()->json("Não permitido!", 403);
        }

        foreach ($item->itens as $i) {
            if ($i->produto && $i->produto->gerenciar_estoque) {
                $this->utilEstoque->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
            }
        }
        $item->itens()->delete();
        $item->fatura()->delete();
        $item->contaReceber()->delete();
        $item->delete();
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'delete', $this->prefix);
        return response()->json($item, 200);

    }catch(\Exception $e){
        __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'delete', $this->prefix);
        return response()->json("Algo deu errado: " . $e->getMessage(), 403);
    }
}
}
