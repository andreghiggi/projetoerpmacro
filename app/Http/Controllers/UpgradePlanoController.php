<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\Empresa;
use App\Models\Pagamento;
use App\Models\ConfiguracaoSuper;

class UpgradePlanoController extends Controller
{
    public function index(Request $request){
        $empresa = Empresa::findOrFail($request->empresa_id);

        $segmento_id = sizeof($empresa->segmentos) > 0 ?$empresa->segmentos[0]->segmento_id : null;

        $planos = Plano::where('status', 1)
        ->where('visivel_clientes', 1)
        ->when($segmento_id, function ($q) use ($segmento_id) {
            return $q->where('segmento_id', $segmento_id);
        })
        ->where('valor', '>', 0)
        ->get();

        $config = ConfiguracaoSuper::first();

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

    public function assinatura(Request $request){
        $config = ConfiguracaoSuper::first();
        $empresa = Empresa::findOrFail($request->empresa_id);
        $plano = $empresa->plano;

        $p = Plano::find($plano->plano_id);
        if($p != null){
            $plano->valor = $p->valor;
        }
        if($config == null){
            session()->flash("flash_error", "Opção de pagamento não configurada!");
            return redirect()->back();
        }

        if($plano == null){
            session()->flash("flash_error", "Empresa sem plano atribuído!");
            return redirect()->back();
        }

        if((!$config->mercadopago_public_key && !$config->mercadopago_access_token) && !$config->asaas_token){
            session()->flash("flash_error", "Opção de pagamento não configurada!");
            return redirect()->back();
        }

        $dataAsaas = null;
        if($config->banco_plano == 'asaas'){
            $client = new \GuzzleHttp\Client();
            $endPoint = 'https://api-sandbox.asaas.com/v3/pix/qrCodes/static';
            if($config->sandbox_boleto == 0){
                $endPoint = 'https://api.asaas.com/v3/pix/qrCodes/static'; 
            }
            $response = $client->request('POST', $endPoint, [
                'body' => '{"value":'.$plano->valor.'}',
                'headers' => [
                    'accept' => 'application/json',
                    'access_token' => $config->asaas_token,
                    'content-type' => 'application/json',
                ],
            ]);
            $dataAsaas = json_decode($response->getBody(),true);
        }

        return view('payment.assinatura', compact('plano', 'config', 'dataAsaas'));
    }

    public function assinaturaMercadoPago(Request $request){
        $config = ConfiguracaoSuper::first();
        try{
            \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

            $payment = new \MercadoPago\Payment();

            $empresa = Empresa::findOrFail($request->empresa_id);
            $plano = $empresa->plano;
            $plano = Plano::findOrfail($plano->plano_id);

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
                session()->flash("flash_success", "QrCode gerado!");
                return redirect()->route('payment.assinatura_mercado_pago', [(string)$payment->id]);
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

}
