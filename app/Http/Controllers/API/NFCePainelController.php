<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\Empresa;
use App\Models\Caixa;
use App\Models\Contigencia;
use App\Models\EmailConfig;
use App\Models\UsuarioEmissao;
use App\Services\NFCeService;
use App\Utils\EmailUtil;
use Mail;
use NFePHP\DA\NFe\Danfce;
use App\Utils\SiegUtil;
use App\Utils\EstoqueUtil;

class NFCePainelController extends Controller
{
    protected $emailUtil;
    protected $siegUtil;

    public function __construct(EmailUtil $util, SiegUtil $siegUtil, EstoqueUtil $estoqueUtil){
        $this->emailUtil = $util;
        $this->siegUtil = $siegUtil;
        $this->estoqueUtil = $estoqueUtil;

        if (!is_dir(public_path('xml_nfce'))) {
            mkdir(public_path('xml_nfce'), 0777, true);
        }
        if (!is_dir(public_path('xml_nfce_cancelada'))) {
            mkdir(public_path('xml_nfce_cancelada'), 0777, true);
        }
    }

    public function emitir(Request $request){

        $nfce = Nfce::findOrFail($request->id);

        $empresa = Empresa::findOrFail($nfce->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $nfce->local_id);
        
        if($empresa->arquivo == null){
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $configUsuarioEmissao = UsuarioEmissao::where('usuario_empresas.empresa_id', $nfce->empresa_id)
        ->join('usuario_empresas', 'usuario_empresas.usuario_id', '=', 'usuario_emissaos.usuario_id')
        ->select('usuario_emissaos.*')
        ->where('usuario_emissaos.usuario_id', $nfce->user_id)
        ->first();

        $nfe_service = new NFCeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$nfce->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            // "schemes" => "PL_009_V4",
            "schemes" => "PL_010_V1.21",
            "versao" => "4.00",
            "CSC" => $empresa->csc,
            "CSCid" => $empresa->csc_id
        ], $empresa);

