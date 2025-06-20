<html>
<style type="text/css">
	@page { size: auto;  margin: 0mm; }

	@media print {
		.print{
			margin: -5px;
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
		font-size: 1.5em;
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
					<th class="title" colspan="3">{{ $config->nome }}</th>
				</tr>
				<tr>
					<th colspan="3">{{ date('d/m/Y H:i:s') }}</th>
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
						<b>CUPOM NÃO FISCAL</b>
					</th>
				</tr>
			</thead>
			<tbody>
				@foreach($item->itens as $i)
				<tr class="top">
					<td colspan="3">{{ $i->produto->nome }}</td>
				</tr>
				<tr>
					<td>R${{ __moeda($i->valor_unitario) }}</td>
					<td>x{{ number_format($i->quantidade, 2) }}</td>
					<td>R${{ __moeda($i->sub_total) }}</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr class="sup ttu p--0">
					<td colspan="3">
						<b>Totais</b>
					</td>
				</tr>
				<tr class="ttu">
					<td colspan="2">Sub-total</td>
					<td align="right">R${{ __moeda($item->total + $item->desconto - $item->acrescimo) }}</td>
				</tr>
				<tr class="ttu">
					<td colspan="2">Acréscimo</td>
					<td align="right">R${{ __moeda($item->acrescimo) }}</td>
				</tr>
				<tr class="ttu">
					<td colspan="2">Desconto</td>
					<td align="right">R${{ __moeda($item->desconto) }}</td>
				</tr>
				<tr class="ttu">
					<td colspan="2">Total</td>
					<td align="right">R${{ __moeda($item->total) }}</td>
				</tr>
				<tr class="sup ttu p--0">
					<td colspan="3">
						<b>Pagamentos</b>
					</td>
				</tr>
				@foreach($item->fatura as $f) 
				<tr class="ttu">
					<td colspan="2">{{ \App\Models\Nfce::getTipoPagamento($f->tipo_pagamento) }}</td>
					<td align="right">R${{ __moeda($f->valor) }}</td>
				</tr>
				@endforeach
				
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
						{{ env("APP_URL") }}
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</body>

<script type="text/javascript">
	window.onload = function() { window.print(); window.close() }
</script>
</html>