<div class="row">
	<div class="col-md-2">
		{!!Form::tel('valor_unitario_item', 'Valor unitário')
		->attrs(['class' => 'moeda'])
		->value(__moeda($itemPedido->valor_unitario))
		!!}

	</div>
	<div class="col-md-6">
		{!!Form::text('observacao_item', 'Observação')
		->value($itemPedido->observacao)
		!!}
	</div>

	<input type="hidden" id="valor_original" value="{{ $produto->valor_unitario }}">
</div>
@if(sizeof($produto->adicionaisAtivos) > 0)
<hr>

<div class="row">
	<div class="card">
		<div class="card-body">
			<h5>ADICIONAIS</h5>
			@foreach($categoriasAdicional as $c)
			<div class="row" style="margin-bottom: 20px;">
				<h5>
					{{ $c->nome }}
				</h5>

				@foreach($c->adicionais as $a)
				@if(in_array($a->id, $produto->adicionaisAtivos->pluck(['adicional_id'])->toArray()))
				<div class="col-md-3">
					<input style="margin-left: 10px" type="checkbox" class="checkbox_adicional" adicional-id="{{ $a->id }}" adicional-valor="{{ $a->valor }}" @if(in_array($a->id, $itemPedido->adicionais->pluck(['adicional_id'])->toArray())) checked @endif>
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
			@endforeach


		</div>
	</div>
</div>
@endif

@if($produto->categoria && $produto->categoria->tipo_pizza == 1)

<div class="row">
	<div class="card">
		<div class="card-body">
			<h5>TAMANHO/SABORES</h5>

			<div class="row">
				<div class="col-md-4">

					<label>Tamanho</label>
					<select id="inp-tamanho_id" class="form-control form-select">
						<option value="">Selecione</option>
						@foreach($tamanhosDePizza as $t)
						<option @if($itemPedido->tamanho_id == $t->id) selected @endif max-sabores="{{ $t->maximo_sabores }}" value="{{ $t->id }}">{{ $t->info }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="row pizzas m-2 mt-4">
			</div>

		</div>
	</div>
</div>
@endif