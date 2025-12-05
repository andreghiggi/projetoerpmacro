@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th class="text-left">Cliente</th>
			<th class="text-left">Qtd. vendas</th>
			<th class="text-left">Total vendas</th>
			<th class="text-left">Percentual</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $item)
		<tr>
			<td class="text-left">{{ $item['nome'] }}</td>
			<td class="text-left">{{ $item['count'] }}</td>
			<td class="text-left">{{ __moeda($item['total']) }}</td>
			<td class="text-left">{{ $item['percentual'] }}</td>
		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<th colspan="2" class="text-left">Soma</th>
			<th colspan="2" class="text-left">R$ {{ __moeda($soma) }}</th>
		</tr>
	</tfoot>
</table>

@endsection
