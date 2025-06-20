<div class="row">
	@foreach($data as $item)
	<div class="col-md-6 col-12">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">
					{{ $item->cliente ? $item->cliente->info : '' }}
					{{ $item->fornecedor ? $item->fornecedor->info : '' }}
				</h5>
			</div>
			<div class="card-body">
				<h5>
					Telefone: 
					<strong class="text-primary">{{ $item->cliente ? $item->cliente->telefone : '' }}
					{{ $item->fornecedor ? $item->fornecedor->telefone : '' }}</strong>
				</h5>

				<h5>
					Email: 
					<strong class="text-primary">{{ $item->cliente ? $item->cliente->email : '' }}
					{{ $item->fornecedor ? $item->fornecedor->email : '' }}</strong>
				</h5>

				<h6>Data de cadastro: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></h6>
				<h6>Assunto: <strong class="text-primary">{{ $item->assunto }}</strong></h6>
			</div>
			<div class="card-footer">
				<a href="{{ route('crm.show', [$item->id]) }}" class="btn btn-primary">Visualizar</a>
			</div>
		</div>
	</div>
	@endforeach
</div>