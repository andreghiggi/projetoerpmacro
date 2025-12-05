<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		.footer {
			position: fixed;
			bottom: 0px;
			padding: 0;
		}
		.footer small{
			color:grey; 
			font-size: 10px;
			text-align: left;
			margin-top: 100px !important;
		}
		body{
			width: 260px;
			/*background: #000;*/
			margin-left: -40px;
			margin-top: -40px;
		}
		.mt-20{
			margin-top: -20px;
		}
		.mt-10{
			margin-top: -10px;
		}
		.mt-25{
			margin-top: -25px;
		}
		table th{
			font-size: 10px;
			text-align: left;
		}

		table td{
			font-size: 11px;
		}

	</style>
</head>
<header>
	<div class="headReport">
	</div>
</header>
<body>
	<h5 style="text-align:center; " class="mt-10">Impressão de pedido</h5>
	<h5 class="mt-20" style="text-align:center; font-size: 12px;">{{ $config->nome }}</h5>
	<h5 style="text-align:center; " class="mt-20">#{{$item->numero_sequencial}} - {{ $item->cliente->razao_social }}</h5>
	<h5 class="mt-20" style="text-align:center; font-size: 8px;">
		{{ $config->rua }}, {{ $config->numero }} - {{ $config->bairro }}
	</h5>
	<h5 class="mt-10" style="text-align:center; font-size: 8px;">
		{{ $item->cliente->telefone }}
	</h5>

	<table>
		<thead>
			<tr>
				<th style="width: 120px">Produto</th>
				<th style="width: 40px">Qtd</th>
				<th style="width: 40px">Vl. unit</th>
				<th style="width: 50px">Subtotal</th>
			</tr>
		</thead>
		<tbody>
			@foreach($item->itens as $i)
			<tr>
				@if(sizeof($i->pizzas) > 0 && str_contains($i->produto->nome, 'izza'))
				<td>Pizza</td>
				@else
				<td>{{ $i->produto->nome }}</td>
				@endif
				<td>{{ number_format($i->quantidade,2) }}</td>
				<td>{{ __moeda($i->valor_unitario) }}</td>
				<td>{{ __moeda($i->sub_total) }}</td>
			</tr>
			@if(sizeof($i->adicionais) > 0)
			<tr>
				<td style="font-weight: bold; font-size: 8.5px;" colspan="4">adicioanis: {{ $i->getAdicionaisStr() }}</td>
			</tr>
			@endif

			@if($i->observacao != '')
			<tr>
				<td style="font-weight: bold; font-size: 8.5px;" colspan="4">observação: {{ $i->observacao }}</td>
			</tr>
			@endif

			@if(sizeof($i->pizzas) > 0)
			<tr>
				<td style="font-weight: bold; font-size: 8.5px;" colspan="4">sabores:
					@foreach($i->pizzas as $s)
					{{ $s->sabor->nome }}@if(!$loop->last) | @endif
					@endforeach
				</td>
			</tr>
			@if($i->tamanho)
			<tr>
				<td style="font-weight: bold; font-size: 8.5px;" colspan="4">tamanho:
					{{ $i->tamanho->nome }}
				</td>
			</tr>
			@endif
			@endif
			@endforeach
		</tbody>
	</table>

	@if($item->valor_entrega > 0)
	<h6>Valor de entrega: <strong>R$ {{ __moeda($item->valor_entrega) }}</strong></h6>
	@else
	<h6></h6>
	@endif
	<h6 class="mt-25">Total: <strong>{{ __moeda($item->valor_total) }}</strong></h6>
	<h6 class="mt-25">Total de itens: <strong>{{ sizeof($item->itens) }}</strong></h6>

	@if($item->observacao != '')
	<h6 class="mt-25">Observacao: <strong>{{ $item->observacao }}</strong></h6>
	@endif

	@if($item->endereco)
	<h6 class="mt-25">Endereço: <strong>{{ $item->endereco->info }}</strong></h6>
	@else
	<h6 class="mt-25">Retirada no balcão</h6>
	@endif

	@if($item->troco_para)
	<h6 class="mt-25">Troco para: <strong>R$ {{ __moeda($item->troco_para) }}</strong></h6>
	@endif
	<h6 class="mt-25">Tipo de pagamento: <strong>{{ $item->tipo_pagamento }}</strong></h6>
	<h6 class="mt-25">Estado do pagamento: <strong>
		@if($item->tipo_pagamento == 'Pix pelo App' && $item->status_pagamento == "approved")
		APROVADO
		@else
		PENDENTE
		@endif
	</strong></h6>

</body>
