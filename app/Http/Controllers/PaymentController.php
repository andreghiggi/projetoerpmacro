<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\Empresa;
use App\Models\Pagamento;
use App\Models\FinanceiroPlano;
use App\Models\ConfiguracaoSuper;

class PaymentController extends Controller
{
    public function index(Request $request){
        $planos = Plano::where('status', 1)
        ->where('visivel_clientes', 1)
        ->where('valor', '>', 0)
        ->get();

        $config = ConfiguracaoSuper::first();

        $empresa = Empresa::findOrFail($request->empresa_id);
        $financeiroPlano = $empresa->financeiroPlano;
        
        foreach($planos as $p){
            if(sizeof($financeiroPlano) == 0 && $p->valor_implantacao > 0){
                $p->valor += $p->valor_implantacao;
            }else{
                $p->valor_implantacao = 0;
            }
        }

        if($config == null){
            session()->flash("flash_error", "Opção de pagamento não configurada!");
            return redirect()->back();
        }

        if((!$config->mercadopago_public_key && !$config->mercadopago_access_token) && !$config->asaas_token){
            session()->flash("flash_error", "Opção de pagamento não configurada!");
            return redirect()->back();
        }

        return view('payment.index', compact('planos', 'config'));
    }

    public function store(Request $request){
        $config = ConfiguracaoSuper::first();
        try{
            \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

            $payment = new \MercadoPago\Payment();

            $plano = Plano::findOrfail($request->plano_id);
            $plano->valor = $request->plano_valor;

            $payment->transaction_amount = (float)$plano->valor;
            $payment->description = "Pagamento do plano " . $plano->nome;
            $payment->payment_method_id = "pix";

            $empresa = Empresa::findOrFail(request()->empresa_id);
            $payment->payer = array(
                "email" => $request->email,
                "first_name" => $request->nome,
                "last_name" => $request->sobre_nome,
                "identification" => array(
                    "type" => $request->docType,
                    "number" => preg_replace('/[^0-9]/', '', $request->docNumber)
                ),
                "address"=>  array(
                    "zip_code" => preg_replace('/[^0-9]/', '', $empresa->cep),
                    "street_name" => $empresa->rua,
                    "street_number" => $empresa->numero,
                    "neighborhood" => $empresa->bairro,
                    "city" => $empresa->cidade->nome,
                    "federal_unit" => $empresa->cidade->uf
                )
            );
            $payment->save();

            if($payment->transaction_details){

                $planoEmpresa = Pagamento::create([
                    'empresa_id' => $request->empresa_id,
                    'plano_id' => $plano->id,
                    'valor' => $plano->valor,
                    'transacao_id' => (string)$payment->id,
                    'status' => $payment->status,
                    'forma_pagamento' => 'pix',
                    'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                    'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
                ]);

                // FinanceiroPlano::create([
                //     'empresa_id' => $request->empresa_id,
                //     'plano_id' => $plano->id,
                //     'valor' => $plano->valor,
                //     'tipo_pagamento' => 'PIX',
                //     'status_pagamento' => 'pendente',
                //     'plano_empresa_id' => $planoEmpresa->id
                // ]);
                
                session()->flash("flash_success", "QrCode gerado!");
                return redirect()->route('payment.pix', [(string)$payment->id]);
            }else{

                $err = $this->trataErros($payment->error);
                session()->flash("flash_error", $err);
                return redirect()->back();
            }
        }catch(\Exception $e){
            session()->flash("flash_error", $e->getMessage());
            return redirect()->back();
        }

    }

    private function trataErros($arr){
        return $arr->message;
        // $cause = $arr->causes[0];
        // $errorCode = $cause->code;
        // $arrCode = $this->arrayErros($arr);
        // return $arrCode[$errorCode];
    }

    public function pix($id){
        $item = Pagamento::where('transacao_id', $id)->first();
        return view('payment.pix', compact('item'));

    }

    public function asaas($id){
        $item = Plano::findOrFail($id);
        $config = ConfiguracaoSuper::first();

        $client = new \GuzzleHttp\Client();
        $endPoint = 'https://api-sandbox.asaas.com/v3/pix/qrCodes/static';
        if($config->sandbox_boleto == 0){
            $endPoint = 'https://api.asaas.com/v3/pix/qrCodes/static'; 
        }

        $response = $client->request('POST', $endPoint, [
            'body' => '{"value":'.$item->valor.'}',
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $config->asaas_token,
                'content-type' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody(),true);
        // echo '<img src="data:image/jpeg;base64,'.($data['encodedImage']).'">';

        // echo "<br>Payload: " . $data['payload'];

        session()->flash("flash_success", "QrCode gerado!");
        return view('payment.pix_asaas', compact('data', 'item'));

    }
}
