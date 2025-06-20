<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\MercadoLivreConfig;
use App\Models\MercadoLivrePergunta;
use App\Models\Notificacao;
use App\Models\PedidoMercadoLivre;
use App\Models\ItemPedidoMercadoLivre;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Cidade;

class MercadoLivreUtil
{
    public function refreshToken($empresa_id)
    {
        $config = MercadoLivreConfig::where('empresa_id', $empresa_id)
        ->first();

        if($config == null){
            return 0;
        }

        $strtotimeAtual = strtotime(date('Y-m-d H:i:s'));
        // echo $strtotimeAtual . "<br>";
        // echo $config->token_expira . "<br>";
        // echo $strtotimeAtual < $config->token_expira;
        // die;
        if($strtotimeAtual < $config->token_expira){
            return "token valido!";
        }
        $curl = curl_init();
        $payload = json_encode([
            "grant_type" => "refresh_token",
            "client_id" => $config->client_id,
            "client_secret" => $config->client_secret,
            "accept" => "application/json",
            "content-type" => "application/x-www-form-urlencoded",
            "refresh_token" => $config->refresh_token
        ]);

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/oauth/token");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        $res = curl_exec($curl);
        $retorno = json_decode($res);

        if(isset($retorno->access_token)){
            if($config){
                $config->access_token = $retorno->access_token;
                $config->refresh_token = $retorno->refresh_token;
                $config->user_id = $retorno->user_id;
                $config->token_expira = strtotime(date('Y-m-d H:i:s')) + $retorno->expires_in;
                $config->save();

            }
        }

        return $retorno;

    }

    public function getNotification($config, $request){
        $resource = $request->resource;
        $tipo = explode("/", $resource);
        $tipo = $tipo[1];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com" . $resource);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ]);

        $res = curl_exec($curl);
        $retorno = json_decode($res);

        if($tipo == 'questions'){
            $this->inserePergunta($retorno, $config);
            return "pergunta inserida";
        }

        if($tipo == 'orders'){
            $item = $this->criaPedido($retorno, $config);
            $this->criaNotificacaoPedido($item);
            return "pedido inserido";
        }
        return $tipo;
    }

    private function inserePergunta($retorno, $config){
        $pergunta = MercadoLivrePergunta::where('_id', $retorno->id)
        ->first();

        if($pergunta == null){
            $pergunta = MercadoLivrePergunta::create([
                'empresa_id' => $config->empresa_id,
                '_id' => $retorno->id,
                'item_id' => $retorno->item_id,
                'status' => $retorno->status,
                'texto' => $retorno->text,
                'data' => substr($retorno->date_created, 0, 20)
            ]);

            $this->criaNotificacaoPergunta($pergunta);

            return $pergunta;
        }
    }

    private function criaNotificacaoPergunta($pergunta){
        $descricao = view('notificacao.partials.pergunta_mercado_livre', compact('pergunta'));
        Notificacao::create([
            'empresa_id' => $pergunta->empresa_id,
            'tabela' => 'mercado_livre_perguntas',
            'descricao' => $descricao,
            'descricao_curta' => 'Pergunta ' . ($pergunta->anuncio ? $pergunta->anuncio->nome : $pergunta->item_id),
            'referencia' => $pergunta->id,
            'status' => 1,
            'por_sistema' => 0,
            'super' => 1,
            'prioridade' => 'alta', 
            'visualizada' => 0,
            'titulo' => 'Pergunta mercado livre'
        ]);
    }

    private function criaNotificacaoPedido($item){
        $descricao = view('notificacao.partials.novo_pedido_mercado_livre', compact('item'));
        Notificacao::create([
            'empresa_id' => $item->empresa_id,
            'tabela' => 'pedido_mercado_livres',
            'descricao' => $descricao,
            'descricao_curta' => 'Novo pedido mercado livre #'.$item->_id,
            'referencia' => $item->id,
            'status' => 1,
            'por_sistema' => 0,
            'super' => 1,
            'prioridade' => 'alta', 
            'visualizada' => 0,
            'titulo' => 'Pedido mercado livre'
        ]);
    }

    public function criaPedido($pedido, $config){

        $dataPedido = [
            'empresa_id' => $config->empresa_id,
            '_id' => $pedido->id,
            'tipo_pagamento' => $pedido->payments[0]->payment_type,
            'status' => $pedido->status,
            'total' => $pedido->total_amount,
            'valor_entrega' => $pedido->shipping_cost ? $pedido->shipping_cost : 0,
            'nickname' => $pedido->seller->nickname,
            'seller_id' => $pedido->seller->id,
            'entrega_id' => $pedido->shipping ? $pedido->shipping->id : null,
            'data_pedido' => substr($pedido->date_created, 0, 19),
            'comentario' => $pedido->comment,
        ];

        $pedidoInsert = PedidoMercadoLivre::where('empresa_id', $config->empresa_id)
        ->where('_id', $pedido->id)->first();
        if($pedidoInsert == null){
            $pedidoInsert = PedidoMercadoLivre::create($dataPedido);
        }
        foreach($pedido->order_items as $itemPedido){
            $produto = Produto::where('mercado_livre_id', $itemPedido->item->id)
            ->first();

            $dataItem = [
                'pedido_id' => $pedidoInsert->id,
                'produto_id' => $produto ? $produto->id : null,
                'item_id' => $itemPedido->item->id,
                'item_nome' => $itemPedido->item->title,
                'condicao' => $itemPedido->item->condition,
                'variacao_id' => $itemPedido->item->variation_id,
                'quantidade' => $itemPedido->quantity,
                'valor_unitario' => $itemPedido->unit_price,
                'sub_total' => $itemPedido->quantity * $itemPedido->unit_price,
                'taxa_venda' => $itemPedido->sale_fee
            ];

            $itemInsert = ItemPedidoMercadoLivre::where('pedido_id', $pedidoInsert->id)
            ->where('item_id', $itemPedido->item->id)->first();

            if($itemInsert == null){
                ItemPedidoMercadoLivre::create($dataItem);
            }
        }

        if($pedido->shipping){
            $this->setDadosEntrega($pedido->shipping->id, $pedidoInsert, $config);
        }

        $cliente = $this->getDadosCliente($pedidoInsert->id);
        return PedidoMercadoLivre::findOrFail($pedidoInsert->id);
    }

