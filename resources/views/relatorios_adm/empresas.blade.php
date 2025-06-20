@extends('relatorios_adm.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Razão social</th>
			<th>Nome fantasia</th>
			<th>CPF/CNPJ</th>
			<th>IE</th>
			<th>Plano</th>
			<th>Stauts</th>
			
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item->nome }}</td>
			<td>{{ $item->nome_fantasia }}</td>
			<td>{{ $item->cpf_cnpj }}</td>
			<td>{{ $item->ie }}</td>
			<td>{{ $item->plano ? $item->plano->plano->nome : '--' }}</td>
			<td>{{ $item->status ? 'ATIVA' : 'DESATIVADA' }}</td>
			
		</tr>
		@endforeach
	</tbody>
</table>
<h4>Total de registros: <strong style="color: #3B4CA7">{{ sizeof($data) }}</strong></h4>


@endsection
