<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carrinho;
use App\Models\Cliente;
use App\Models\PedidoEcommerce;
use App\Models\ItemPedidoEcommerce;
use App\Models\EcommerceConfig;
use App\Models\CategoriaProduto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Utils\UploadUtil;

class PagamentoController extends Controller
{

    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
        session_start();
    }

    public function index(Request $request){

        $carrinho = $this->_getCarrinho();

        $config = EcommerceConfig::findOrfail($request->loja_id);
        if($carrinho == []){
            return redirect()->route('loja.index', 'link='.$config->loja_id);
        }
        $categorias = CategoriaProduto::where('ecommerce', 1)->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();

        $clienteLogado = $this->_getClienteLogado();
        if(!$clienteLogado){
            session()->flash("flash_error", "Cliente não localizado!");
            return redirect()->route('loja.login', 'link='.$config->loja_id);
        }
        $tiposPagamento = json_decode($config->tipos_pagamento);
        if(sizeof($tiposPagamento) == 0){
            session()->flash("flash_error", "Nenhum tipo de pagamento!");
            return redirect()->back();
        }

        return view('loja.pagamento', compact('carrinho', 'config', 'categorias', 'tiposPagamento'));
    }

    public function novaChavePix(Request $request){

        $item = PedidoEcommerce::where('hash_pedido',$request->hash)->first();
        $clienteLogado = $this->_getClienteLogado();

        if($item == null){
            session()->flash("flash_error", "Algo deu errado");
            return redirect()->back();
        }
        if($item->cliente_id != $clienteLogado){
            session()->flash("flash_error", "Algo deu errado");
            return redirect()->back();
        }
        $config = EcommerceConfig::findOrfail($request->loja_id);
        
        $categorias = CategoriaProduto::where('ecommerce', 1)->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();

        return view('loja.pagamento_pix', compact('config', 'categorias', 'item'));

    }

    private function _getClienteLogado(){
        if(isset($_SESSION['cliente_ecommerce'])){
            return $_SESSION['cliente_ecommerce'];
        }
        return null;
    }

    private function _getCarrinho(){
        $data = [];
        if(isset($_SESSION["session_cart"])){
            $data = Carrinho::where('session_cart', $_SESSION["session_cart"])
            ->first();
        }
        return $data;
    }

    public function pagamentoNovoPix(Request $request){
        try{
            $config = EcommerceConfig::findOrfail($request->loja_id);
            $pedido = PedidoEcommerce::findOrfail($request->pedido_id);
            $result = DB::transaction(function () use ($request, $config, $pedido) {

                \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);
                $payment = new \MercadoPago\Payment();
                $payment->transaction_amount = $pedido->valor_total;
                $payment->description = "Pedido $pedido->hash_pedido, loja " . $config->nome;
                $payment->payment_method_id = "pix";

                $cep = str_replace("-", "", $config->cep);
                $payment->payer = array(
                    "email" => $request->payerEmail,
                    "first_name" => $request->payerFirstName,
                    "last_name" => $request->payerLastName,
                    "identification" => array(
                        "type" => $request->docType,
                        "number" => preg_replace('/[^0-9]/', '', $request->docNumber)
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
                    $pedido->status_pagamento = $payment->status;
                    $pedido->qr_code_base64 = $payment->point_of_interaction->transaction_data->qr_code_base64;
                    $pedido->qr_code = $payment->point_of_interaction->transaction_data->qr_code;
                    $pedido->transacao_id = (string)$payment->id;
                    $pedido->tipo_pagamento = 'pix';
                    $pedido->save();

                    session()->flash("flash_success", "PIX gerado!");
                    return [
                        'sucesso' => 1,
                        'transacao_id' => $pedido->transacao_id
                    ];
                }else{

                    session()->flash("flash_error", $payment->error);
                    return [
                        'erro' => $payment->error
                    ];
                }
            });

            if(isset($result['sucesso'])){

                return redirect()->route('loja.finalizar', 'link='.$config->loja_id .'&transacao_id='.$result['transacao_id'].'&hash_pedido='.$pedido->hash_pedido);
            }else{
                return redirect()->back();
            }
        }catch(\Exception $e){
            session()->flash("flash_error", $e->getMessage());
            return redirect()->back();
        }
    }

    public function pagamentoPix(Request $request){
        try{
            $config = EcommerceConfig::findOrfail($request->loja_id);
            $result = DB::transaction(function () use ($request, $config) {

                $pedido = $this->createPedido($request);
                $carrinho = $this->_getCarrinho();

                \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);
                $payment = new \MercadoPago\Payment();
                $payment->transaction_amount = $pedido->valor_total;
                $payment->description = "Pedido $pedido->hash_pedido, loja " . $config->nome;
                $payment->payment_method_id = "pix";

                $cep = str_replace("-", "", $config->cep);
                $payment->payer = array(
                    "email" => $request->payerEmail,
                    "first_name" => $request->payerFirstName,
                    "last_name" => $request->payerLastName,
                    "identification" => array(
                        "type" => $request->docType,
                        "number" => preg_replace('/[^0-9]/', '', $request->docNumber)
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
                    $pedido->status_pagamento = $payment->status;
                    $pedido->qr_code_base64 = $payment->point_of_interaction->transaction_data->qr_code_base64;
                    $pedido->qr_code = $payment->point_of_interaction->transaction_data->qr_code;
                    $pedido->transacao_id = (string)$payment->id;
                    $pedido->tipo_pagamento = 'pix';
                    $pedido->save();

                    session()->flash("flash_success", "PIX gerado!");
                    return [
                        'sucesso' => 1,
                        'transacao_id' => $pedido->transacao_id,
                        'hash_pedido' => $pedido->hash_pedido
                    ];
                }else{

                    session()->flash("flash_error", $payment->error);
                    return [
                        'erro' => $payment->error
                    ];
                }
            });

            if(isset($result['sucesso'])){
                return redirect()->route('loja.finalizar', 'link='.$config->loja_id .'&transacao_id='.$result['transacao_id'].'&hash_pedido='.$result['hash_pedido']);
            }else{
                return redirect()->back();
            }
        }catch(\Exception $e){
            session()->flash("flash_error", $e->getMessage());
            return redirect()->back();
        }
    }

    public function pagamentoBoleto(Request $request){
        try{
            $config = EcommerceConfig::findOrfail($request->loja_id);

            $result = DB::transaction(function () use ($request, $config) {

                $pedido = $this->createPedido($request);
                $carrinho = $this->_getCarrinho();

                \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);
                $payment = new \MercadoPago\Payment();
                $payment->transaction_amount = $pedido->valor_total;
                $payment->description = "Pedido $pedido->hash_pedido, loja " . $config->nome;
                $payment->payment_method_id = "bolbradesco";

                $cep = str_replace("-", "", $config->cep);
                $payment->payer = array(
                    "email" => $request->payerEmail,
                    "first_name" => $request->payerFirstName,
                    "last_name" => $request->payerLastName,
                    "identification" => array(
                        "type" => $request->docType,
                        "number" => preg_replace('/[^0-9]/', '', $request->docNumber)
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
                    $pedido->status_pagamento = $payment->status;
                    $pedido->transacao_id = (string)$payment->id;
                    $pedido->link_boleto = $payment->transaction_details->external_resource_url;
                    $pedido->tipo_pagamento = 'boleto';
                    $pedido->save();

                    session()->flash("flash_success", "Boleto gerado!");
                    return [
                        'sucesso' => 1,
                        'transacao_id' => $pedido->transacao_id,
                        'hash_pedido' => $pedido->hash_pedido
                    ];
                }else{

                    session()->flash("flash_error", $payment->error);
                    return [
                        'erro' => $payment->error
                    ];
                }
            });

            if(isset($result['sucesso'])){
                return redirect()->route('loja.finalizar', 'link='.$config->loja_id .'&transacao_id='.$result['transacao_id'].'&hash_pedido='.$result['hash_pedido']);
            }else{
                session()->flash("flash_error", $result['erro']);
                return redirect()->back();
            }
        }catch(\Exception $e){
            session()->flash("flash_error", $e->getMessage());
            return redirect()->back();
        }
    }

    public function pagamentoDeposito(Request $request){
        try{
            $config = EcommerceConfig::findOrfail($request->loja_id);
            $pedido = DB::transaction(function () use ($request, $config) {

                $pedido = $this->createPedido($request);
                $carrinho = $this->_getCarrinho();
                $pedido->tipo_pagamento = 'deposito';
                $pedido->save();
                session()->flash("flash_success", "Pedido gerado!");
                return $pedido;
            });

            return redirect()->route('loja.finalizar-deposito', 'link='.$config->loja_id.'&hash='.$pedido->hash_pedido);
        }catch(\Exception $e){
            session()->flash("flash_error", $e->getMessage());
            return redirect()->back();
        }
    }

    // private function trataErros($arr){
    //     $cause = $arr->causes[0];
    //     $errorCode = $cause->code;
    //     $arrCode = $this->arrayErros($arr);
    //     return $arrCode[$errorCode];
    // }

    // private function arrayErros($arr){
    //     return [
    //         '2067' => 'Número documento inválido!',
    //         '13253' => 'Ative o QR code do cadastro!'
    //     ];
    // }

    private function createPedido($request){
        $carrinho = $this->_getCarrinho();

        $config = EcommerceConfig::findOrfail($request->loja_id);

        $clienteLogado = $this->_getClienteLogado();

        $cliente = Cliente::findOrFail($clienteLogado);
        // $cliente->email = $request->payerEmail;
        $cliente->cpf_cnpj = $request->docNumber;
        // $cliente->rua = $request->rua;
        // $cliente->numero = $request->numero;
        // $cliente->bairro = $request->bairro;
        // $cliente->cep = $request->cep;
        $cliente->save();


        $pedido = PedidoEcommerce::create([
            'cliente_id' => $clienteLogado,
            'empresa_id' => $config->empresa_id,
            'endereco_id' => $carrinho->endereco_id,
            'estado' => 'novo',
            'observacao' => $request->observacao ?? '',
            'hash_pedido' => Str::random(12),
            'valor_total' => $carrinho->valor_total,
            'valor_frete' => $carrinho->valor_frete,
            'desconto' => 0,
            'link_boleto' => '',
            'qr_code_base64' => '',
            'qr_code' => '',
            'tipo_frete' => $carrinho->tipo_frete,
            'rua_entrega' => $carrinho->endereco ? $carrinho->endereco->rua : '',
            'cidade_entrega' => $carrinho->endereco ? $carrinho->endereco->cidade->info : '',
            'numero_entrega' => $carrinho->endereco ? $carrinho->endereco->numero : '',
            'referencia_entrega' => $carrinho->endereco ? $carrinho->endereco->referencia : '',
            'bairro_entrega' => $carrinho->endereco ? $carrinho->endereco->bairro : '',
            'cep_entrega' => $carrinho->endereco ? $carrinho->endereco->cep : '',
            'nome' => $request->payerFirstName ?? '',
            'sobre_nome' => $request->payerLastName ?? '',
            'email' => $request->payerEmail ?? '',
            'tipo_documento' => $request->docType ?? '',
            'numero_documento' => $request->docNumber ?? ''
        ]);

        foreach($carrinho->itens as $item){
            $item = ItemPedidoEcommerce::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $item->produto_id,
                'variacao_id' => $item->variacao_id,
                'quantidade' => $item->quantidade,
                'valor_unitario' => $item->valor_unitario,
                'sub_total' => $item->sub_total
            ]);
        }

        return $pedido;
    }

    public function finalizar(Request $request){
        $pedido = PedidoEcommerce::where('hash_pedido', $request->hash_pedido)
        ->first();
        $_SESSION["session_cart"] = null;
        $config = EcommerceConfig::where('empresa_id', $pedido->empresa_id)->first();
        
        if($pedido == null){
            session()->flash("flash_error", "Algo deu errado!");
            return redirect()->route('loja.index', ['link='.$config->loja_id]);
        }

        $categorias = CategoriaProduto::where('ecommerce', 1)->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();
        return view('loja.finalizar', compact('pedido', 'config', 'categorias'));
    }

    public function finalizarDeposito(Request $request){

        $pedido = PedidoEcommerce::where('hash_pedido', $request->hash)
        ->first();
        $_SESSION["session_cart"] = null;

        if($pedido == null){
            session()->flash("flash_error", "Algo deu errado!");
            return redirect()->back();
        }
        $config = EcommerceConfig::findOrfail($request->loja_id);
        $categorias = CategoriaProduto::where('ecommerce', 1)->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();
        return view('loja.finalizar', compact('pedido', 'config', 'categorias'));
    }

    public function pagamentoCartao(Request $request){
        try{
            $config = EcommerceConfig::findOrfail($request->loja_id);
            $result = DB::transaction(function () use ($request, $config) {

                $pedido = $this->createPedido($request);
                $carrinho = $this->_getCarrinho();
                
                //teste
                // $pedido->status_pagamento = 'approved';
                // $pedido->transacao_id = "123";
                // $pedido->tipo_pagamento = 'cartao';
                // $pedido->save();

                // session()->flash("flash_success", "Pagamento concluído com sucesso!");
                // return [
                //     'sucesso' => 1,
                //     'hash_pedido' => $pedido->hash_pedido
                // ];
                //teste

                \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

                $payment = new \MercadoPago\Payment();
                $payment->transaction_amount = (float)$request->transactionAmount;
                $payment->token = $request->token;
                $payment->description = "Pagamento do pedido #" . $pedido->hash_pedido;
                $payment->installments = (int)$request->installments;
                $payment->payment_method_id = $request->paymentMethodId;

                $payer = new \MercadoPago\Payer();
                $payer->email = $request->email;
                $payer->identification = array(
                    "type" => $request->docType,
                    "number" => $request->docNumber
                );
                $payment->payer = $payer;
                // dd($payment);
                $payment->save();

                // dd($request->all());

                if($payment->error){
                    // dd($payment);
                    session()->flash("flash_error", $payment->error);
                    return [
                        'erro' => $payment->error
                    ];
                }else{
                    $pedido->status_pagamento = $payment->status;
                    $pedido->transacao_id = (string)$payment->id;
                    $pedido->tipo_pagamento = 'cartao';
                    $pedido->save();

                    // dd($payment);
                    session()->flash("flash_success", "Pagamento concluído com sucesso!");
                    return [
                        'sucesso' => 1,
                        'hash_pedido' => $pedido->hash_pedido
                    ];
                }
            });

            if(isset($result['sucesso'])){
                return redirect()->route('loja.finalizar', 'link='.$config->loja_id .'&hash_pedido='.$result['hash_pedido']);
            }else{
                return redirect()->back();
            }
        }catch(\Exception $e){
            session()->flash("flash_error", $e->getMessage());
            return redirect()->back();
        }
    }

    public function enviarComprovante(Request $request){
        $pedido = PedidoEcommerce::findOrfail($request->pedido_id);
        if ($request->hasFile('file')) {
            $config = EcommerceConfig::where('empresa_id', $pedido->empresa_id)->first();

            $file_name = $this->util->uploadImage($request, '/comprovantes', 'file');

            session()->flash("flash_success", "Comprovante enviado com sucesso!");
            $pedido->comprovante = $file_name;
            $pedido->save();
            return redirect()->route('loja.index', 'link='.$config->loja_id);
        }else{
            session()->flash("flash_error", "Selecione o arquivo!");
            return redirect()->back();
        }
    }

}