        $doc = $nfe_service->gerarXml($nfce);

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
                // contigencia condicional
                if($this->getContigencia($nfce->empresa_id)){
                    if(!is_dir(public_path('xml_nfce_contigencia'))){
                        mkdir(public_path('xml_nfce_contigencia'), 0777, true);
                    }

                    $nfce->contigencia = 1;
                    $nfce->reenvio_contigencia = 0;
                    $nfce->chave = $chave;

                    $nfce->estado = 'aprovado';
                    $nfce->numero = $doc['numero'];
                    $nfce->data_emissao = date('Y-m-d H:i:s');
                    $nfce->save();
                    
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

                    file_put_contents(public_path('xml_nfce_contigencia/').$chave.'.xml', $signed);
                    $data = [
                        'recibo' => '',
                        'chave' => $chave,
                        'contigencia' => 1
                    ];
                    $descricaoLog = 'Emitida em contigência número ' . $doc['numero'];
                    __createLog($nfce->empresa_id, 'NFCe', 'transmitir', $descricaoLog);
                    return response()->json($data, 200);

                }else{
                    $resultado = $nfe_service->transmitir($signed, $doc['chave']);
                    // $nfce->contigencia = $this->getContigencia($nfce->empresa_id);
                    $nfce->reenvio_contigencia = 0;

                    if ($resultado['erro'] == 0) {
                        $nfce->chave = $doc['chave'];
                        $nfce->estado = 'aprovado';

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

                        try{
                            $fileDir = public_path('xml_nfce/').$nfce->chave.'.xml';
                            $this->emailUtil->enviarXmlContador($nfce->empresa_id, $fileDir, 'NFCe', $nfce->chave);
                        }catch(\Exception $e){

                        }

                        try{
                            $fileDir = public_path('xml_nfce/').$nfce->chave.'.xml';
                            $this->siegUtil->enviarXml($nfce->empresa_id, $fileDir);
                        }catch(\Exception $e){

                        }

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
                }
            }catch(\Exception $e){
                __createLog($nfce->empresa_id, 'NFCe', 'erro', $e->getMessage());
                return response()->json($e->getMessage(), 404);
            }

        }else{
            return response()->json($doc['erros_xml'], 401);
        }
    }

    private function getContigencia($empresa_id){
        $active = Contigencia::
        where('empresa_id', $empresa_id)
        ->where('status', 1)
        ->where('documento', 'NFCe')
        ->first();
        return $active != null ? 1 : 0;
    }

    public function cancelar(Request $request)
    {
        $nfce = Nfce::findOrFail($request->id);
        $empresa = Empresa::findOrFail($nfce->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $nfce->local_id);

        if ($nfce != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
            $nfe_service = new NFCeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$nfce->ambiente,
                "razaosocial" => $empresa->nome,
                "siglaUF" => $empresa->cidade->uf,
                "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ], $empresa);
            $doc = $nfe_service->cancelar($nfce, $request->motivo);

            if (!isset($doc['erro'])) {
                $nfce->estado = 'cancelado';

                foreach ($nfce->itens as $i) {
                    if ($i->produto->gerenciar_estoque) {
                        $this->estoqueUtil->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $nfce->local_id);
                    }
                }
                $nfce->save();
                // return response()->json($doc, 200);
                $motivo = $doc['retEvento']['infEvento']['xMotivo'];
                $cStat = $doc['retEvento']['infEvento']['cStat'];

                if($cStat == 135){
                    $descricaoLog = "CANCELADA $nfce->chave";
                    __createLog($nfce->empresa_id, 'NFCe', 'cancelar', $descricaoLog);

                    try{
                        $fileDir = public_path('xml_nfce_cancelada/').$nfce->chave.'.xml';
                        $this->siegUtil->enviarXml($nfce->empresa_id, $fileDir);
                    }catch(\Exception $e){
                    }

                    return response()->json("[$cStat] $motivo", 200);
                }else{
                    $descricaoLog = "ERRO CANCELAR: $nfce->chave";
                    __createLog($nfce->empresa_id, 'NFCe', 'erro', $descricaoLog);
                    return response()->json("[$cStat] $motivo", 401);
                }
            } else {
                $arr = $doc['data'];
                if(!isset($arr['retEvento'])){
                    return response()->json($arr, $doc['status']);
                }

                $cStat = $arr['retEvento']['infEvento']['cStat'];
                $motivo = $arr['retEvento']['infEvento']['xMotivo'];
                
                __createLog($nfce->empresa_id, 'NFCe', 'erro', "[$cStat] $motivo");
                return response()->json("[$cStat] $motivo", $doc['status']);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function consultar(Request $request)
    {
        $nfce = Nfce::findOrFail($request->id);
        $empresa = Empresa::findOrFail($nfce->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $nfce->local_id);
        
        if ($nfce != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
            $nfe_service = new NFCeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$nfce->ambiente,
                "razaosocial" => $empresa->nome,
                "siglaUF" => $empresa->cidade->uf,
                "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ], $empresa);
            $consulta = $nfe_service->consultar($nfce);
            if (!isset($consulta['erro'])) {
                try{

                    $motivo = $consulta['protNFe']['infProt']['xMotivo'];
                    $cStat = $consulta['protNFe']['infProt']['cStat'];
                    if($cStat == 100){
                        return response()->json("[$cStat] $motivo", 200);
                    }else{
                        return response()->json("[$cStat] $motivo", 401);
                    }
                }catch(\Exception $e){
                    return response()->json($consulta['cStat'] . " " . $consulta['xMotivo'], 404);
                }
            }else{
                return response()->json($consulta['data'], $consulta['status']);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function consultaStatusSefaz(Request $request){
        $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
        $empresa = Empresa::findOrFail($request->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $caixa ? $caixa->local_id : null);

        $nfce_service = new NFCeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
        ], $empresa);
        $consulta = $nfce_service->consultaStatus((int)$empresa->ambiente, $empresa->cidade->uf);
        return response()->json($consulta, 200);
    }

    public function transmitirContigencia(Request $request){

        $nfce = Nfce::findOrFail($request->id);

        $empresa = Empresa::findOrFail($nfce->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $nfce->local_id);
        
        if($empresa->arquivo == null){
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        if(!file_exists(public_path('xml_nfce_contigencia/'.$nfce->chave.'.xml'))){
            return response()->json("arquivo não existe", 401);
        }
        $nfe_service = new NFCeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$nfce->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "CSC" => $empresa->csc,
            "CSCid" => $empresa->csc_id
        ], $empresa);

        $xml = file_get_contents(public_path('xml_nfce_contigencia/'.$nfce->chave.'.xml'));
        $resultado = $nfe_service->transmitir($xml, $nfce->chave);

        if ($resultado['erro'] == 0) {

            $nfce->estado = 'aprovado';
            $nfce->reenvio_contigencia = 1;
            $nfce->save();

            $data = [
                'recibo' => $resultado['success'],
                'chave' => $nfce->chave
            ];
            return response()->json($data, 200);

        }else{
            $error = $resultado['error'];
            $nfce->estado = 'rejeitado';
            $nfce->save();

            if(isset($error['protNFe'])){
                $motivo = $error['protNFe']['infProt']['xMotivo'];
                $cStat = $error['protNFe']['infProt']['cStat'];

                $nfce->motivo_rejeicao = substr("[$cStat] $motivo", 0, 200);
                $nfce->save();

                return response()->json("[$cStat] $motivo", 403);
            }else{
                return response()->json($error, 403);
            }
        }

    }

    public function sendMail(Request $request){
        $email = $request->email;
        $xml = $request->xml;
        $danfe = $request->danfe;
        $id = $request->id;

        $nfce = Nfce::findOrFail($id);
        if(!$email){
            return response()->json('Informe o email!', 404);
        }

        $docs = [];
        if($xml){
            $docs[] = public_path('xml_nfce/').$nfce->chave.'.xml';
        }
        if($danfe){
            $this->gerarDanfceTemporaria($nfce);
            $docs[] = public_path('danfce/').$nfce->chave.'.pdf';
        }

        $emailConfig = EmailConfig::where('empresa_id', $nfce->empresa_id)
        ->where('status', 1)
        ->first();
        try{
            if($emailConfig != null){

                $body = view('mail.nfce', compact('nfce'));
                $result = $this->emailUtil->enviaEmailPHPMailer($email, 'Envio de documento', $body, $emailConfig, $docs);
            }else{
                Mail::send('mail.nfce', ['nfce' => $nfce], function($m) use ($email, $docs){
                    $nomeEmail = env('MAIL_FROM_NAME');
                    $m->from(env('MAIL_USERNAME'), $nomeEmail);
                    $m->subject('Envio de documento');
                    foreach($docs as $f){
                        $m->attach($f); 
                    }
                    $m->to($email);
                });
            }

            $this->unlinkr(public_path('danfce'));
            return response()->json("Email enviado!", 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 404);
        }

    }

    private function gerarDanfceTemporaria($nfce){
        if (!is_dir(public_path('danfce'))) {
            mkdir(public_path('danfce'), 0777, true);
        }
        $xml = file_get_contents(public_path('xml_nfce/').$nfce->chave.'.xml');
        $danfe = new Danfce($xml);
        $pdf = $danfe->render();
        file_put_contents(public_path('danfce/') . $nfce->chave . '.pdf', $pdf);

    }

    private function unlinkr($dir){ 
        $files = array_diff(scandir($dir), array('.', '..')); 

        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? $this->unlinkr("$dir/$file") : unlink("$dir/$file"); 
        }

        return rmdir($dir); 
    }

}
