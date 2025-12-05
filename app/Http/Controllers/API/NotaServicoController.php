<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudDfe\SdkPHP\Nfse;
use App\Models\Empresa;
use App\Models\NotaServico;
use App\Models\ConfigGeral;

class NotaServicoController extends Controller
{
    public function transmitir(Request $request){
        $item = NotaServico::findOrFail($request->id);
        $empresa = $item->empresa;

        $params = [
            'token' => $empresa->token_nfse,
            'ambiente' => Nfse::AMBIENTE_PRODUCAO,
            'debug' => false,
            'timeout' => 60,
            'port' => 443,
            'http_version' => CURL_HTTP_VERSION_NONE,
            'options' => [
                'debug' => false,
                'timeout' => 60,
                'port' => 443,
                'http_version' => CURL_HTTP_VERSION_NONE
            ]
        ];

        $nfse = new Nfse($params);
        $servico = $item->servico;

        try {

            $doc = preg_replace('/[^0-9]/', '', $item->documento);
            $im = preg_replace('/[^0-9]/', '', $item->im);
            $ie = preg_replace('/[^0-9]/', '', $item->ie);
            $config = ConfigGeral::where('empresa_id', $item->empresa_id)
            ->first();

            $regimeTributacao = null;
            if($config != null){
                $regimeTributacao = $config->regime_nfse;
            }

            $empresa->numero_ultima_nfse = $item->numero_nfse;
            $empresa->save();

            $valorLiquido = $servico->valor_servico - $servico->desconto_incondicional - $servico->desconto_condicional;

            $payload = [
                "numero" => $item->numero_nfse,
                "serie" => $empresa->numero_serie_nfse,
                "tipo" => "1",
                "status" => "1",
                "data_emissao" => date("Y-m-d\TH:i:sP"),
                "data_competencia" => date("Y-m-d\TH:i:sP"),
                "regime_tributacao" => $regimeTributacao,
                "tomador" => [
                    "cnpj" => strlen($doc) == 14 ? $doc : null,
                    "cpf" => strlen($doc) == 11 ? $doc : null,
                    "im" => $im ? $im : null,
                    "ie" => $ie ? $ie : null,
                    "razao_social" => $item->razao_social,
                    "endereco" => [
                        "logradouro" => $this->retiraAcentos($item->rua),
                        "numero" => $this->retiraAcentos($item->numero),
                        "complemento" => $this->retiraAcentos($item->complemento),
                        "bairro" => $this->retiraAcentos($item->bairro),
                        "codigo_municipio" => $item->cidade->codigo,
                        "uf" => $item->cidade->uf,
                        "cep" => preg_replace('/[^0-9]/', '', $item->cep)
                    ]
                ],
                "servico" => [
                    "codigo" => $servico->codigo_servico,
                    "codigo_tributacao_municipio" => $servico->codigo_tributacao_municipio,
                    "discriminacao" => $this->retiraAcentos($servico->discriminacao),
                    "codigo_municipio" => $empresa->cidade->codigo,
                    "valor_servicos" => $servico->valor_servico,
                    "valor_pis" => $servico->aliquota_pis,
                    "valor_aliquota" => $servico->aliquota_iss,
                    "valor_iss" => $servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : null,
                    "valor_iss_retido" => $servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : null,
                    "valor_liquido" => $valorLiquido - ($servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : 0),
                    "codigo_cnae" => $servico->codigo_cnae,
                    "itens" => [
                        [
                            "codigo" => $servico->codigo_servico,
                            "codigo_tributacao_municipio" => $servico->codigo_tributacao_municipio,
                            "discriminacao" => $this->retiraAcentos($servico->discriminacao),
                            "codigo_municipio" => $empresa->cidade->codigo,
                            "valor_servicos" => $servico->valor_servico,
                            "valor_pis" => $servico->aliquota_pis,
                            "valor_aliquota" => $servico->aliquota_iss,
                            "valor_liquido" => $valorLiquido - ($servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : 0),
                            "codigo_cnae" => $servico->codigo_cnae,
                            "valor_iss" => $servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : null,
                            "valor_iss_retido" => $servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : null,
                        ]
                    ]
                ]
            ];

            if($servico->desconto_incondicional > 0){
                $payload['servico']['valor_desconto_incondicionado'] = $servico->desconto_incondicional;
                $payload['servico']['itens'][0]['valor_desconto_incondicionado'] = $servico->desconto_incondicional;
            }

            if($servico->desconto_condicional > 0){
                $payload['servico']['valor_desconto_condicionado'] = $servico->desconto_condicional;
                $payload['servico']['itens'][0]['valor_desconto_condicionado'] = $servico->desconto_condicional;
            }

            if($servico->iss_retido == 1){
                $payload['servico']['iss_retido'] = true;
                $payload['servico']['responsavel_retencao'] = $servico->responsavel_retencao_iss;
            }

            $resp = $nfse->cria($payload);
            $resp = json_decode($resp);
            if(isset($resp->sucesso) && $resp->sucesso == true){
                if(isset($resp->chave)){
                    $item->chave = $resp->chave;
                    $item->save();
                }
                // return response()->json($resp, 200);

                $chave = $resp->chave;
                sleep(15);
                $tentativa = 1;
                while ($tentativa <= 2) {
                    $payload = [
                        'chave' => $item->chave
                    ];
                    $resp = $nfse->consulta($payload);
                    $resp = json_decode($resp);
                    if($resp->codigo != 5023) {
                        if ($resp->sucesso) {
                    // autorizado

                            $item->estado = 'aprovado';
                            $item->url_pdf_nfse = $resp->link_pdf;
                            // $item->numero_nfse = $resp->numero;
                            $item->codigo_verificacao = $resp->codigo_verificacao;

                            $item->save();

                            // $empresa->numero_ultima_nfse = (int)$resp->numero;
                            // $empresa->save();
                            $xml = $resp->xml;
                            file_put_contents(public_path('xml_nota_servico/')."$item->chave.xml", $xml);
                            return response()->json($resp, 200);
                        } else {
                            return response()->json($resp, 200);
                        }
                    }
                    sleep(3);
                    $tentativa++;
                }

            }else{
                if($resp->codigo == 5008){
                    $item->chave = $resp->chave;
                    $item->save();
                }
                return response()->json($resp, 404);
            }

        }catch (\Exception $e) {
            return response()->json($e->getMessage(), 403);
        }
    }

