<html>
<style type="text/css">
	@page { size: auto;  margin: 0mm; }

	@media print {
		.print{
			margin: -8px;
		}
	}

	.text-center {
		text-align: center;
	}
	.ttu {
		text-transform: uppercase;
	}

	.printer-ticket {

		padding:0px;
		margin-left: auto;
		margin-right: auto;
		background: #ffffe0;
		display: table !important;
		width: 100%;
		max-width: 400px;
		font-weight: light;
		line-height: 1.1em;
	}
	.printer-ticket,
	.printer-ticket * {
		font-family: Tahoma, Geneva, sans-serif;
		font-size: 11px;
	}
	.printer-ticket th:nth-child(2),
	.printer-ticket td:nth-child(2) {
		width: 50px;
	}
	.printer-ticket th:nth-child(3),
	.printer-ticket td:nth-child(3) {
		width: 90px;
		text-align: right;
	}
	.printer-ticket th {
		font-weight: inherit;
		padding: 1px 0;
		text-align: center;
		border-bottom: 1px dashed #BCBCBC;
	}
	.printer-ticket tbody tr:last-child td {
		padding-bottom: 10px;
	}
	.printer-ticket tfoot .sup td {
		padding: 10px 0;
		border-top: 1px dashed #BCBCBC;
	}
	.printer-ticket tfoot .sup.p--0 td {
		padding-bottom: 0;
	}
	.printer-ticket .title {
		font-size: 14px;
		padding: 15px 0;
	}
	.printer-ticket .top td {
		padding-top: 10px;
	}
	.printer-ticket .last td {
		padding-bottom: 10px;
	}

</style>
<body>
	<div class="print">
		<table class="printer-ticket">
			<thead>
				<tr>
					<th class="title" colspan="3" style="line-height: 1">
						{{ $config->nome }}<br>
						<span style="font-size: 14px;">{{ $config->nome_fantasia }}</span><br>
						<span style="font-size: 14px;">{{ __setMask($config->cpf_cnpj) }}</span><br>
						<span style="font-size: 14px;">Inscrição Estadual: {{ $config->ie }}</span><br>
						<span style="font-size: 14px;">{{ $config->endereco }}</span><br>
						<span style="font-size: 14px;">{{ $config->celular }}</span><br>
					</th>
				</tr>
				
				@if($item->cliente)
				<tr>
					<th colspan="3">
						{{ $item->cliente->razao_social }} <br />
						{{ $item->cliente->cpf_cnpj }}
					</th>
				</tr>
				@endif
				<tr>
					<th class="ttu" colspan="3">
						<b>DOCUMENTO AUXILIAR</b>
					</th>
				</tr>
			</thead>
			<tbody>
				@foreach($item->itens as $i)
				<tr class="top">
					<td colspan="3">
						#{{ $i->produto->numero_sequencial }} 
						@if(sizeof($i->pizzas) > 0)
						@foreach($i->pizzas as $s)
						1/{{ sizeof($i->pizzas) }} {{ $s->sabor->nome }}
						@endforeach
						@else
						{{ $i->produto->nome }}
						@endif
						@if($i->tamanho)
						<strong>Tamanho:</strong> {{ $i->tamanho->nome }}
						@endif
					</td>
				</tr>
				@if(sizeof($i->adicionais) > 0)
				<tr>
					<td colspan="3">
						<strong>Adicionais:</strong>
						@foreach($i->adicionais as $a)
						{{ $a->adicional->nome }}@if(!$loop->last), @endif
						@endforeach
					</td>
				</tr>
				@endif
				<tr>
					<td>R${{ __moeda($i->valor_unitario) }}</td>
					<td>x{{ number_format($i->quantidade, 2) }}</td>
					<td>R${{ __moeda($i->sub_total) }}</td>
				</tr>
				@if($i->observacao)
				<tr>
					<td colspan="3">
						<strong>Observação:</strong>
						{{ $i->observacao }}
					</td>
				</tr>
				@endif
				@endforeach
			</tbody>
			<tfoot>
				<tr class="sup ttu p--0">
					<td colspan="3">
					</td>
				</tr>
				<tr class="ttu">
					<td colspan="2">Qtde total de itens:</td>
					<td align="right">{{ sizeof($item->itens) }}</td>
				</tr>
				<tr class="ttu">
					<td colspan="2">Valor total:</td>
					<td align="right">R${{ __moeda($item->total + $item->desconto - $item->acrescimo) }}</td>
				</tr>
				<tr class="ttu">
					<td colspan="2">Acréscimo:</td>
					<td align="right">R${{ __moeda($item->acrescimo) }}</td>
				</tr>
				<tr class="ttu">
					<td colspan="2">Desconto:</td>
					<td align="right">R${{ __moeda($item->desconto) }}</td>
				</tr>
				@if($item->valor_entrega)
				<tr class="ttu">
					<td colspan="2">Valor entrega:</td>
					<td align="right">R${{ __moeda($item->valor_entrega) }}</td>
				</tr>
				@endif
				
				<tr class="sup ttu p--0">
					<td colspan="3">
						<b>FORMA DE PAGAMENTO</b>
					</td>
				</tr>
				@foreach($item->fatura as $f) 
				<tr class="ttu">
					<td colspan="2">{{ \App\Models\Nfce::getTipoPagamento($f->tipo_pagamento) }}</td>
					<td align="right">R${{ __moeda($f->valor) }}</td>
				</tr>
				@endforeach

				<tr class="ttu">
					<td colspan="2">Troco:</td>
					<td align="right">R${{ __moeda($item->troco) }}</td>
				</tr>

				<tr class="ttu">
					<td colspan="2">Data:</td>
					<td align="right">{{ __data_pt($item->created_at) }}</td>
				</tr>

				<tr class="ttu">
					<td colspan="2">Código da venda:</td>
					<td align="right">{{ $item->numero_sequencial }}</td>
				</tr>
				
				@if($item->observacao)
				<tr class="sup">
					<td colspan="3" align="center">
						<b>Observação:</b>
						{{ $item->observacao }}
					</td>
				</tr>
				@endif

				<tr class="sup">
					<td colspan="3" align="center">
					</td>
				</tr>
				<tr class="">
					<td colspan="3" align="center">
					</td>
				</tr>
				<!-- <tr class="sup">
					<td colspan="3" align="center">
						{{ env("APP_URL") }}
					</td>
				</tr> -->
			</tfoot>
		</table>
	</div>
</body>

<script type="text/javascript">
	window.onload = function() { 
		window.print();
		setTimeout(() => {
			window.close() 
		}, 10)
	}
</script>
</html>