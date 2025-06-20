<div class="row">
	@foreach($data as $item)
	<div class="col-12 col-lg-6">
		<form method="GET" action="{{ route('pedido-delivery.altera-status') }}" id="form-{{$item->id}}">

			<div class="card">
				<div class="card-header">
					<h2>Pedido <strong class="text-danger">#{{ $item->id }}</strong></h2>
				</div>
				<div class="card-body" style="height: 160px; line-height: 0.1;">

					<p>Cliente: <strong class="text-primary">{{ $item->cliente->razao_social }}</strong></p>
					<p>Telefone: <strong class="text-primary">{{ $item->telefone }}</strong></p>
					<p>Tipo de pagamento: <strong class="text-primary">{{ $item->tipo_pagamento }}</strong></p>

					<p>Data: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></p>

					<p>Valor: <strong class="text-success">{{ __moeda($item->valor_total) }}</strong></p>
					<p>Troco para: <strong class="text-danger">{{ __moeda($item->troco_para) }}</strong></p>
					<p>Observação: <strong class="text-primary">{{ $item->observacao ? $item->observacao : '--' }}</strong></p>
					<p>Endereço: <strong class="text-primary">{{ $item->endereco ? $item->endereco->info : 'retirada no balcão' }}</strong></p>

				</div>

				<div class="card-footer">
					<input type="hidden" value="{{ $item->id }}" name="pedido_id">
					<input type="hidden" value="" id="estado" name="estado">
					<div class="row">
						<div class="col-lg-6 col-12">
							<button type="button" class="btn btn-danger w-100 btn-recusar">
								<i class="ri-close-fill"></i>
								Recusar
							</button>
						</div>
						<div class="col-lg-6 col-12">
							<button type="button" class="btn btn-success w-100 btn-confirmar">
								<i class="ri-check-double-fill"></i>
								Confirmar
							</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	@endforeach
</div>
