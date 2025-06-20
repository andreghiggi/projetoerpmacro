@extends('relatorios_adm.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Usuário</th>
			<th>Empresa</th>
			<th>Data</th>
			<th>IP</th>
			
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item->usuario->name }}</td>
			<td>{{ $item->usuario->empresa ? $item->usuario->empresa->empresa->nome : '--' }}</td>
			<td>{{ __data_pt($item->created_at) }}</td>
			<td>{{ $item->ip }}</td>

			
		</tr>
		@endforeach
	</tbody>
</table>
<h4>Total de registros: <strong style="color: #3B4CA7">{{ sizeof($data) }}</strong></h4>


@endsection
