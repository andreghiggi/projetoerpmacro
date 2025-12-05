<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empresa;
use App\Models\ConfiguracaoSuper;
use App\Models\FinanceiroBoleto;
use App\Models\LogBoleto;

class BoletoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:boleto-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gerar boletos de planos com asaas';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $config = ConfiguracaoSuper::first();
        $empresas = Empresa::orderBy('nome')
        ->where('receber_com_boleto', 1)
        ->where('status', 1)
        ->where('dia_vencimento_boleto', '!=', null)
        ->get();

        foreach($empresas as $empresa){
            if($empresa->plano){
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
                $valor = $empresa->plano->valor;
                $vencimento = date('Y-m') . "-" . $empresa->dia_vencimento_boleto;

                $financeiroBoleto = FinanceiroBoleto::where('empresa_id', $empresa->id)
                ->whereMonth('vencimento', date('m'))->first();
                if($financeiroBoleto == null){
                    $dataBoleto = [
                        'customer' => $empresa->asaas_id,
                        'billingType' => 'BOLETO',
                        'value' => $valor,
                        'dueDate' => $vencimento
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
                            'valor' => $valor,
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
                            'descricao' => 'Cron mensal'
                        ]);
                    }
                }

            }
        }
    }
}
