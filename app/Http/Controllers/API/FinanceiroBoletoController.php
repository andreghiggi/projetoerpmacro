<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\ConfiguracaoSuper;
use App\Models\FinanceiroBoleto;
use App\Models\LogBoleto;

class FinanceiroBoletoController extends Controller
{
    public function store(Request $request){
        $empresa = Empresa::findOrFail($request->empresa_id);
        $valor = __convert_value_bd($request->valor);
        $vencimento = $request->vencimento;

        $config = ConfiguracaoSuper::first();
        try{
            if($empresa->asaas_id == null){
                $dataCliente = [
                    'name' => $empresa->nome,
                    'cpfCnpj' => $empresa->cpf_cnpj,
                    'email' => $empresa->email,
                    'mobilePhone' => preg_replace('/[^0-9]/', '', $empresa->telefone),
                    'address' => $empresa->rua,
                    'addressNumber' => $empresa->numero,
                    'province' => $empresa->bairro,
                    'postalCode' => preg_replace('/[^0-9]/', '', $empresa->cep),
                ];

                $endPoint = 'https://api-sandbox.asaas.com/v3/customers';
                if($config->sandbox_boleto == 0){
                    $endPoint = 'https://api.asaas.com/v3/customers';
                }
                $client = new \GuzzleHttp\Client();

                $response = $client->request('POST', $endPoint, [
                    'body' => json_encode($dataCliente),
                    'headers' => [
                        'accept' => 'application/json',
                        'access_token' => $config->asaas_token_boleto,
                    ],
                ]);

                $data = json_decode($response->getBody(),true);

                $empresa->asaas_id = $data['id'];
                $empresa->save();
            }

            $dataBoleto = [
                'customer' => $empresa->asaas_id,
                'billingType' => 'BOLETO',
                'value' => __convert_value_bd($request->valor),
                'dueDate' => $request->vencimento
            ];

            if($config->percentual_juros_padrao_boleto){
                $dataBoleto['interest'] = [
                    'value' => $config->percentual_juros_padrao_boleto
                ];
            }

            if($config->percentual_multa_padrao_boleto){
                $dataBoleto['fine'] = [
                    'value' => $config->percentual_multa_padrao_boleto
                ];
            }

            $endPoint = 'https://api-sandbox.asaas.com/v3/payments';
            if($config->sandbox_boleto == 0){
                $endPoint = 'https://api.asaas.com/v3/payments';
            }
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $endPoint, [
                'body' => json_encode($dataBoleto),
                'headers' => [
                    'accept' => 'application/json',
                    'access_token' => $config->asaas_token_boleto,
                ],
            ]);

            $data = json_decode($response->getBody(),true);

            if($data){
                $boleto = [
                    'empresa_id' => $empresa->id,
                    'valor' => __convert_value_bd($request->valor),
                    'vencimento' => $vencimento,
                    'pdf_boleto' => $data['bankSlipUrl'],
                    'valor_recebido' => 0,
                    'juros' => $config->percentual_juros_padrao_boleto,
                    'multa' => $config->percentual_multa_padrao_boleto,
                    'status' => 0,
                    'plano_id' => $empresa->plano->plano_id,
                    '_id' => $data['id']
                ];
                FinanceiroBoleto::create($boleto);

                LogBoleto::create([
                    'tipo' => 'geracao',
                    'empresa_id' => $empresa->id,
                    'status' => 1,
                    'descricao' => 'Gerador'
                ]);
            }

            return response()->json($dataBoleto, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 404);
        }

    }

    public function confirmacao(Request $request){
        try{

            $id = $request->payment['id'];

            $financeiro = FinanceiroBoleto::where('_id', $id)->first();
            if($financeiro){
                $financeiro->status = 1;
                $financeiro->data_recebimento = date('Y-m-d');
                $financeiro->valor_recebido = $financeiro->valor;
                $financeiro->save();

                LogBoleto::create([
                    'tipo' => 'confirmacao',
                    'empresa_id' => $financeiro->empresa_id,
                    'status' => 1,
                    'descricao' => 'Confirmação webhook'
                ]);
            }else{
                LogBoleto::create([
                    'tipo' => 'confirmacao',
                    'empresa_id' => null,
                    'status' => 0,
                    'descricao' => 'ID não encontrado'
                ]);
            }
        }catch(\Exception $e){
            LogBoleto::create([
                'tipo' => 'confirmacao',
                'empresa_id' => null,
                'status' => 0,
                'descricao' => $e->getMessage()
            ]);
        }
    }

    public function modal(Request $request){

        // $item = FinanceiroBoleto::where('empresa_id', $request->empresa_id)
        // ->whereMonth('created_at', date('m'))
        // ->where('status', 0)
        // ->first();

        $item = $this->validaFaturasAnteriores($request->empresa_id);
        return view('financeiro_boletos.partials.modal', compact('item'));
    }

    private function validaFaturasAnteriores($empresa_id){
        $mes = 1;
        $fim = (int)date('m');
        for($i=1; $i<=$fim; $i++){
            $fatura = FinanceiroBoleto::where('empresa_id', $empresa_id)
            ->whereMonth('vencimento', $mes)
            ->where('status', 0)
            ->first();

            if($fatura != null) return $fatura;
            $mes++;
        }
    }
}
