@extends('relatorios.default')
@section('content')

<p>Período: 
	<strong>{{ $start_date ? __data_pt($start_date, 0) : 'não definido' }}</strong>
	até
	<strong>{{ $end_date ? __data_pt($end_date, 0) : 'não definido' }}</strong>

</p>
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>#</th>
			<th>Produto</th>
			<th>Total em vendas</th>
			<th>Total em compras</th>
			<th>Lucro</th>
			
		</tr>
	</thead>
	<tbody>
		@php $soma = 0 @endphp
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item['numero_sequencial'] }}</td>
			<td>{{ $item['produto_nome'] }}</td>
			<td>{{ __moeda($item['total_vendas']) }}</td>
			<td>{{ __moeda($item['total_compras']) }}</td>
			<td>{{ __moeda($item['total_vendas'] - $item['total_compras']) }}</td>
			
		</tr>

		@endforeach
	</tbody>
</table>

@endsection
