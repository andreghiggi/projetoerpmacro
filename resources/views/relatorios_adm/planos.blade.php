@extends('relatorios_adm.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Razão social</th>
			<th>CPF/CNPJ</th>
			<th>Telefone</th>
			<th>Plano</th>
			<th>Vencimento</th>
			<th>Valor</th>
			
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item->empresa->nome }}</td>
			<td>{{ $item->empresa->cpf_cnpj }}</td>
			<td>{{ $item->empresa->celular }}</td>
			<td>{{ $item->plano->nome }}</td>
			<td>{{ __data_pt($item->data_expiracao, 0) }}</td>
			<td>{{ __moeda($item->valor) }}</td>
			
		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5">Soma</td>
			<td>{{ __moeda($data->sum('valor')) }}</td>
		</tr>
	</tfoot>
</table>
<h4>Total de registros: <strong style="color: #3B4CA7">{{ sizeof($data) }}</strong></h4>

@endsection
