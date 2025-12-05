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
		.mt-45{
			margin-top: -50px;
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
	<h5 style="text-align:center; " class="mt-10">IMPRESSÃO DE PEDIDO</h5>
	<h5 style="text-align:center; font-size: 30px" class="mt-20">{{$item->comanda}}</h5>
	<h5 class="mt-45" style="text-align:center; font-size: 12px;">{{ $config->nome }}</h5>
	<h5 class="mt-20" style="text-align:center; font-size: 8px;">
		{{ $config->rua }}, {{ $config->numero }} - {{ $config->bairro }}
	</h5>

	<table>
		<thead>
			<tr>
				<th style="width: 130px">Produto</th>
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
				<td style="font-weight: bold; font-size: 9.5px;" colspan="4">adicioanis: {{ $i->getAdicionaisStr() }}</td>
			</tr>
			@endif

			@if($i->observacao != '')
			<tr>
				<td style="font-weight: bold; font-size: 9.5px;" colspan="4">observação: {{ $i->observacao }}</td>
			</tr>
			@endif

			@if(sizeof($i->pizzas) > 0)
			<tr>
				<td style="font-weight: bold; font-size: 9.5px;" colspan="4">sabores:
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

			@foreach($item->itensServico as $i)
			<tr>
				<td>Servico: {{ $i->servico->nome }}</td>
				<td>{{ __moeda($i->quantidade) }}</td>
				<td>{{ __moeda($i->valor_unitario) }}</td>
				<td>{{ __moeda($i->sub_total) }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<h6 class="mt-1">Total de itens: <strong>{{ sizeof($item->itens) }}</strong></h6>
	@if($item->_mesa)
	<h6 class="mt-25"><strong>{{ $item->_mesa->nome }}</strong></h6>
	@endif
	@if($item->cliente_nome != '')
	<h6 class="mt-25">Cliente: <strong>{{ $item->cliente_nome }}</strong></h6>
	@endif

	@if($item->cliente_fone != '')
	<h6 class="mt-25">Telefone: <strong>{{ $item->cliente_fone }}</strong></h6>
	@endif
	<h6 class="mt-25">Valor total de produtos: <strong>R$ {{ __moeda($item->total) }}</strong></h6>

	@if($item->acrescimo > 0)
	<h6 class="mt-25">Acréscimo: <strong>R$ {{ __moeda($item->acrescimo) }}</strong></h6>
	<h6 class="mt-25">Valor total: <strong>R$ {{ __moeda($item->total+$item->acrescimo) }}</strong></h6>
	@endif

</body>
