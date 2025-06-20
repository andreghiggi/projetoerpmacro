<div class="row">
	<div class="col-md-6 col-12">
		<h5>Data de cadastro: <strong>{{ __data_pt($item->created_at) }}</strong></h5>
		<h5>Data de retorno: <strong>{{ __data_pt($item->data_retorno, 0) }}</strong></h5>
		<h5>Assunto: <strong>{{ $item->assunto }}</strong></h5>
	</div>
	<div class="col-md-6 col-12">
		@if($item->cliente)
		<h5>Cliente: <strong>{{ $item->cliente->info }}</strong></h5>
		<h5>Telefone: <strong>{{ $item->cliente->telefone }}</strong></h5>
		<h5>Email: <strong>{{ $item->cliente->email }}</strong></h5>
		@else
		<h5>Fornecedor: <strong>{{ $item->fornecedor->info }}</strong></h5>
		<h5>Telefone: <strong>{{ $item->fornecedor->telefone }}</strong></h5>
		<h5>Email: <strong>{{ $item->fornecedor->email }}</strong></h5>
		@endif

	</div>

	<div class="col-md-2 col-6">
		<a class="btn btn-dark" href="{{ route('crm.show', [$item->id]) }}">Visualizar</a>
	</div>
</div>