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
					<th class="title" colspan="3">COMANDA: <strong style="font-size: 25px">{{ $pedido->comanda }}</strong></th>
				</tr>
				<tr>
					<th colspan="3">Impresso em: <strong>{{ date('d/m/Y H:i:s') }}</strong></th>
				</tr>
				<tr>
					<th class="ttu" colspan="3">
						<b>IMPRESSÃO AUTOMATICA</b>
					</th>
				</tr>
			</thead>
			<tbody>
				@foreach($itens as $i)
				<tr class="top">
					<td colspan="3">
						@if(sizeof($i->pizzas) > 0)
						@foreach($i->pizzas as $pz)
						<strong>1/{{ sizeof($i->pizzas) }} {{ $pz->sabor->nome }}</strong>
						@endforeach
						@else
						<strong>{{ $i->produto->nome }}</strong>
						@endif
						@if(sizeof($i->adicionais) > 0)
						<br>
						adicioanis: <strong>{{ $i->getAdicionaisStr() }}</strong>
						@endif

						@if($i->observacao)
						<br>
						observação: <strong>{{ $i->observacao }}</strong>
						@endif

						@if($i->tamanho)
						- tamanho: <strong>{{ $i->tamanho->nome }}</strong>
						@endif

						@if($i->pedido->_mesa)
						<br>
						<strong>{{ $i->pedido->_mesa->nome }}</strong>
						@endif

					</td>
				</tr>
				<tr>
					<td>Qtd: <strong>{{ number_format($i->quantidade, 2) }}</strong></td>
				</tr>
				@endforeach
			</tbody>

		</table>
	</div>
</body>

<script type="text/javascript">
	window.onload = function() { window.print(); window.close() }
</script>
</html>