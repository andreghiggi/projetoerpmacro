<div class="row">
	<h4>{{ $item->produto->nome }}</h4>

	@if($nfeEntrada)
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">Compra</h5>
			</div>
			<div class="card-body">
				<h5>Fornecedor: <strong>{{ $nfeEntrada->nfe->fornecedor->info }}</strong></h5>
				<h5>Data de compra: <strong>{{ __data_pt($nfeEntrada->created_at) }}</strong></h5>
				<h5>Observação: <strong>{{ $nfeEntrada->observacao }}</strong></h5>

				<a class="btn w-50 btn-dark" href="{{ route('nfe.show', [$nfeEntrada->nfe->id]) }}">Ver compra</a>
			</div>
		</div>
	</div>
	@endif

	@if($nfeSaida)
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">Venda</h5>
			</div>
			<div class="card-body">
				<h5>Cliente: <strong>{{ $nfeSaida->nfe->cliente->info }}</strong></h5>
				<h5>Data de venda: <strong>{{ __data_pt($nfeSaida->created_at) }}</strong></h5>
				<h5>Observação: <strong>{{ $nfeSaida->observacao }}</strong></h5>

				<a class="btn w-50 btn-success" href="{{ route('nfe.show', [$nfeSaida->nfe->id]) }}">Ver venda</a>

			</div>
		</div>
	</div>

	@endif
</div>