    private function setDadosEntrega($shipping_id, $pedido, $config){

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 
            "https://api.mercadolibre.com/shipments/$shipping_id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ]);
        $res = curl_exec($curl);
        $retorno = json_decode($res);
        if(isset($retorno->destination)){
            $shipping_address = $retorno->destination->shipping_address;

            $pedido->rua_entrega = $shipping_address->street_name;
            $pedido->numero_entrega = $shipping_address->street_number;
            $pedido->cep_entrega = $shipping_address->zip_code;
            $pedido->comentario_entrega = $shipping_address->comment;
            $pedido->bairro_entrega = $shipping_address->neighborhood ? $shipping_address->neighborhood->name : '';
            $pedido->cidade_entrega = $shipping_address->city->name . " - " . $shipping_address->state->name;
            $pedido->save();
        }
    }

    private function getDadosCliente($pedido_id){
        $item = PedidoMercadoLivre::findOrFail($pedido_id);

        $curl = curl_init();
        $config = MercadoLivreConfig::where('empresa_id', $item->empresa_id)
        ->first();

        curl_setopt($curl, CURLOPT_URL, 
            "https://api.mercadolibre.com/orders/$item->_id/billing_info");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'x-version:2',
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ]);
        $res = curl_exec($curl);
        $retorno = json_decode($res);
        // dd($retorno);
        if(isset($retorno->buyer)){
            $info = $retorno->buyer->billing_info;
            $address = $info->address;
            try{
                $cidade = Cidade::where('nome', $address->city_name)->first();

                if($info->identification->type == 'CPF'){
                    $dataCliente = [
                        'cpf_cnpj' => $info->identification->number,
                        'razao_social' => "$info->name $info->last_name",
                        'email' => '',
                        'rua' => $address->street_name,
                        'numero' => $address->street_number,
                        'bairro' => isset($address->neighborhood) ? $address->neighborhood : '',
                        'consumidor_final' => 1,
                        'cep' => $address->zip_code,
                        'cidade_id' => $cidade ? $cidade->id : 1,
                        'empresa_id' => $item->empresa_id
                    ];
                }else{
                    $ie = $info->taxes->inscriptions->state_registration;

                    $dataCliente = [
                        'cpf_cnpj' => $info->identification->number,
                        'razao_social' => "$info->name " . (isset($info->last_name) ? $info->last_name : ''),
                        'email' => '',
                        'ie' => $ie,
                        'contribuinte' => $ie ? 1 : 0,
                        'consumidor_final' => $ie ? 0 : 1,
                        'rua' => isset($info->street_name) ? $info->street_name : $info->address->street_name,
                        'numero' => isset($info->street_number) ? $info->street_number : $info->address->street_number,
                        'bairro' => isset($address->neighborhood) ? $address->neighborhood : '',
                        'cep' => isset($info->zip_code) ? $info->zip_code : $info->address->zip_code,
                        'cidade_id' => $cidade ? $cidade->id : 1,
                        'empresa_id' => $item->empresa_id
                    ];

                }
            }catch(\Exception $e){
                // dd($info);
                $dataCliente = [
                    'cpf_cnpj' => $info->identification->number,
                    'razao_social' => "$info->name " . (isset($info->last_name) ? $info->last_name : ''),
                    'email' => '',
                    'ie' => '',
                    'contribuinte' => 0,
                    'consumidor_final' => 1,
                    'rua' => '',
                    'numero' => '',
                    'bairro' => '',
                    'cep' => '',
                    'cidade_id' => 1,
                    'empresa_id' => $item->empresa_id
                ];
            }

            $item->cliente_nome = $dataCliente['razao_social'];
            $item->cliente_documento = $dataCliente['cpf_cnpj'];

            $cliente = Cliente::where('empresa_id', $item->empresa_id)
            ->where('cpf_cnpj', $dataCliente['cpf_cnpj'])
            ->first();
            if($cliente == null){
                $cliente = Cliente::create($dataCliente);
            }

            if($cliente){
                $item->cliente_id = $cliente->id;
            }
            $item->save();

            return $cliente;
        }
        return null;
    }
}