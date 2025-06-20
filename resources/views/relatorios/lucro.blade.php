@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Cliente</th>
			<th>Valor da venda</th>
			<th>Valor de custo</th>
            <th>Data</th>
			<th>Lucro</th>
			@if(__countLocalAtivo() > 1)
            <th>Local</th>
            @endif
		</tr>
	</thead>
	<tbody>
		@php $soma = 0 @endphp
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item['cliente'] }}</td>
			<td>{{ __moeda($item['valor_venda']) }}</td>
			<td>{{ __moeda($item['valor_custo']) }}</td>
			<td>{{ $item['data'] }}</td>
			<td>{{ __moeda($item['valor_venda']-$item['valor_custo']) }}</td>
			@if(__countLocalAtivo() > 1)
            <td class="text-danger">{{ $item['localizacao']->descricao }}</td>
            @endif
		</tr>
		@php $soma += $item['valor_venda']-$item['valor_custo'] @endphp

		@endforeach
	</tbody>
</table>
<h4>Total lucro: R$ {{ __moeda($soma) }}</h4>
@endsection
