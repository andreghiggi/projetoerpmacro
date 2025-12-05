@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Razão social</th>
			<th>Nome fantasia</th>
			<th>CPF/CNPJ</th>
			<th>IE</th>
			<th>Endereço</th>
			<th>Cidade</th>
			<th>Data de cadastro</th>
			<th>Data de nascimento</th>
			@if($tipo != '')
			<th>Total vendido</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item->razao_social }}</td>
			<td>{{ $item->nome_fantasia }}</td>
			<td>{{ $item->cpf_cnpj }}</td>
			<td>{{ $item->ie }}</td>
			<td>{{ $item->endereco }}</td>
			<td>{{ $item->cidade ? $item->cidade->info : '' }}</td>
			<td>{{ __data_pt($item->created_at) }}</td>
			<td>{{ $item->data_nascimento ? __data_pt($item->data_nascimento, 0) : '--' }}</td>
			@if($tipo != '')
			<td>{{ __moeda($item->total) }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>
@if($tipo != '')
<h4>Total: R$ {{ __moeda($data->sum('total')) }}</h4>
@endif

@endsection
