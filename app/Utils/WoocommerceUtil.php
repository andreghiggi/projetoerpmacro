<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\WoocommerceConfig;
use App\Models\WoocommercePedido;
use App\Models\WoocommerceItemPedido;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Produto;
use Automattic\WooCommerce\Client;

class WoocommerceUtil
{

	public function getConfig($empresa_id){
		$config = WoocommerceConfig::where('empresa_id', $empresa_id)->first();
		if($config == null) return null;

		$url = $config->url;
		$consumer_key = $config->consumer_key;
		$consumer_secret = $config->consumer_secret;
		$woocommerceClient = new Client($url, $consumer_key, $consumer_secret);
		return $woocommerceClient;
	}

	public function criaPedido($empresa_id, $pedido){

		$cidade = Cidade::where('nome', $pedido->shipping->city)
		->where('uf', $pedido->shipping->state)->first();

		$documento = $pedido->billing->cpf;
		if($documento == ""){
			$documento = $pedido->billing->cnpj;
		}
		$dataCliente = [
			'cpf_cnpj' => $documento,
			'razao_social' => $pedido->billing->first_name . " " . $pedido->billing->last_name,
			'email' => $pedido->billing->email,
			'telefone' => $pedido->billing->phone,
			'rua' => $pedido->shipping->address_1,
			'numero' => $pedido->shipping->number . " " .$pedido->shipping->address_2,
			'bairro' => $pedido->shipping->neighborhood,
			'consumidor_final' => 1,
			'cep' => $pedido->shipping->postcode,
			'cidade_id' => $cidade ? $cidade->id : 1,
			'empresa_id' => $empresa_id
		];

		$cliente = Cliente::where('empresa_id', $empresa_id)
		->where('cpf_cnpj', $documento)
		->first();
		if($cliente == null || $documento == ""){
			$cliente = Cliente::create($dataCliente);
		}

		$dataPedido = [
			'empresa_id' => $empresa_id,
			'pedido_id' => $pedido->id,
			'rua' => $pedido->shipping->address_1,
			'numero' => $pedido->shipping->number . " " .$pedido->shipping->address_2,
			'bairro' => $pedido->shipping->neighborhood,
			'cidade' => $pedido->shipping->city,
			'uf' => $pedido->shipping->state,
			'cep' => $pedido->shipping->postcode,
			'total' => $pedido->total,
			'valor_entrega' => $pedido->shipping_total,
			'desconto' => $pedido->discount_total,
			'observacao' => $pedido->customer_note,

			'nome' => $pedido->billing->first_name . " " . $pedido->billing->last_name,
			'email' => $pedido->billing->email,
			'documento' => '',
			'tipo_pagamento' => $pedido->payment_method_title,

			'data' => substr($pedido->date_modified, 0, 19),
			'numero_pedido' => $pedido->number,
			'cliente_id' => $cliente->id
		];

		$pedidoInsert = WoocommercePedido::where('empresa_id', $empresa_id)
		->where('pedido_id', $pedido->id)->first();
		if($pedidoInsert == null){
			$pedidoInsert = WoocommercePedido::create($dataPedido);
		}

		foreach($pedido->line_items as $itemPedido){
			$produto = Produto::where('woocommerce_id', $itemPedido->product_id)
			->first();

			$dataItem = [
				'pedido_id' => $pedidoInsert->id,
				'produto_id' => $produto ? $produto->id : null,
				'item_id' => $itemPedido->product_id,
				'item_nome' => $itemPedido->name,
				'quantidade' => $itemPedido->quantity,
				'valor_unitario' => $itemPedido->price,
				'sub_total' => $itemPedido->subtotal,
			];

			$itemInsert = WoocommerceItemPedido::where('pedido_id', $pedidoInsert->id)
			->where('item_id', $itemPedido->product_id)->first();

			if($itemInsert == null){
				WoocommerceItemPedido::create($dataItem);
			}
		}
	}

}