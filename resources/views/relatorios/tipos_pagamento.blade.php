@extends('relatorios.default')
@section('content')

<p>Total de registros <strong>{{ sizeof($data) }}</strong></p>

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>

			<th>Tipo de pagamento</th>
			<th>Valor</th>
			
		</tr>
	</thead>
	<tbody>
		@php
		$soma = 0;
		@endphp

		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			
			<td>{{ App\Models\Nfce::getTipoPagamento($key) }}</td>
			<td>{{ __moeda($item) }}</td>

			@php
			$soma += (float)$item;
			@endphp
		</tr>
		@endforeach
	</tbody>
</table>

@if(sizeof($data) > 1)
<span>Total: <strong style="color: blue">{{ __moeda($soma) }}</strong></span>
@endif
@endsection
