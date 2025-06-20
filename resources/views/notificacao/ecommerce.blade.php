<div class="row">
	@foreach($data as $item)
	<div class="col-12 col-lg-6">
		<div class="card">
			<div class="card-header">
				<h2>Pedido <strong class="text-danger">#{{ $item->hash_pedido }}</strong></h2>
			</div>
			<div class="card-body" style="height: 160px; line-height: 0.1;">

				<p>Cliente: <strong class="text-primary">{{ $item->cliente->info }}</strong></p>
				<p>Telefone: <strong class="text-primary">{{ $item->cliente->telefone }}</strong></p>
				<p>Tipo de pagamento: <strong class="text-primary">{{ $item->tipo_pagamento }}</strong></p>

				<p>Data: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></p>

				<p>Valor total: <strong class="text-success">{{ __moeda($item->valor_total) }}</strong></p>
				<p>Valor frete: <strong class="text-danger">{{ __moeda($item->valor_frete) }}</strong></p>
				<p>Observação: <strong class="text-primary">{{ $item->observacao ? $item->observacao : '--' }}</strong></p>
				<p>Endereço: <strong class="text-primary">{{ $item->rua_entrega ? $item->endereco : 'retirada' }}</strong></p>

			</div>

			<div class="card-footer">
				<div class="row">
					<div class="col-lg-6 col-12">
						<a href="{{ route('pedidos-ecommerce.show', [$item->id]) }}" type="button" class="btn btn-success w-100 btn-confirmar">

							Visualizar
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endforeach
</div>
