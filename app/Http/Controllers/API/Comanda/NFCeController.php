<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NFCeService;
use App\Models\Nfce;
use App\Models\Empresa;
use App\Models\UsuarioEmissao;

class NFCeController extends Controller
{
    public function emitir(Request $request)
    {

        $nfce = Nfce::findOrFail($request->id);
        $empresa = Empresa::findOrFail($nfce->empresa_id);

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

        try {

            $doc = $nfce_service->gerarXml($nfce);
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

                $signed = $nfce_service->sign($xml);

                $resultado = $nfce_service->transmitir($signed, $doc['chave']);
                    // $nfce->contigencia = $this->getContigencia($nfce->empresa_id);
                $nfce->reenvio_contigencia = 0;

                if ($resultado['erro'] == 0) {
                    $nfce->chave = $doc['chave'];
                    $nfce->estado = 'aprovado';
                    if($nfce->user){

                        $configUsuarioEmissao = UsuarioEmissao::where('usuario_empresas.empresa_id', $nfce->empresa_id)
                        ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
                        ->select('usuario_emissaos.*')
                        ->where('usuario_emissaos.usuario_id', $nfce->user_id)
                        ->first();
                        if($configUsuarioEmissao == null){
                            if($empresa->ambiente == 2){
                                $empresa->numero_ultima_nfce_homologacao = $doc['numero'];
                            }else{
                                $empresa->numero_ultima_nfce_producao = $doc['numero'];
                            }
                            $empresa->save();
                        }else{
                            $configUsuarioEmissao->numero_ultima_nfce = $doc['numero'];
                            $configUsuarioEmissao->save();
                        }
                    }

                    $nfce->numero = $doc['numero'];
                    $nfce->recibo = $resultado['success'];
                    $nfce->data_emissao = date('Y-m-d H:i:s');

                    $nfce->save();

                    $data = [
                        'recibo' => $resultado['success'],
                        'chave' => $nfce->chave
                    ];

                    $descricaoLog = "Emitida número $nfce->numero - $nfce->chave APROVADA";
                    __createLog($nfce->empresa_id, 'NFCe', 'transmitir', $descricaoLog);

                    // try{
                    //     $fileDir = public_path('xml_nfce/').$nfce->chave.'.xml';
                    //     $this->emailUtil->enviarXmlContador($nfce->empresa_id, $fileDir, 'NFCe', $nfce->chave);
                    // }catch(\Exception $e){

                    // }

                    // try{
                    //     $fileDir = public_path('xml_nfce/').$nfce->chave.'.xml';
                    //     $this->siegUtil->enviarXml($nfce->empresa_id, $fileDir);
                    // }catch(\Exception $e){

                    // }

                    return response()->json($data, 200);
                }else{
                    $recibo = isset($resultado['recibo']) ? $resultado['recibo'] : null;

                    $error = $resultado['error'];

                    if($nfce->chave == ''){
                        $nfce->chave = $doc['chave'];
                    }

                    if($nfce->signed_xml == null){
                        $nfce->signed_xml = $signed;
                    }
                    if($nfce->recibo == null){
                        $nfce->recibo = $recibo;
                    }
                    $nfce->estado = 'rejeitado';
                    $nfce->save();

                    if(isset($error['protNFe'])){
                        $motivo = $error['protNFe']['infProt']['xMotivo'];
                        $cStat = $error['protNFe']['infProt']['cStat'];

                        $nfce->motivo_rejeicao = substr("[$cStat] $motivo", 0, 200);
                        $nfce->save();

                        $descricaoLog = "REJEITADA $nfce->chave - $motivo";
                        __createLog($nfce->empresa_id, 'NFCe', 'erro', $descricaoLog);

                        return response()->json("[$cStat] $motivo", 403);
                    }else{
                        return response()->json($error, 403);
                    }
                }


            }else{
                return response()->json($doc['erros_xml'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(" error: " . $e->getMessage() . ", line: " . $e->getLine(), 404);
        }
    }
}
