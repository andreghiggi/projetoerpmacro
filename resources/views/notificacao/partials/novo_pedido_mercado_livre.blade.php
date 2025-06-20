<div class="row">
	<div class="col-md-6 col-12">
		<h5>Total de itens: <strong>{{ sizeof($item->itens) }}</strong></h5>
	</div>
	<div class="col-md-6 col-12">
		<h5>Cliente: <strong>{{ $item->cliente_nome }}</strong></h5>
		<h5>Documento: <strong>{{ $item->cliente_documento }}</strong></h5>
	</div>

	<div class="col-md-2 col-6 mt-3">
		<a class="btn btn-dark" href="{{ route('mercado-livre-pedidos.show', [$item->id]) }}">Ver pedido</a>
	</div>
</div>