    public function consultar(Request $request){
        $item = NotaServico::findOrFail($request->id);
        $empresa = $item->empresa;
        // $params = [
        //     'token' => $empresa->token_nfse,
        //     'ambiente' => Nfse::AMBIENTE_PRODUCAO,
        //     // 'ambiente' => $empresa->ambiente == 2 ? Nfse::AMBIENTE_HOMOLOGACAO : Nfse::AMBIENTE_PRODUCAO,
        //     'options' => [
        //         'debug' => false,
        //         'timeout' => 60,
        //         'port' => 443,
        //         'http_version' => CURL_HTTP_VERSION_NONE
        //     ]
        // ];
        $params = [
            'token' => $empresa->token_nfse,
            'ambiente' => Nfse::AMBIENTE_PRODUCAO,
            'debug' => false,
            'timeout' => 60,
            'port' => 443,
            'http_version' => CURL_HTTP_VERSION_NONE,
            'options' => [
                'debug' => false,
                'timeout' => 60,
                'port' => 443,
                'http_version' => CURL_HTTP_VERSION_NONE
            ]
        ];
        try{

            $nfse = new Nfse($params);
            $payload = [
                'chave' => $item->chave
            ];
            $resp = $nfse->consulta($payload);
            $resp = json_decode($resp);
            if(isset($resp->sucesso) && $resp->sucesso == true){
                if($resp->codigo == 100){
                    $item->estado = 'aprovado';
                    if($resp->status == 'Cancelado'){
                        $item->estado = 'cancelado';
                    }

                    $item->url_pdf_nfse = $resp->link_pdf;
                    // $item->numero_nfse = $resp->numero;
                    $item->codigo_verificacao = $resp->codigo_verificacao;

                    $item->save();

                    // $empresa->numero_ultima_nfse = (int)$resp->numero;
                    // $empresa->save();
                    $xml = $resp->xml;
                    file_put_contents(public_path('xml_nota_servico/')."$item->chave.xml", $xml);
                }else if($resp->codigo == 101){
                    if($resp->status == 'Cancelado'){
                        $item->estado = 'cancelado';
                    }

                    $item->save();
                }
                return response()->json($resp, 200);
            }
            return response()->json($resp, 404);
        }catch (\Exception $e) {
            return response()->json($e->getMessage(), 403);
        }
    }

    public function cancelar(Request $request){
        $item = NotaServico::findOrFail($request->id);
        $empresa = $item->empresa;
        $params = [
            'token' => $empresa->token_nfse,
            'ambiente' => Nfse::AMBIENTE_PRODUCAO,
            // 'ambiente' => $empresa->ambiente == 2 ? Nfse::AMBIENTE_HOMOLOGACAO : Nfse::AMBIENTE_PRODUCAO,
            'debug' => false,
            'timeout' => 60,
            'port' => 443,
            'http_version' => CURL_HTTP_VERSION_NONE,
            'options' => [
                'debug' => false,
                'timeout' => 60,
                'port' => 443,
                'http_version' => CURL_HTTP_VERSION_NONE
            ]
        ];
        try{

            $nfse = new Nfse($params);
            $payload = [
                'chave' => $item->chave,
                'justificativa' => $request->motivo
            ];
            $resp = $nfse->cancela($payload);
            if(isset($resp->sucesso) && $resp->sucesso == true){
                return response()->json($resp, 200);
            }

            return response()->json($resp, 404);
        }catch (\Exception $e) {
            return response()->json($e->getMessage(), 403);
        }
    }

    private function retiraAcentos($texto){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/", "/(ç)/", "/(&)/"),explode(" ","a A e E i I o O u U n N c e"),$texto);
    }
}
