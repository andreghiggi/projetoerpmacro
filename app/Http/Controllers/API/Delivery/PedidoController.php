<?php

namespace App\Http\Controllers\API\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PedidoDelivery;
use App\Models\ItemPedidoDelivery;
use App\Models\ItemAdicionalDelivery;
use App\Models\Cliente;
use App\Models\MarketPlaceConfig;
use App\Models\CupomDescontoCliente;
use App\Models\CupomDesconto;
use App\Models\ItemPizzaPedidoDelivery;
use DB;

class PedidoController extends Controller
{

    private function getLastNumero($empresa_id){
        $last = PedidoDelivery::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    public function save(Request $request){

        try{
            $result = DB::transaction(function () use ($request) {
                $cliente = Cliente::where('uid', $request->uid)->first();
                $itens = $request->itens;

                $cupom = null;
                if($request->pedido['cupom']){
                    $cupom = CupomDesconto::where('codigo',$request->pedido['cupom'])
                    ->first();
                }

                $dataPedido = [
                    'cliente_id' => $cliente->id,
                    'valor_total' => $request->pedido['total_produtos'] + __convert_value_bd($request->valor_entrega),
                    'tipo_pagamento' => $request->forma_pagamento,
                    'observacao' => $request->observacao ?? '',
                    'telefone' => $cliente->telefone,
                    'estado' => 'novo',
                    'endereco_id' => $request->endereco ? $request->endereco['id'] : null,
                    'motivoEstado' => '',
                    'troco_para' => $request->troco,
                    'cupom_id' => $cupom ? $cupom->id : null,
                    'desconto' => $request->pedido['desconto'],
                    'app' => 1,
                    'empresa_id' => $request->empresa_id,
                    'valor_entrega' => __convert_value_bd($request->valor_entrega),
                    'qr_code_base64' => '',
                    'qr_code' => '',
                    'transacao_id' => '',
                    'status_pagamento' => '',
                    'pedido_lido' => 0,
                    'horario_cricao' => date('H:i'),
                    'horario_entrega' => '',
                    'horario_leitura' => '',
                    'numero_sequencial' => $this->getLastNumero($request->empresa_id)
                ];
                // return response()->json("do", 401);
                $pedido = PedidoDelivery::create($dataPedido);

                if($cupom != null){
                    //insere cupom
                    CupomDescontoCliente::create([
                        'cliente_id' => $cliente->id,
                        'empresa_id' => $request->empresa_id,
                        'cupom_id' => $cupom ? $cupom->id : null,
                        'pedido_id' => $pedido->id
                    ]);
                }


                foreach($itens as $item){
                    $dataItem = [
                        'pedido_id' => $pedido->id,
                        'produto_id' => $item['id'],
                        'status' => 0,
                        'quantidade' => __convert_value_bd($item['qtd']),
                        'observacao' => $item['observacao'] ?? '',
                        'tamanho_id' => $item['tamanho_id'] ?? null,
                        'valor_unitario' => __convert_value_bd($item['valor']),
                        'sub_total' => __convert_value_bd($item['valor']) * __convert_value_bd($item['qtd'])
                    ];
                    $tItem = ItemPedidoDelivery::create($dataItem);

                    if(isset($item['adicionais'])){
                        foreach($item['adicionais'] as $add){
                            ItemAdicionalDelivery::create([
                                'item_pedido_id' => $tItem->id,
                                'adicional_id' => $add['id'],
                            ]);
                        }
                    }

                    if($item['sabores']){
                        foreach($item['sabores'] as $s){
                            ItemPizzaPedidoDelivery::create([
                                'item_pedido_id' => $tItem->id,
                                'produto_id' => $s
                            ]);
                        }
                    }
                }

                $pedido->sumTotal();

                if($request->forma_pagamento == 'Pix pelo App'){
                    $pedido = $this->gerarQrcode($pedido, $request->cpf);

                    $cpf = preg_replace('/[^0-9]/', '', $request->cpf);
                    $cliente->cpf_cnpj = $cpf;
                    $cliente->save();
                }

                if($request->forma_pagamento == 'Cartão pelo App'){
                    $payCard = $this->gerarPagamentoCartao($pedido, $request->tokenCard, $request->cpf, 
                        $request->paymentMethodId);

                    return $payCard;

                }
                return $pedido;
            });
if(isset($result['erro'])){
    return response()->json($result['erro'], 401);
}

return response()->json($result, 200);
}catch(\Exception $e){
    return response()->json($e->getMessage(), 404);
}
}

public function gerarQrcode($pedido, $cpf){
    $config = MarketPlaceConfig::where('empresa_id', $pedido->empresa_id)->first();
    $cliente = $pedido->cliente;

    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

    $payment = new \MercadoPago\Payment();

    $payment->transaction_amount = (float) number_format($pedido->valor_total,2);
    $payment->description = "pagamento do pedido #" . $pedido->id;
    $payment->payment_method_id = "pix";

    $cep = str_replace("-", "", $config->cep);

    $payment->payer = array(
        "email" => $cliente->email,
        // "first_name" => $cliente->nome,
        // "last_name" => $cliente->sobre_nome,
        "identification" => array(
            "type" => 'CPF',
            "number" => $cpf
        ),
        "address"=>  array(
            "zip_code" => $cep,
            "street_name" => $config->rua,
            "street_number" => $config->numero,
            "neighborhood" => $config->bairro,
            "city" => $config->cidade->nome,
            "federal_unit" => $config->cidade->uf
        )
    );

    $payment->save();
    if($payment->transaction_details){

        $pedido->transacao_id = $payment->id;
        $pedido->status_pagamento = $payment->status;
        $pedido->qr_code_base64 = $payment->point_of_interaction->transaction_data->qr_code_base64;
        $pedido->qr_code = $payment->point_of_interaction->transaction_data->qr_code;

        $pedido->save();
        return $pedido;

    }else{
        return [
            'erro' => $payment->error
        ];
    }

}

public function consultaPix(Request $request){
    try{

        $pedido = PedidoDelivery::findOrFail($request->pedido_id);
        
        $config = MarketPlaceConfig::where('empresa_id', $pedido->empresa_id)->first();
        \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

        if($pedido){
            $payStatus = \MercadoPago\Payment::find_by_id($pedido->transacao_id);
                // $payStatus->status = "approved";
            if($payStatus->status == "approved"){
                $pedido->status_pagamento = $payStatus->status;
                $pedido->estado = 'aprovado';
                $pedido->save();
            }
        }
        return response()->json($pedido->status_pagamento, 200);

    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function ultimoPedidoParaConfirmar(Request $request){
    $cliente = Cliente::where('uid', $request->uid)->first();

    $pedido = PedidoDelivery::
    where('cliente_id', $cliente->id)
    ->orderBy('id', 'desc')
    ->first();
    try{
        return response()->json($pedido, 200);

    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function consultaPedidoLido(Request $request){
    $pedido = PedidoDelivery::findOrFail($request->pedido_id);

    try{
        return response()->json($pedido, 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function gerarPagamentoCartao($pedido, $token, $cpf, $paymentMethodId){
    $config = MarketPlaceConfig::where('empresa_id', $pedido->empresa_id)->first();
    $cliente = $pedido->cliente;

    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

    $payment = new \MercadoPago\Payment();

    $payment->transaction_amount = (float) number_format($pedido->valor_total,2);
    $payment->description = "pagamento do pedido #" . $pedido->id;
    $payment->token = $token;
    $payment->installments = 1;
    $payment->payment_method_id = $paymentMethodId;

    $cep = str_replace("-", "", $config->cep);

    $payment->payer = array(
        "email" => $cliente->email,
        "first_name" => $cliente->nome,
        "last_name" => $cliente->sobre_nome,
        "identification" => array(
            "type" => 'CPF',
            "number" => $cpf
        ),
        "address"=>  array(
            "zip_code" => $cep,
            "street_name" => $config->rua,
            "street_number" => $config->numero,
            "neighborhood" => $config->bairro,
            "city" => $config->cidade->nome,
            "federal_unit" => $config->cidade->uf
        )
    );

    $payment->save();

    if($payment->error){

        return $payment->error;

    }else{
        $pedido->transacao_id = $payment->id;
        $pedido->status_pagamento = $payment->status;
            // $pedido->estado = 'aprovado';
        $pedido->save();
        return response()->json($pedido, 200);
    }
}

}
