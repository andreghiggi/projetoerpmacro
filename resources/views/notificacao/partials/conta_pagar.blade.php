<div class="row">
	<div class="col-md-6 col-12">
		<h5>Data de vencimento: <strong>{{ __data_pt($item->data_vencimento, 0) }}</strong></h5>
		<h5>Valor: <strong>R$ {{ __moeda($item->valor_integral) }}</strong></h5>
	</div>
	<div class="col-md-6 col-12">
		<h5>Cliente: <strong>{{ $item->fornecedor->info }}</strong></h5>
		<h5>Descrição: <strong>{{ $item->descricao }}</strong></h5>
	</div>

	<div class="col-md-2 col-6">
		<a class="btn btn-dark" href="{{ route('conta-pagar.pay', [$item->id]) }}">Ver conta</a>
	</div>
</div>