<div class="row">
	<div class="col-md-6 col-12">
		<h5>Produto: <strong>{{ $item->produto->nome }}</strong></h5>
		<h5>Lote: <strong>{{ $item->lote }}</strong></h5>
	</div>
	<div class="col-md-6 col-12">
		<h5>Data de vencimento: <strong>{{ __data_pt($item->data_vencimento, 0) }}</strong></h5>
		<h5>Valor de compra: <strong>R$ {{ __moeda($item->produto->valor_compra) }}</strong></h5>
	</div>

</div>