<div class="row mt-3">
	<div class="col-md-6">
		<h4>{{ $item->nome }}</h4>
	</div>
	<div class="col-md-6">
		<h4 class="text-end valor-produto">R$ 0,00</h4>
	</div>
	<hr>

	@if(sizeof($item->adicionaisAtivos) > 0)
	<h6>ADICIONAIS</h6>

	@foreach($categoriasAdicional as $c)
	<div class="card">
		<div class="card-body">
			<div class="adicional_categoria_{{ $c->id }}">
				<input type="hidden" value="{{ $c->minimo_escolha }}" class="minimo_escolha">
				<input type="hidden" value="{{ $c->nome }}" class="categoria_nome">
				<h5>
					{{ $c->nome }}
				</h5>

				<div class="opcoes">
					<div class="row">

						@foreach($c->adicionais as $a)
						@if(in_array($a->id, $item->adicionaisAtivos->pluck(['adicional_id'])->toArray()))
						<div class="col-md-3">
							<input type="checkbox" class="check-adicional check-adicional-{{$a->id}}" categoria-adicional-id="{{ $c->id }}" adicional-id="{{ $a->id }}" adicional-valor="{{ $a->valor }}">
							{{ $a->nome }} 
							@if($a->valor > 0)
							R$ {{ __moeda($a->valor) }}
							@else
							Gratis
							@endif
						</div>
						@endif
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
	@endforeach
	@endif

</div>