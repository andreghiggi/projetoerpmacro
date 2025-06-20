@foreach($item->itensDimensao as $key => $i)
<tr class="tr_{{ $key }}">
	<td>{{ __moeda($i->valor_unitario_m2) }}</td>
	<td>{{ $i->largura }}</td>
	<td>{{ $i->altura }}</td>
	<td>{{ $i->quantidade }}</td>
	<td>{{ $i->m2_total }}</td>
	<td>{{ $i->espessura }}</td>
	<td class="sub_total">{{ __moeda($i->sub_total) }}</td>
	<td>{{ $i->observacao }}</td>
	<td>
		<button data-key="{{ $key }}" type="button" class="btn btn-danger btn-remove-tr-dimensao btn-sm">
			<i class="ri-delete-bin-line"></i>
		</button>
	</td>
</tr>
@endforeach