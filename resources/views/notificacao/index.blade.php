<div class="row">
	@foreach($data as $item)
	<div class="col-12 col-lg-6">
		<div class="card">
			<div class="card-header">
				<h2><strong class="text-danger">{{ $item->mesa }}</strong></h2>
			</div>
			<div class="card-body" style="height: 130px;">
				@if($item->tipo == 'garcom')
				<h3>CHAMANDO GARÇOM</h3>
				@else

				@if($item->pedido->em_atendimento == 1)
				<h3>ABERTURA DE MESA</h3>
				@else
				<h3>FECHAMENTO DE MESA</h3>
				@endif
				@endif

				@if($item->avaliacao)
				@for($i=1; $i<=5; $i++)
				<i class="ri-star-line @if($item->avaliacao >= $i) text-warning @endif"></i>
				@endfor
				@endif

				@if($item->observacao)
				<p>Observação: <strong>{{ $item->observacao }}</strong></p>
				@endif
				
				<p>Horário: <strong>{{ __data_pt($item->created_at) }}</strong></p>
			</div>

			<div class="card-footer">
				<input type="hidden" value="{{ $item->id }}" id="item_id">
				<button type="button" class="btn btn-dark w-100 btn-set-status">Sinalizar</button>
			</div>
		</div>
	</div>
	@endforeach
</div>
