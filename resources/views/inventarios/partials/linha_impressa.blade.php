@foreach($item->itensImpresso as $p)
<tr data-id="{{ $p->id }}">
	<td>{{ $p->produto->nome }}</td>
	<td>{{ $p->produto->categoria ? $p->produto->categoria->nome : '' }}</td>
	<td>{{ $p->produto->codigo_barras ?? '--' }}</td>
	<td>{{ $p->produto->referencia ?? '--' }}</td>
	<td>
		<button class="btn btn-danger btn-delete-item">
			<i class="ri-delete-bin-line"></i>
		</button>
	</td>
</tr>
@endforeach