@extends('relatorios.default')
@section('content')
<h4>Período {{ __data_pt($start_date, 0) }} - {{ __data_pt($end_date, 0) }}</h4>
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Cliente</th>
			<th>Valor total</th>
			<th>Valor de estadia</th>
			<th>Valor outros</th>
			<th>Data de criação</th>
			<th>Qtd. hóspedes</th>
			<th>Estado</th>
			
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item->cliente->razao_social }}</td>

			<td>{{ __moeda($item->valor_total) }}</td>
			<td>{{ __moeda($item->valor_estadia) }}</td>
			<td>{{ __moeda($item->valor_total-$item->valor_estadia) }}</td>
			<td>{{ __data_pt($item->created_at) }}</td>
			<td>{{ $item->total_hospedes }}</td>
			<td>{{ $item->estado }}</td>
			
		</tr>


		<tr>
			<td style="font-weight: bold;">Data checkin</td>
			<td>{{ __data_pt($item->data_checkin, 0) }}</td>
			<td style="font-weight: bold;">Data checkout</td>
			<td>{{ __data_pt($item->data_checkout, 0) }}</td>
			<td style="font-weight: bold;">Acomodação</td>
			<td colspan="2">{{ $item->acomodacao->nome }}</td>

		</tr>

		@endforeach
	</tbody>
</table>
<h4>Total: R$ {{ __moeda($data->sum('valor_total')) }}</h4>
@endsection
