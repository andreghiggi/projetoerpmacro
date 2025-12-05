<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pagamento;
use App\Models\PlanoEmpresa;
use App\Models\Plano;
use App\Models\Empresa;
use App\Models\FinanceiroPlano;
use App\Models\ConfiguracaoSuper;

class PaymentController extends Controller
{
    public function status($id){

        $item = Pagamento::where('transacao_id', $id)->first();

        $config = ConfiguracaoSuper::first();
        \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

        $payStatus = \MercadoPago\Payment::find_by_id($id);
        // $payStatus->status = "approved";
        if($payStatus->status != $item->status){
            $this->setarLicenca($item);

            $item->status = $payStatus->status;
            $item->save();
        }
        return response()->json($item->status, 200);
    }

    public function statusAsaas(Request $request){

        $plano = Plano::findOrfail($request->plano_id);
        $empresa = Empresa::findOrfail($request->empresa_id);

        $config = ConfiguracaoSuper::first();

        $client = new \GuzzleHttp\Client();
        $endPoint = 'https://api-sandbox.asaas.com/v3/pix/transactions';

        if($config->sandbox_boleto == 0){
            // $endPoint = 'https://api.asaas.com/v3/pix/qrCodes/static';
            $endPoint = 'https://api.asaas.com/v3/pix/transactions';
        }

        $response = $client->request('GET', $endPoint, [
            'headers' => [
                'accept' => 'application/json',
                'access_token' => $config->asaas_token,
            ],
        ]);

        $data = json_decode($response->getBody(),true);
        // return response()->json($data, 404);

        if(isset($data['data'])){
            $data = $data['data'];
            foreach($data as $d){
                if($d['conciliationIdentifier'] == $request->id){
                    $this->setarLicencaAsaas($plano, $empresa);
                    return response()->json("pago", 200);
                }
            }
        }
        return response()->json(0, 404);
    }

    private function setarLicenca($pagamento){
        $plano = $pagamento->plano;
        $empresa = $pagamento->empresa;
        $exp = date('Y-m-d', strtotime("+$plano->intervalo_dias days",strtotime( 
          date('Y-m-d'))));

        $planoEmpresa = PlanoEmpresa::create([
            'empresa_id' => $empresa->id,
            'plano_id' => $plano->id,
            'data_expiracao' => $exp,
            'valor' => $plano->valor,
            'forma_pagamento' => 'pix'
        ]);

        FinanceiroPlano::create([
            'empresa_id' => $empresa->id,
            'plano_id' => $plano->id,
            'valor' => $plano->valor,
            'tipo_pagamento' => 'PIX',
            'status_pagamento' => 'recebido',
            'plano_empresa_id' => $planoEmpresa->id
        ]);
    }

    private function setarLicencaAsaas($plano, $empresa){

        $exp = date('Y-m-d', strtotime("+$plano->intervalo_dias days",strtotime( 
          date('Y-m-d'))));

        $planoEmpresa = PlanoEmpresa::create([
            'empresa_id' => $empresa->id,
            'plano_id' => $plano->id,
            'data_expiracao' => $exp,
            'valor' => $plano->valor,
            'forma_pagamento' => 'pix'
        ]);

        FinanceiroPlano::create([
            'empresa_id' => $empresa->id,
            'plano_id' => $plano->id,
            'valor' => $plano->valor,
            'tipo_pagamento' => 'PIX',
            'status_pagamento' => 'recebido',
            'plano_empresa_id' => $planoEmpresa->id
        ]);
    }
}
