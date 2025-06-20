@foreach($horarios as $i)
<tr>
	<td>{{ $i['funcionario_nome'] }}</td>
	<td>{{ $i['inicio'] }} - {{ $i['fim'] }}</td>
	<td>{{ __moeda($i['total']) }}</td>
	<td>
		<button onclick="escolheHorario('{{ json_encode($i) }}')" type="button" class="btn btn-sm btn-dark">
			<i class="ri-check-line"></i>
			escolher
		</button>
	</td>
</tr>
@endforeach