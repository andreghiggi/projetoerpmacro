<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cte;
use App\Models\Cliente;
use App\Models\Cidade;
use App\Models\ComponenteCte;
use App\Models\ChaveNfeCte;
use App\Models\Veiculo;
use App\Models\Empresa;
use App\Models\MedidaCte;
use App\Models\NaturezaOperacao;
use App\Services\CTeService;
use Illuminate\Support\Facades\DB;
use NFePHP\DA\CTe\Dacte;
use NFePHP\DA\CTe\Daevento;
class CTeController extends Controller
{
    public function __construct(){
        if (!is_dir(public_path('xml_cte'))) {
            mkdir(public_path('xml_cte'), 0777, true);
        }
        if (!is_dir(public_path('xml_cte_cancelada'))) {
            mkdir(public_path('xml_cte_cancelada'), 0777, true);
        }
        if (!is_dir(public_path('xml_cte_correcao'))) {
            mkdir(public_path('xml_cte_correcao'), 0777, true);
        }
        if (!is_dir(public_path('dacte_temp'))) {
            mkdir(public_path('dacte_temp'), 0777, true);
        }

        if (!is_dir(public_path('dacte'))) {
            mkdir(public_path('dacte'), 0777, true);
        }
        if (!is_dir(public_path('dacte_cancelamento'))) {
            mkdir(public_path('dacte_cancelamento'), 0777, true);
        }
    }

    public function emitir(Request $request){
        $documento = $request->documento;
        $remetente = $request->remetente;
        $destinatario = $request->destinatario;
        $recebedor = $request->recebedor;
        $expedidor = $request->expedidor;
        $tomador = $request->tomador;
        $referencia = $request->referencia;
        $documentoAuxiliar = $request->documento_auxiliar;
        $componentes = $request->componentes;
        $medidas = $request->medidas;
        $veiculo = $request->veiculo;

        $empresa = Empresa::findOrFail($request->empresa_id);

        // return response()->json($remetente, 200);

        if($empresa->arquivo == null){
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $cte_service = new CTeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => $empresa->cpf_cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
        ], $empresa);

        $cte = DB::transaction(function () use ($empresa, $documento, $remetente, $destinatario, $recebedor, $expedidor, $tomador, $referencia, $documentoAuxiliar, $componentes, $medidas, $veiculo) {
            $cte = $this->criaCte($empresa, $documento, $remetente, $destinatario, $recebedor, $expedidor, $tomador, 
                $referencia, $documentoAuxiliar, $componentes, $medidas, $veiculo);
            return $cte;
        });

