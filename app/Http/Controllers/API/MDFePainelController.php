<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Mdfe;
use App\Services\MDFeService;
use Illuminate\Http\Request;
use App\Utils\EmailUtil;

class MDFePainelController extends Controller
{

    protected $emailUtil;
    public function __construct(EmailUtil $util){
        $this->emailUtil = $util;
    }

    public function emitir(Request $request)
    {
        $item = Mdfe::findOrFail($request->id);
        $config = Empresa::where('id', $request->empresa_id)
        ->first();

        $config = __objetoParaEmissao($config, $item->local_id);

        if ($config == null) {
            return response()->json("Configure o emitente", 401);
        }
        try {
            $cnpj = preg_replace('/[^0-9]/', '', $config->cpf_cnpj);

            $mdfe_service = new MDFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$config->ambiente,
                "razaosocial" => $config->nome,
                "siglaUF" => $config->cidade->uf,
                "cnpj" => $cnpj,
                "inscricaomunicipal" => $config->inscricao_municipal,
                "codigomunicipio" => $config->cidade->codigo,
                "schemes" => "PL_MDFe_300a",
                "versao" => '3.00'
            ], $config);
            // return response()->json($config->senha, 401);

            if ($item->estado_emissao == 'rejeitado' || $item->estado_emissao == 'novo') {
                $mdfe = $mdfe_service->gerar($item);
                if (!isset($mdfe['erros_xml'])) {
                    $signed = $mdfe_service->sign($mdfe['xml']);
                    $resultado = $mdfe_service->transmitir($signed);
                    if (!isset($resultado['erro'])) {
                        $item->chave = $resultado['chave'];
                        $item->estado_emissao = 'aprovado';
                        $item->protocolo = $resultado['protocolo'];
                        $item->mdfe_numero = $mdfe['numero'];
                        if ($config->ambiente == 2) {
                            $config->numero_ultima_mdfe_homologacao = $mdfe['numero'];
                        } else {
                            $config->numero_ultima_mdfe_producao = $mdfe['numero'];
                        }
                        // $config->ultimo_numero_mdfe = $mdfe['numero'];
                        $config->save();
                        $item->save();
                        $file = file_get_contents(public_path('xml_mdfe/') . $resultado['chave'] . '.xml');

                        $descricaoLog = "Emitida número $item->mdfe_numero - $item->chave APROVADA";
                        __createLog($item->empresa_id, 'MDFe', 'transmitir', $descricaoLog);

                        try{
                            $fileDir = public_path('xml_mdfe/').$item->chave.'.xml';
                            $this->emailUtil->enviarXmlContador($empresa->id, $fileDir, 'MDFe', $item->chave);
                        }catch(\Exception $e){

                        }
                        return response()->json("[" . $resultado['cStat'] . "] " . $resultado['chave'] . " - " . $resultado['protocolo'], 200);
                    } else {
                        $item->estado_emissao = 'rejeitado';
                        $item->save();
                        return response()->json($resultado['cStat'] . " - " . $resultado['message'], 403);
                    }
                } else {
                    return response()->json($mdfe['erros_xml'], 401);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage() . ", l: " . $e->getLine() . ", f: " . $e->getFile(), 404);
        }
    }

    public function consultar(Request $request)
    {
        $mdfe = Mdfe::findOrFail($request->id);

        if ($mdfe->estado_emissao == 'aprovado' || $mdfe->estado_emissao == 'cancelado') {
            $config = Empresa::where('id', $request->empresa_id)
            ->first();
            $cnpj = preg_replace('/[^0-9]/', '', $config->cpf_cnpj);

            $config = __objetoParaEmissao($config, $item->local_id);
            $mdfe_service = new MDFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$config->ambiente,
                "razaosocial" => $config->nome,
                "siglaUF" => $config->cidade->uf,
                "cnpj" => $cnpj,
                "inscricaomunicipal" => $config->inscricao_municipal,
                "codigomunicipio" => $config->cidade->codigo,
                "schemes" => "PL_MDFe_300a",
                "versao" => '3.00'
            ], $config);

            $mdfe = Mdfe::find($request->id);
            $result = $mdfe_service->consultar($mdfe->chave);

            return response()->json($result, 200);
        } else {
            return response()->json("Erro ao consultar", 404);
        }
    }
}
