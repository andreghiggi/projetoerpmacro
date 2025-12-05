<div class="row">
	<div class="col-md-6 col-12">
		<h5>ID Pedido: <strong>{{ $id }}</strong></h5>
		<h5>Data: <strong>{{ __data_pt($dataHoraOperacao) }}</strong></h5>
	</div>

	<div class="col-md-2 col-6">
		<a class="btn btn-dark" href="{{ route('vendizap-pedidos.index') }}">Visualizar</a>
	</div>
</div>