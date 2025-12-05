<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CteOs;
use App\Models\Empresa;
use App\Services\CTeOsService;
use Illuminate\Http\Request;
use NFePHP\DA\CTe\Dacte;
use NFePHP\DA\CTe\Daevento;

class CTeOsPainelController extends Controller
{
    public function emitir(Request $request)
    {

        $item = CteOs::findOrFail($request->id);

        $empresa = Empresa::findOrFail($item->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $item->local_id);

        $cte_service = new CTeOsService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => $empresa->cpf_cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
        ], $empresa);

        $doc = $cte_service->gerarCTe($item);

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];
            $chave = $doc['chave'];

            $signed = $cte_service->sign($xml);
            $resultado = $cte_service->transmitir($signed, $chave);
            // return response()->json($resultado, 403);

            if ($resultado['erro'] == 0) {
                $item->chave = $doc['chave'];
                $item->estado_emissao = 'aprovado';

                if ($empresa->ambiente == 2) {
                    $empresa->numero_ultima_cte_homologacao = $doc['nCte'];
                } else {
                    $empresa->numero_ultima_cte_producao = $doc['nCte'];
                }
                $empresa->save();

                $item->numero_emissao = $doc['nCte'];
                $item->recibo = $resultado['success'];
                $item->save();
                $data = [
                    'recibo' => $resultado['success'],
                    'chave' => $item->chave
                ];
                return response()->json($data, 200);
            } else {
                $error = $resultado['error'];

                if (isset($error['protCTe'])) {
                    $motivo = $error['protCTe']['infProt']['xMotivo'];
                    $cStat = $error['protCTe']['infProt']['cStat'];
                    $item->motivo_rejeicao = substr("[$cStat] $motivo", 0, 200);
                }
                $item->chave = $doc['chave'];
                $item->estado_emissao = 'rejeitado';
                $item->save();

                if (isset($error['protCTe'])) {
                    return response()->json("[$cStat] $motivo", 403);
                } else {
                    return response()->json($error, 403);
                }
            }
        } else {
            return response()->json($doc['erros_xml'], 401);
        }
    }

    public function consultar(Request $request)
    {
        $item = CteOs::findOrFail($request->id);
        $empresa = Empresa::findOrFail($item->empresa_id);
        if ($item != null) {
            $empresa = __objetoParaEmissao($empresa, $item->local_id);
            
            $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
            $cte_service = new CTeOsService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$empresa->ambiente,
                "razaosocial" => $empresa->nome,
                "siglaUF" => $empresa->cidade->uf,
                "cnpj" => $empresa->cpf_cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ], $empresa);
            $consulta = $cte_service->consultar($item);
            if (!isset($consulta['erro'])) {
                $motivo = $consulta['protCTe']['infProt']['xMotivo'];
                $cStat = $consulta['protCTe']['infProt']['cStat'];
                if ($cStat == 100) {
                    return response()->json("[$cStat] $motivo", 200);
                } else {
                    return response()->json("[$cStat] $motivo", 401);
                }
            } else {
                return response()->json($consulta['data'], $consulta['status']);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function cancelar(Request $request)
    {
        $item = CteOs::findOrFail($request->id);
        $empresa = Empresa::findOrFail($item->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $item->local_id);
        
        if ($item != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
            $cte_service = new CTeOsService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$empresa->ambiente,
                "razaosocial" => $empresa->nome,
                "siglaUF" => $empresa->cidade->uf,
                "cnpj" => $empresa->cpf_cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ], $empresa);
            $doc = $cte_service->cancelar($item, $request->motivo);

            if (!isset($doc['erro'])) {
                $item->estado_emissao = 'cancelado';
                $item->save();

                $motivo = $doc['infEvento']['xMotivo'];
                $cStat = $doc['infEvento']['cStat'];
                if ($cStat == 135) {
                    return response()->json("[$cStat] $motivo", 200);
                } else {
                    return response()->json("[$cStat] $motivo", 401);
                }
            } else {
                $arr = $doc['data'];
                $cStat = $arr['infEvento']['cStat'];
                $motivo = $arr['infEvento']['xMotivo'];

                return response()->json("[$cStat] $motivo", $doc['status']);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function corrigir(Request $request)
    {
        $item = CteOs::findOrFail($request->id);

        $empresa = Empresa::findOrFail($item->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $item->local_id);

        $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
        $cte_service = new CTeOsService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => $empresa->cpf_cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
        ], $empresa);
        $cte = $cte_service->cartaCorrecao($item, $request->grupo, $request->campo, $request->motivo);

        if (!isset($cte['erro'])) {

            return response()->json($cte['infEvento']['cStat'] . " - " . $cte['infEvento']['xMotivo'], 200);
        } else {
            return response()->json($cte['data'], $cte['status']);
        }
    }
}
