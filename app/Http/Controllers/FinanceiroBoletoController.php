<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceiroBoleto;
use App\Models\Empresa;
use App\Models\ConfiguracaoSuper;
use App\Models\LogBoleto;

class FinanceiroBoletoController extends Controller
{
    public function index(Request $request){
        $empresa = $request->empresa;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status_pagamento;

        $data = FinanceiroBoleto::orderBy('id', 'desc')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('vencimento', '<=', $end_date);
        })
        ->when(!empty($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when(!empty($empresa), function ($query) use ($empresa) {
            return $query->where('empresa_id', $empresa);
        })
        ->paginate(__itensPagina());

        if($empresa){
            $empresa = Empresa::findOrFail($empresa);
        }
        return view('financeiro_boletos.index', compact('data', 'empresa'));
    }

    public function create(){
        $config = ConfiguracaoSuper::first();
        if($config == null){
            session()->flash("flash_warning", "Primeiramente configure!");
            return redirect()->route('configuracao-super.index');
        }
        return view('financeiro_boletos.create', compact('config'));
    }

    public function edit($id){
        $item = FinanceiroBoleto::findOrFail($id);
        $config = ConfiguracaoSuper::first();

        return view('financeiro_boletos.edit', compact('config', 'item'));
    }

    public function store(Request $request){
        $client = new \GuzzleHttp\Client();
        $config = ConfiguracaoSuper::first();
        $empresa = Empresa::findOrFail($request->empresa_boleto);

        try{
            if($empresa->asaas_id == null){

                $dataCliente = [
                    'name' => $request->razao_social,
                    'cpfCnpj' => $request->cpf_cnpj,
                    'email' => $request->email,
                    'mobilePhone' => preg_replace('/[^0-9]/', '', $request->telefone),
                    'address' => $request->rua,
                    'addressNumber' => $request->numero,
                    'province' => $request->bairro,
                    'postalCode' => preg_replace('/[^0-9]/', '', $request->cep),
                ];
                $endPoint = 'https://api-sandbox.asaas.com/v3/customers';
                if($config->sandbox_boleto == 0){
                    $endPoint = 'https://api.asaas.com/v3/customers';
                }
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

            if($request->juros){
                $dataBoleto['interest'] = [
                    'value' => $request->juros
                ];
            }

            if($request->multa){
                $dataBoleto['fine'] = [
                    'value' => $request->multa
                ];
            }
            // dd($dataBoleto);

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
                    'vencimento' => $request->vencimento,
                    'pdf_boleto' => $data['bankSlipUrl'],
                    'valor_recebido' => 0,
                    'juros' => $request->juros,
                    'multa' => $request->multa,
                    'status' => 0,
                    'plano_id' => $request->plano_id,
                    '_id' => $data['id']
                ];
                FinanceiroBoleto::create($boleto);
                LogBoleto::create([
                    'tipo' => 'geracao',
                    'empresa_id' => $empresa->id,
                    'status' => 1,
                    'descricao' => 'Cadastro manual'
                ]);

                if($request->qtd_boletos > 1){
                    $this->gerarQtdBoletos($config, $empresa, $endPoint, $request);
                    session()->flash("flash_success", "Boletos gerados!");
                }else{
                    session()->flash("flash_success", "Boleto gerado!");
                }
                return redirect()->route('financeiro-boleto.index');
            }
        }catch(\GuzzleHttp\Exception\ClientException $e){
            $data = json_decode($e->getResponse()->getBody()->getContents());
            dd($data);
            session()->flash("flash_error", "Algo deu errado: " . $data->errors[0]->description);
            return redirect()->route('financeiro-boleto.create');
        }

        // dd($data);
    }

    private function gerarQtdBoletos($config, $empresa, $endPoint, $request){
        $vencimento = $request->vencimento;
        $dia = (int)\Carbon\Carbon::parse($vencimento)->format('d');
        $mes = (int)\Carbon\Carbon::parse($vencimento)->format('m');
        $ano = \Carbon\Carbon::parse($vencimento)->format('Y');

        for($i=0; $i<$request->qtd_boletos-1; $i++){
            sleep(1);
            $mes++;
            $dia = (int)$dia;
            $mes = (int)$mes;
            if($mes > 12){
                $mes = 1;
                $ano+=1;
            }
            if($mes == 2 && $dia > 28){
                $dia = 28;
            }
            $mes = $mes < 10 ? "0$mes" : $mes;
            $dia = $dia < 10 ? "0$dia" : $dia;
            $vencimento = "$ano-$mes-$dia";

            $dataBoleto = [
                'customer' => $empresa->asaas_id,
                'billingType' => 'BOLETO',
                'value' => __convert_value_bd($request->valor),
                'dueDate' => $vencimento
            ];

            if($request->juros){
                $dataBoleto['interest'] = [
                    'value' => $request->juros
                ];
            }

            if($request->multa){
                $dataBoleto['fine'] = [
                    'value' => $request->multa
                ];
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
                    'juros' => $request->juros,
                    'multa' => $request->multa,
                    'status' => 0,
                    'plano_id' => $request->plano_id,
                    '_id' => $data['id']
                ];
                FinanceiroBoleto::create($boleto);
                LogBoleto::create([
                    'tipo' => 'geracao',
                    'empresa_id' => $empresa->id,
                    'status' => 1,
                    'descricao' => 'Cadastro manual'
                ]);
            }
        }

    }

    public function imprimir($id)
    {
        $item = FinanceiroBoleto::findOrFail($id);

    }

    public function destroy($id)
    {
        $item = FinanceiroBoleto::findOrFail($id);
        $config = ConfiguracaoSuper::first();

        try {

            $endPoint = 'https://api-sandbox.asaas.com/v3/payments/'.$item->_id;
            if($config->sandbox_boleto == 0){
                $endPoint = 'https://api.asaas.com/v3/payments/'.$item->_id;
            }
            $client = new \GuzzleHttp\Client();
            $response = $client->request('DELETE', $endPoint, [
                'headers' => [
                    'accept' => 'application/json',
                    'access_token' => $config->asaas_token_boleto,
                ],
            ]);
            $data = json_decode($response->getBody(),true);
            if($data['deleted']){
                $item->delete();

                session()->flash("flash_success", "Boleto removida com sucesso!");
            }else{
                session()->flash("flash_error", 'Algo deu errado');
            }
        } catch (\Exception $e) {

            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function gerar(Request $request){
        $data = Empresa::orderBy('nome')
        ->where('receber_com_boleto', 1)
        ->where('status', 1)
        ->where('dia_vencimento_boleto', '!=', null)
        ->get();

        foreach($data as $item){
            if($item->plano == null){
                session()->flash("flash_warning", 'Empresa ' . $item->info . ' sem plano atribuído!');
                return redirect()->back();
            }
        }
        return view('financeiro_boletos.gerar', compact('data'));
    }

    public function update(Request $request, $id){
        $item = FinanceiroBoleto::findOrFail($id);
        try{
            $item->valor_recebido = __convert_value_bd($request->valor_recebido);
            $item->status = $request->status;
            $item->data_recebimento = $request->data_recebimento;

            $item->save();
            session()->flash("flash_success", 'Boleto alterado!');
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('financeiro-boleto.index');
    }

    public function logs(Request $request){

        $empresa = $request->empresa;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;

        $data = LogBoleto::orderBy('id', 'desc')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($status), function ($query) use ($status) {
            return $query->where('status', $status == -1 ? 0 : 1);
        })
        ->when(!empty($empresa), function ($query) use ($empresa) {
            return $query->where('empresa_id', $empresa);
        })
        ->paginate(__itensPagina());

        if($empresa){
            $empresa = Empresa::findOrFail($empresa);
        }
        return view('financeiro_boletos.logs', compact('data', 'empresa'));

    }

}
