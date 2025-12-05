@foreach($data as $item)
<tr>
	<td>{{ $item->empresa->info }}</td>
	<td>
		@if($item->empresa->status)
		<i class="ri-checkbox-circle-fill text-success"></i>
		@else
		<i class="ri-close-circle-fill text-danger"></i>
		@endif
	</td>

	<td>
		@if($contador->empresa_selecionada != $item->empresa_id)
		<a class="btn btn-success btn-sm" href="{{ route('contador.set-empresa', [$item->empresa->id]) }}" title="Selecionar empresa e visualizar os dados">
			<i class="ri-check-line"></i>
		</a>
		@endif
	</td>
</tr>
@endforeach