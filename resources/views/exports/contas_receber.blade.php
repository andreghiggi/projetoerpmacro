<table>
	<thead>
		<tr>
			<th style="width: 300px">Cliente</th>
			<th style="width: 300px">Descrição</th>
			<th style="width: 300px">Valor integral</th>
			<th style="width: 300px">Valor recebido</th>
			<th style="width: 120px">Data de cadastro</th>
			<th style="width: 120px">Data de vencimento</th>
			<th style="width: 120px">Data de recebimento</th>
			<th style="width: 120px">Estado</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr>
			<td>{{ $item->cliente ? $item->cliente->info : '--' }}</td>
			<td>{{ $item->descricao }}</td>
			<td>{{ __moeda($item->valor_integral) }}</td>
			<td>{{ __moeda($item->valor_recebido) }}</td>
			<td>{{ __data_pt($item->created_at, 0) }}</td>
			<td>
				{{ __data_pt($item->data_vencimento, 0) }}
			</td>
			<td>{{ $item->status ? __data_pt($item->data_recebimento, false) : '--' }}</td>
			<td>{{ $item->status ? 'Recebido' : 'Pendente' }}</td>


		</tr>
		@endforeach
	</tbody>
</table>
