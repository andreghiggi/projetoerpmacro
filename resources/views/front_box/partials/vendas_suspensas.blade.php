@forelse($data as $item)
<tr>
	<td>{{ $item->cliente ? $item->cliente->razao_social : ($item->cliente_nome != "" ? $item->cliente_nome : "--") }}</td>
	<td>{{ __moeda($item->total) }}</td>
	<td>{{ __data_pt($item->created_at) }}</td>
	<td>{{ $item->user ? $item->user->name : '--' }}</td>
	<td>
		<form action="{{ route('frontbox.destroy-suspensa', $item->id) }}" method="get" id="form-{{$item->id}}">

			<a class="btn btn-sm btn-dark" href="{{ route('frontbox.create', ['venda_suspensa='.$item->id]) }}">
				<i class="ri-price-tag-3-fill"></i>
				Finalizar
			</a>

			<button type="button" class="btn btn-delete btn-sm btn-danger">
				<i class="ri-delete-bin-line"></i>
				Remover
			</button>

		</form>
	</td>
</tr>
@empty
<tr>
	<td class="text-center" colspan="5">Nenhuma venda suspensa!</td>
</tr>
@endforelse