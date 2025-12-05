@foreach($data as $l)
<tr>
	<td>{{ $l->numero_sequencial }}</td>
	<td>{{ $l->cliente ? $l->cliente->info : 'Consumidor final' }}</td>
	<td>{{ __moeda($l->total) }}</td>
	<td>{{ __data_pt($l->created_at) }}</td>
	<td>{{ $l->estado == 'aprovado' || $l->estado == 'cancelado' ? $l->numero : '--' }}</td>
	<td>
		@if($l->estado == 'aprovado')
		<span class="btn btn-success text-white btn-sm">APROVADO</span>
		@elseif($l->estado == 'cancelado')
		<span class="btn btn-danger text-white btn-sm">CANCELADO</span>
		@elseif($l->estado == 'rejeitado')
		<span class="btn btn-warning text-white btn-sm">REJEITADO</span>
		@else
		<span class="btn btn-info text-white btn-sm">NOVO</span>
		@endif
	</td>
	<td>
		@isset($l->troco)
		PDV
		@else
		PEDIDO
		@endif
	</td>
	<td>
		<form action="{{ route('trocas.create') }}">
			<input type="hidden" name="tipo" value="{{ isset($l->troco) ? 'nfce' : 'nfe' }}">
			<input type="hidden" name="id" value="{{ $l->id }}">
			<button class="btn btn-sm btn-primary">
				<i class="ri-arrow-right-circle-fill"></i>
			</button>
		</form>
	</td>
</tr>
@endforeach