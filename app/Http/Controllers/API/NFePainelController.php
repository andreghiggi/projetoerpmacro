<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Nfe;
use App\Models\Caixa;
use App\Models\EmailConfig;
use App\Models\Inutilizacao;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\ProdutoUnico;
use App\Services\NFeService;
use NFePHP\DA\NFe\Danfe;
use App\Models\Contigencia;
use App\Utils\EmailUtil;
use Mail;
use App\Utils\SiegUtil;
use App\Utils\EstoqueUtil;

class NFePainelController extends Controller
{

    protected $emailUtil;
    protected $siegUtil;
    protected $estoqueUtil;
    
    public function __construct(EmailUtil $util, SiegUtil $siegUtil, EstoqueUtil $estoqueUtil){
        $this->emailUtil = $util;
        $this->siegUtil = $siegUtil;
        $this->estoqueUtil = $estoqueUtil;

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
    }

    private function getContigencia($empresa_id){
        $active = Contigencia::
        where('empresa_id', $empresa_id)
        ->where('status', 1)
        ->where('documento', 'NFe')
        ->first();
        return $active != null ? 1 : 0;
    }

    public function emitir(Request $request){

        $nfe = Nfe::findOrFail($request->id);

        $empresa = Empresa::findOrFail($nfe->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $nfe->local_id);

        if($empresa->arquivo == null){
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

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

            if($this->validarTagsICMS($xml) != ""){
                return response()->json("XML inválido: não foi encontrado o grupo ICMS/ICMSSN correspondente ao CST/CSOSN informado no " . $this->validarTagsICMS($xml), 403);
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
                    $nfe->contigencia = $this->getContigencia($nfe->empresa_id);
                    // $nfe->ambiente = $empresa->ambiente
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

                    // return response()->json($resultado, 401);
                    $motivo = '';
                    if(isset($error['protNFe'])){
                        $motivo = $error['protNFe']['infProt']['xMotivo'];
                        $cStat = $error['protNFe']['infProt']['cStat'];
                        $nfe->motivo_rejeicao = substr("[$cStat] $motivo", 0, 200);
                    }

                    // $nfe->numero = isset($documento['numero_nfe']) ? $documento['numero_nfe'] : 
                    // Nfe::lastNumero($empresa);
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
                $msg = $this->formatarErroNFe($e->getMessage());
                return response()->json($msg, 404);
            }

        }else{
            return response()->json($doc['erros_xml'], 401);
        }
    }

    
    function validarTagsICMS($xmlString)
    {
        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($xmlString);
        $retorno = "";
        $cont = 1;
        foreach ($xml->infNFe->det as $det) {

            $nItem = (string) $det['nItem'];

            $imposto = $det->imposto;
            $temTag = 0;
            $gruposICMS = [
                'ICMS00','ICMS10','ICMS20','ICMS30','ICMS40','ICMS51',
                'ICMS60', 'ICMS61','ICMS70','ICMS90','ICMSST'
            ];

            foreach ($gruposICMS as $g) {
                if (isset($imposto->ICMS->{$g})) {
                    $temTag = 1;
                }
            }
            if (isset($imposto->ICMS->ICMSST)) {
                $temTag = 1;
            }

            $gruposSN = [
                'ICMSSN101','ICMSSN102','ICMSSN201','ICMSSN202','ICMSSN203',
                'ICMSSN500','ICMSSN900', 'ICMS61'
            ];

            foreach ($gruposSN as $g) {
                if (isset($imposto->ICMS->{$g})) {
                    $temTag = 1;
                }
            }

            if($temTag == 0){
                $retorno .= " item $cont.";
            }
            $cont++;
        }

        return $retorno;
    }

    private function formatarErroNFe($mensagem)
    {
        if (preg_match('/cClassTrib.*pattern.*not accepted/', $mensagem)) {
            return "O código de classificação tributária (cClassTrib) deve conter 6 dígitos numéricos. Verifique o campo e corrija o valor enviado (exemplo: 000002).";
        }

        if (preg_match('/ICMSSN900.*Expected.*vICMS/', $mensagem)) {
            return "No regime Simples Nacional com CSOSN 900 é obrigatório informar o campo vICMS (mesmo que seja 0,00). Ajuste o XML e tente novamente.";
        }

        if (preg_match('/This element is not expected/', $mensagem)) {
            preg_match('/Element.*\{.*\}(.*?)\'.*Expected.*\{.*\}(.*?)\)/', $mensagem, $matches);
            if (isset($matches[1], $matches[2])) {
                return "Campo inválido no XML: <strong>{$matches[1]}</strong>. Esperado: <strong>{$matches[2]}</strong>.";
            }
            return "Erro de estrutura no XML: verifique os campos do ICMS.";
        }

        return "Erro ao validar XML. Detalhes técnicos: " . htmlspecialchars($mensagem);
    }

