@forelse($data as $item)
<div class="card-suspensa">

	<div class="cs-info">
		<div class="cs-cliente"><i class="ri-user-line"></i> {{ $item->cliente ? $item->cliente->razao_social : ($item->cliente_nome != "" ? $item->cliente_nome : "--") }}</div>
		<div class="cs-data"><i class="ri-calendar-line"></i> {{ __data_pt($item->created_at) }}</div>
	</div>

	<div class="cs-total">
		<span>Total:</span>
		<strong>R$ {{ __moeda($item->total) }}</strong>
	</div>

	<div class="cs-acoes">
		<a class="btn btn-dark btn-sm cs-btn-finalizar" href="{{ route('pdv-mobo.index', ['venda_suspensa='.$item->id]) }}">
			<i class="ri-price-tag-3-fill"></i> Finalizar
		</a>

		<button class="btn btn-danger btn-sm cs-btn-remover" data-id="{{ $item->id }}">
			<i class="ri-delete-bin-line"></i> Remover
		</button>
	</div>

</div>
@empty

<label class="text-center w-100 m-2">Nenhuma venda suspensa.</label>

@endforelse

