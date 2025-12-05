@extends('relatorios.default')
@section('content')
<h4>Período {{ __data_pt($start_date, 0) }} - {{ __data_pt($end_date, 0) }}</h4>
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Nome</th>
			<th>Número</th>
			<th>Categoria</th>
			<th>Capacidade</th>
			<th>Valor</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item->nome }}</td>
			<td>{{ $item->numero }}</td>
			<td>{{ $item->categoria ? $item->categoria->nome : '' }}</td>
			<td>{{ $item->capacidade }}</td>
			<td>{{ __moeda($item->valor_diaria) }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

@endsection