        $doc = $cte_service->gerarCTe($cte);

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];
            $chave = $doc['chave'];

            try{
                $signed = $cte_service->sign($xml);
                $resultado = $cte_service->transmitir($signed, $doc['chave']);

                if ($resultado['erro'] == 0) {
                    $cte->chave = $doc['chave'];
                    $cte->estado = 'aprovado';
                    $cte->numero = $doc['nCte'];
                    $cte->recibo = $resultado['success'];
                    if($empresa->ambiente == 2){
                        $empresa->numero_ultima_cte_homologacao = $doc['nCte'];
                    }else{
                        $empresa->numero_ultima_cte_producao = $doc['nCte'];
                    }

                    $empresa->save();

                    $cte->save();

                    $xml = file_get_contents(public_path('xml_cte/') . $cte->chave . '.xml');
                    $danfe = new Dacte($xml);
                    $pdf = $danfe->render();
                    file_put_contents(public_path('dacte/') . $cte->chave . '.pdf', $pdf);
                    $pathPrint = env("APP_URL") . "/dacte/$cte->chave.pdf";
                    $data = [
                        'recibo' => $resultado['success'],
                        'url_print' => $pathPrint,
                        'chave' => $cte->chave
                    ];
                    return response()->json($data, 200);
                }else{
                    $error = $resultado['error'];
                    // return response()->json($resultado, 401);

                    if(isset($error['protCTe'])){
                        $motivo = $error['protCTe']['infProt']['xMotivo'];
                        $cStat = $error['protCTe']['infProt']['cStat'];
                        $cte->motivo_rejeicao = substr("[$cStat] $motivo", 0, 200);
                    }

                    $cte->numero = isset($documento['numero_cte']) ? $documento['numero_cte'] : 
                    Nfe::lastNumero($empresa);
                    $cte->chave = $doc['chave'];
                    $cte->estado = 'rejeitado';
                    $cte->save();
                    
                    if(isset($error['protCTe'])){
                        return response()->json("[$cStat] $motivo", 403);
                    }else{
                        return response()->json($error, 403);
                    }
                }

                return response()->json($xml, 200);
            }catch(\Exception $e){
                return response()->json(__getError($e), 404);
            }

        }else{
            return response()->json($doc['erros_xml'], 401);
        }
    }

    public function xmlTemporario(Request $request){

        $documento = $request->documento;
        $remetente = $request->remetente;
        $destinatario = $request->destinatario;
        $recebedor = $request->recebedor;
        $expedidor = $request->expedidor;
        $tomador = $request->tomador;
        $referencia = $request->referencia;
        $documentoAuxiliar = $request->documento_auxiliar;
        $componentes = $request->componentes;
        $medidas = $request->medidas;
        $veiculo = $request->veiculo;

        $empresa = Empresa::findOrFail($request->empresa_id);

        // return response()->json($remetente, 200);

        if($empresa->arquivo == null){
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $cte_service = new CTeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => $empresa->cpf_cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
        ], $empresa);

        $cte = DB::transaction(function () use ($empresa, $documento, $remetente, $destinatario, $recebedor, $expedidor, $tomador, $referencia, $documentoAuxiliar, $componentes, $medidas, $veiculo) {
            $cte = $this->criaCte($empresa, $documento, $remetente, $destinatario, $recebedor, $expedidor, $tomador, 
                $referencia, $documentoAuxiliar, $componentes, $medidas, $veiculo);
            return $cte;
        });

        $doc = $cte_service->gerarCTe($cte);

        if($cte != null){
            $cte->componentes()->delete();
            $cte->medidas()->delete();
            $cte->chaves_nfe()->delete();
            $cte->delete();
        }
        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];

            return response()->json($xml, 200);
        }else{
            return response()->json($doc['erros_xml'], 401);
        }

    }

    public function dacteTemporario(Request $request){

        $documento = $request->documento;
        $remetente = $request->remetente;
        $destinatario = $request->destinatario;
        $recebedor = $request->recebedor;
        $expedidor = $request->expedidor;
        $tomador = $request->tomador;
        $referencia = $request->referencia;
        $documentoAuxiliar = $request->documento_auxiliar;
        $componentes = $request->componentes;
        $medidas = $request->medidas;
        $veiculo = $request->veiculo;

        $empresa = Empresa::findOrFail($request->empresa_id);

        // return response()->json($remetente, 200);

        if($empresa->arquivo == null){
            return response()->json("Certificado não encontrado para este emitente", 401);
        }

        $cte_service = new CTeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => $empresa->cpf_cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
        ], $empresa);

        $cte = DB::transaction(function () use ($empresa, $documento, $remetente, $destinatario, $recebedor, $expedidor, $tomador, $referencia, $documentoAuxiliar, $componentes, $medidas, $veiculo) {
            $cte = $this->criaCte($empresa, $documento, $remetente, $destinatario, $recebedor, $expedidor, $tomador, 
                $referencia, $documentoAuxiliar, $componentes, $medidas, $veiculo);
            return $cte;
        });

        $doc = $cte_service->gerarCTe($cte);

        if($cte != null){
            $cte->componentes()->delete();
            $cte->medidas()->delete();
            $cte->chaves_nfe()->delete();
            $cte->delete();
        }
        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];
            $chave = $doc['chave'];

            $danfe = new Dacte($xml);
            $pdf = $danfe->render();
            file_put_contents(public_path('dacte_temp/') . $chave . '.pdf', $pdf);
            $pathPrint = env("APP_URL") . "/dacte_temp/$chave.pdf";
            return response()->json($pathPrint, 200);

            return response()->json($xml, 200);
        }else{
            return response()->json($doc['erros_xml'], 401);
        }

    }

    private function criaCte($empresa, $documento, $remetente, $destinatario, $recebedor, $expedidor, $tomador, 
        $referencia, $documentoAuxiliar, $componentes, $medidas, $veiculo){
        $remetente_id = $this->criaCliente($remetente, $empresa->id);
        $destinatario_id = $this->criaCliente($destinatario, $empresa->id);

        $recebedor_id = null;
        $expedidor_id = null;
        if($recebedor){
            $recebedor_id = $this->criaCliente($recebedor, $empresa->id);
        }
        if($expedidor){
            $expedidor_id = $this->criaCliente($expedidor, $empresa->id);
        }

        $natureza_id = null;
        $natureza_id = $this->criaNatureza($documento['natureza_operacao'], $empresa->id);
        $veiculo_id = $this->criaVeiculo($veiculo, $empresa->id);

        $numero = isset($documento['numero_cte']) ? $documento['numero_cte'] : 0;
        if($numero == 0){
            $empresa = Empresa::findOrFail($empresa->id);
            $numero = Cte::lastNumero($empresa);
        }

        $cidadeEnvio = Cidade::where('codigo', $documento['municipio_envio_ibge'])->first();
        $cidadeInicio = Cidade::where('codigo', $documento['municipio_inicio_ibge'])->first();
        $cidadeFim = Cidade::where('codigo', $documento['municipio_fim_ibge'])->first();
        $cidadeTomador = Cidade::where('codigo', $tomador['cod_municipio_ibge'])->first();

        $data = [
            'empresa_id' => $empresa->id,
            'remetente_id' => $remetente_id,
            'destinatario_id' => $destinatario_id,
            'natureza_id' => $natureza_id,
            'veiculo_id' => $veiculo_id,

            'chave' => "",
            'numero_serie' => $documento['numero_serie'],
            'cfop' => $documento['cfop'],
            'numero' => $numero,
            'estado' => 'novo',
            'tomador' => $tomador['tipo'],
            'logradouro_tomador' => $tomador['rua'],
            'numero_tomador' => $tomador['numero'],
            'bairro_tomador' => $tomador['bairro'],
            'cep_tomador' => $tomador['cep'],
            'municipio_tomador' => $cidadeTomador->id,
            'municipio_envio' => $cidadeEnvio->id,
            'municipio_inicio' => $cidadeInicio->id,
            'municipio_fim' => $cidadeFim->id,
            'api' => 1,
            'motivo_rejeicao' => "",
            'sequencia_cce' => 0,
            'ambiente' => $empresa->ambiente,

            'valor_transporte' => $documento['valor_transporte'],
            'valor_receber' => $documento['valor_receber'],
            'valor_carga' => $documento['valor_carga'],

            'produto_predominante' => $documento['produto_predominante'],
            'data_prevista_entrega' => $documento['data_prevista_entrega'],
            'observacao' => isset($documento['observacao']) ? $documento['observacao'] : '',
            'retira' => isset($documento['retira']) ? $documento['retira'] : 0,
            'detalhes_retira' => isset($documento['detalhes_retira']) ? $documento['detalhes_retira'] : '',

            'modal' => $documento['modal'],

            'tpDoc' => $documentoAuxiliar ? $documentoAuxiliar['tipo'] : '',
            'descOutros' => $documentoAuxiliar ? $documentoAuxiliar['descricao'] : '',
            'nDoc' => $documentoAuxiliar ? $documentoAuxiliar['numero'] : 0,
            'vDocFisc' => $documentoAuxiliar ? $documentoAuxiliar['valor'] : 0,
            'globalizado' => isset($documento['globalizado']) ? $documento['globalizado'] : '',


            'cst' => isset($documento['cst']) ? $documento['cst'] : '00',
            'perc_icms' => __convert_value_bd($documento['perc_icms']),
            'perc_red_bc' => __convert_value_bd($documento['perc_red_bc']),

        ];
        $item = Cte::create($data);

        $this->criaCompenentes($componentes, $item->id);
        $this->criaMedidas($medidas, $item->id);
        if($referencia){
            $this->criaReferencia($referencia, $item->id);
        }
        return $item;

    }

    private function criaReferencia($referencia, $cte_id){
        foreach($referencia as $r){
            ChaveNfeCte::create([
                'chave' => $r['chave'],
                'cte_id' => $cte_id
            ]);
        }
    }

    private function criaCompenentes($componentes, $cte_id){
        foreach($componentes as $c){
            ComponenteCte::create([
                'nome' => $c['nome'],
                'valor' => __convert_value_bd($c['valor']),
                'cte_id' => $cte_id
            ]);
        }
    }

    private function criaMedidas($medidas, $cte_id){
        foreach($medidas as $m){
            MedidaCte::create([
                'cte_id' => $cte_id,
                'tipo_medida' => $m['tipo_medida'],
                'quantidade' => __convert_value_bd($m['quantidade']),
                'cod_unidade' => $m['cod_unidade']
            ]);
        }
    }

    private function criaNatureza($descricao, $empresa_id){
        $natureza = NaturezaOperacao::where('descricao', $descricao)->first();
        if($natureza != null){
            return $natureza->id;
        }

        $natureza = NaturezaOperacao::create([
            'empresa_id' => $empresa_id,
            'descricao' => $descricao,
        ]);
        
        return $natureza->id;
    }

    private function criaVeiculo($veiculo, $empresa_id){
        $item = Veiculo::where('placa', $veiculo)->first();
        if($item != null){
            return $natureza->id;
        }

        $item = Veiculo::create([
            'empresa_id' => $empresa_id,
            'tipo' => $veiculo['tipo'],
            'placa' => $veiculo['placa'],
            'uf' => $veiculo['uf'],
            'cor' => $veiculo['cor'],
            'marca' => $veiculo['marca'],
            'modelo' => $veiculo['modelo'],
            'rntrc' => isset($veiculo['rntrc']) ? $veiculo['rntrc'] : '',
            'tipo_carroceria' => $veiculo['tipo_carroceria'],
            'tipo_rodado' => $veiculo['tipo_rodado'],
            'tara' => __convert_value_bd($veiculo['tara']),
            'capacidade' => __convert_value_bd($veiculo['capacidade']),
            'proprietario_documento' => $veiculo['proprietario_documento'],
            'proprietario_nome' => $veiculo['proprietario_nome'],
            'proprietario_ie' => isset($veiculo['proprietario_ie']) ? $veiculo['proprietario_ie'] : '',
            'proprietario_uf' => $veiculo['proprietario_uf'],
            'proprietario_tp' => $veiculo['proprietario_tp'],
            'taf' => isset($veiculo['taf']) ? $veiculo['taf'] : '',
            'renavam' => isset($veiculo['renavam']) ? $veiculo['renavam'] : '',
            'numero_registro_estadual' => isset($veiculo['numero_registro_estadual']) ? $veiculo['numero_registro_estadual'] : ''
        ]);
        
        return $item->id;
    }

    private function criaCliente($cliente, $empresa_id){
        $cpf_cnpj = preg_replace('/[^0-9]/', '', $cliente['cpf_cnpj']);
        if(strlen($cpf_cnpj) == 11){
            $doc = $this->setMask($cliente["cpf_cnpj"], '###.###.###-##');
        }else{
            $doc = $this->setMask($cliente["cpf_cnpj"], '##.###.###/####-##');
        }

        $cli = Cliente::where('cpf_cnpj', $doc)->first();
        if($cli != null){
            return $cli->id;
        }

        $cidade = Cidade::where('codigo', $cliente['cod_municipio_ibge'])->first();

        $cli = Cliente::create([
            'empresa_id' => $empresa_id,
            'razao_social' => $cliente['nome'],
            'nome_fantasia' => $cliente['nome'],
            'cpf_cnpj' => $doc,
            'ie' => isset($cliente['ie']) ? $cliente['ie'] : '',
            'contribuinte' => $cliente['contribuinte'],
            'consumidor_final' => $cliente['consumidor_final'],
            'email' => isset($cliente['email']) ? $cliente['email'] : '',
            'telefone' => isset($cliente['telefone']) ? $cliente['telefone'] : '',
            'cidade_id' => $cidade->id,
            'rua' => $cliente['rua'],
            'cep' => $cliente['cep'],
            'numero' => $cliente['numero'],
            'bairro' => $cliente['bairro'],
            'complemento' => isset($cliente['complemento']) ? $cliente['complemento'] : '',
        ]);
        return $cli->id;

    }

    private function setMask($val, $mask) {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++) {
            if($mask[$i] == '#') {
                if(isset($val[$k])) $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i])) {
                    if($mask[$i] == $val[$k]) {
                        $k++;
                    }
                    $maskared .= $mask[$i];
                }
            }
        }
        return $maskared;
    }

    public function cancelar(Request $request)
    {
        $chave = $request->chave;
        $cte = Cte::where('chave', $chave)->first();
        if ($cte != null) {

            $empresa = $cte->empresa;

            $cte_service = new CTeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$empresa->ambiente,
                "razaosocial" => $empresa->nome,
                "siglaUF" => $empresa->cidade->uf,
                "cnpj" => $empresa->cpf_cnpj,
                "schemes" => "PL_009_V4",
                "versao" => "4.00",
            ], $empresa);
            $doc = $cte_service->cancelar($cte, $request->motivo);

            if (!isset($doc['erro'])) {
                $cte->estado = 'cancelado';
                $cte->save();
                // return response()->json($doc, 200);
                // if(!isset($doc['retEvento'])){
                //     return response()->json($doc, 401);
                // }
                $motivo = $doc['infEvento']['xMotivo'];
                $cStat = $doc['infEvento']['cStat'];

                $xml = file_get_contents(public_path('xml_cte_cancelada/') . $cte->chave . '.xml');
                $dadosEmitente = $this->getEmitente($cte->empresa);
                $daevento = new Daevento($xml, $dadosEmitente);
                $pdf = $daevento->render();

                file_put_contents(public_path('dacte_cancelamento/') . $cte->chave . '.pdf', $pdf);
                $pathPrint = env("APP_URL") . "/dacte_cancelamento/$cte->chave.pdf";

                $data = [
                    'url_print' => $pathPrint,
                    'status' => "[$cStat] $motivo"
                ];

                return response()->json($data, 200);
                
            } else {
                $arr = $doc['data'];
                if(isset($arr['infEvento'])){

                    $cStat = $arr['infEvento']['cStat'];
                    $motivo = $arr['infEvento']['xMotivo'];

                    return response()->json("[$cStat] $motivo", $doc['status']);
                }else{
                    return response()->json($arr, $doc['status']);
                }
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    private function getEmitente($empresa){

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
}
