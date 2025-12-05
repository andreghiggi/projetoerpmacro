<html>
<style type="text/css">
	@page { size: auto;  margin: 0mm; }

	@media print {
		.print{
			margin: -5px;
		}
		.noPrint {display:none;}
	}

	.text-center {
		/*text-align: center;*/
	}
	.ttu {
		text-transform: uppercase;
	}

	.printer-ticket {

		padding:0px;
		margin-left: auto;
		margin-right: auto;
		/*background: #ffffe0;*/
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
		/*text-align: center;*/
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

	@if($config != null)
	td, th{
		line-height: {{$config->distancia_entre_linhas}}px;
	}
	@endif

</style>
<body>
	<button class="noPrint" onclick="window.print()">Imprimir</button>
	<div class="print" @if($config != null) style="margin-top: {{$config->margem_topo}}px;" @endif>
		@foreach($item->itens as $i)
		@for($x=0; $x<$i->quantidade; $x++)
			<table class="printer-ticket" @if($config != null) style="margin-left: {{$config->margem_lateral}}px;" @endif>
				<thead>
					<tr>
						@if($config != null)

						<th class="title" colspan="3">
							@if($empresa->logo)
							<img style="width: {{ $config->largura_imagem }}; height: {{ $config->altura_imagem }}; border-radius: 5px" src="{{ env('APP_URL').'/uploads/logos/'. $empresa->logo }}">
							@else
							<img style="width: {{ $config->largura_imagem }}; height: {{ $config->altura_imagem }}; border-radius: 5px" src="{{ env('APP_URL').'/imgs/no-image.png' }}">
							@endif
						</th>
						@else
						<th class="title" colspan="3">
							@if($empresa->logo)
							<img style="width: 140px; border-radius: 5px" src="{{ env('APP_URL').'/uploads/logos/'. $empresa->logo }}">
							@else
							<img style="width: 140px; border-radius: 5px" src="{{ env('APP_URL').'/imgs/no-image.png' }}">
							@endif
						</th>
						@endif
					</tr>
					<tr>
						<th colspan="3">
							Nº da OP {{ $item->codigo_sequencial }}
						</th>
					</tr>

					<tr>
						<th class="ttu" colspan="3">
							Nº do Pedido {{ $i->itemProducao ? $i->itemProducao->itemNfe->nfe->numero_sequencial : $i->numero_pedido }}
						</th>
					</tr>
				</thead>
				<tbody>
					@if($i->observacao)
					<tr>
						<td>{{ $i->observacao }}</td>
					</tr>
					@endif
					<tr>
						<td>
							Cliente/Razão Soscial:<br>
							<strong>{{ $i->itemProducao ? $i->itemProducao->itemNfe->nfe->cliente->razao_social : $i->cliente->info }}</strong>
						</td>
					</tr>
					<tr>
						<td>
							Item Produto:<br>
							<strong>{{ $i->produto->nome }}
								@if($i->itemProducao)
								{{ $i->itemProducao->dimensao }}
								@endif
							</strong>

						</td>
					</tr>
					<tr>
						<td>
							Data do pedido:<br>
							<strong>{{ __data_pt($item->created_at) }}</strong>
						</td>
					</tr>
				</tbody>

			</table>
			@if($config != null)
			<div style="height: {{$config->distancia_entre_etiquetas}}px;"></div>
			@endif

			@endfor
			@endforeach
		</div>
	</body>

	<script type="text/javascript">
	// window.onload = function() { window.print(); }
</script>
</html>