    public function cancelar(Request $request)
    {
        $nfe = Nfe::findOrFail($request->id);
        $empresa = Empresa::findOrFail($nfe->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $nfe->local_id);

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

                $garantia = Garantia::where('empresa_id', $nfe->empresa_id)
                ->where('nfe_id', $nfe->id)->first();

                if($garantia){
                    $garantia->delete();
                }

                ContaPagar::where('nfe_id', $nfe->id)->delete();
                ContaReceber::where('nfe_id', $nfe->id)->delete();
                ProdutoUnico::where('nfe_id', $nfe->id)->delete();
                $item->delete();
                $nfe->fatura()->delete();
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

    public function corrigir(Request $request)
    {
        $nfe = Nfe::findOrFail($request->id);
        $empresa = Empresa::findOrFail($nfe->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $nfe->local_id);

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
            $doc = $nfe_service->correcao($nfe, $request->motivo);

            if (!isset($doc['erro'])) {

                // return response()->json($doc, 200);
                $motivo = $doc['retEvento']['infEvento']['xMotivo'];
                $cStat = $doc['retEvento']['infEvento']['cStat'];
                if($cStat == 135){

                    $descricaoLog = "CORRIGIDA $nfe->chave";
                    __createLog($nfe->empresa_id, 'NFe', 'corrigir', $descricaoLog);

                    try{
                        $fileDir = public_path('xml_nfe_correcao/').$nfe->chave.'.xml';
                        $this->siegUtil->enviarXml($nfe->empresa_id, $fileDir);
                    }catch(\Exception $e){
                    }

                    return response()->json("[$cStat] $motivo", 200);
                }else{
                    $descricaoLog = "ERRO CORRIGIR: $nfe->chave";
                    __createLog($nfe->empresa_id, 'NFe', 'erro', $descricaoLog);
                    return response()->json("[$cStat] $motivo", 401);
                }
            } else {
                $descricaoLog = "ERRO CORRIGIR: $nfe->chave";
                __createLog($nfe->empresa_id, 'NFe', 'erro', $descricaoLog);
                return response()->json($nfe['data'], $nfe['status']);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function consultar(Request $request)
    {
        $nfe = Nfe::findOrFail($request->id);
        $empresa = Empresa::findOrFail($nfe->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $nfe->local_id);

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
            $consulta = $nfe_service->consultar($nfe);

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

    public function inutilizar(Request $request)
    {
        $item = Inutilizacao::findOrFail($request->id);
        $empresa = Empresa::findOrFail($item->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $item->local_id);
        
        if ($item != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
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
            $consulta = $nfe_service->inutilizar($item);
            if (!isset($consulta['erro'])) {
                $cStat = $consulta['infInut']['cStat'];
                if($cStat == 102){
                    $item->estado = 'aprovado';
                    $item->save();
                    return response()->json($consulta['infInut']['xMotivo'], 200);

                }else{
                    $item->estado = 'rejeitado';
                    $item->save();
                    return response()->json($consulta['infInut']['xMotivo'], 403);
                }

            }else{
                $item->estado = 'rejeitado';
                $item->save();
                return response()->json($consulta['data'], 403);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function consultaStatusSefaz(Request $request){
        $caixa = Caixa::where('usuario_id', $request->usuario_id)->where('status', 1)->first();
        $empresa = Empresa::findOrFail($request->empresa_id);
        $empresa = __objetoParaEmissao($empresa, $caixa ? $caixa->local_id : null);
        
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
        $consulta = $nfe_service->consultaStatus((int)$empresa->ambiente, $empresa->cidade->uf);
        return response()->json($consulta, 200);
    }

    public function find(Request $request){
        $item = Nfe::with('cliente')->findOrFail($request->id);
        return response()->json($item, 200);
    }

    public function sendMail(Request $request){
        $email = $request->email;
        $xml = $request->xml;
        $danfe = $request->danfe;
        $id = $request->id;

        $nfe = Nfe::findOrFail($id);
        if(!$email){
            return response()->json('Informe o email!', 404);
        }

        $docs = [];
        if($xml){
            $docs[] = public_path('xml_nfe/').$nfe->chave.'.xml';
        }
        if($danfe){
            $this->gerarDanfeTemporaria($nfe);
            $docs[] = public_path('danfe_temp/').$nfe->chave.'.pdf';
        }

        $emailConfig = EmailConfig::where('empresa_id', $nfe->empresa_id)
        ->where('status', 1)
        ->first();
        try{
            if($emailConfig != null){

                $body = view('mail.nfe', compact('nfe'));
                $result = $this->emailUtil->enviaEmailPHPMailer($email, 'Envio de documento', $body, $emailConfig, $docs);
            }else{
                Mail::send('mail.nfe', ['nfe' => $nfe], function($m) use ($email, $docs){
                    $nomeEmail = env('MAIL_FROM_NAME');
                    $m->from(env('MAIL_USERNAME'), $nomeEmail);
                    $m->subject('Envio de documento');
                    foreach($docs as $f){
                        $m->attach($f); 
                    }
                    $m->to($email);
                });
            }
            //limpa diretorio danfe_temp
            $this->unlinkr(public_path('danfe_temp'));
            return response()->json("Email enviado!", 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 404);
        }

    }

    private function gerarDanfeTemporaria($nfe){
        if (!is_dir(public_path('danfe_temp'))) {
            mkdir(public_path('danfe_temp'), 0777, true);
        }
        $xml = file_get_contents(public_path('xml_nfe/').$nfe->chave.'.xml');
        $danfe = new Danfe($xml);
        $pdf = $danfe->render();
        file_put_contents(public_path('danfe_temp/') . $nfe->chave . '.pdf', $pdf);

    }

    private function unlinkr($dir){ 
        $files = array_diff(scandir($dir), array('.', '..')); 

        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? $this->unlinkr("$dir/$file") : unlink("$dir/$file"); 
        }

        return rmdir($dir); 
    } 

}
