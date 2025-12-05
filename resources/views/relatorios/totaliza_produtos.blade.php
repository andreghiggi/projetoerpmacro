@extends('relatorios.default')
@section('content')

<p>Total de registros <strong>{{ sizeof($data) }}</strong></p>
@if($local)
<h4>{{ $local->nome }}</h4>
@endif
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>

			<th>Produto</th>
			<th>Vl. venda</th>
			<th>Vl. compra</th>
			<th>Dt. cadastro</th>
			<th>Estoque total</th>
			
		</tr>
	</thead>
	<tbody>
		@php
		$somaEstoque = 0;
		$somaVenda = 0;
		$somaCompra = 0;
		@endphp

		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			
			<td>{{ $item->nome }}</td>
			<td>{{ __moeda($item->valor_unitario) }}</td>
			<td>{{ __moeda($item->valor_compra) }}</td>
			<td>{{ __data_pt($item->created_at) }}</td>
			<td>{{ $item->estoqueTotal($local_id) }}</td>

			@php
			$somaEstoque += (float)$item->estoqueTotal($local_id);
			$somaVenda += (float)$item->estoqueTotal($local_id) * $item->valor_unitario;
			$somaCompra += (float)$item->estoqueTotal($local_id) * $item->valor_compra;
			@endphp
		</tr>
		@endforeach
	</tbody>
</table>

<span>Total de itens no estoque: <strong style="color: blue">{{ $somaEstoque }}</strong></span>
<span style="margin-left: 25px;">Total valor de venda: <strong>R$ {{ __moeda($somaVenda) }}</strong></span>
<span style="margin-left: 25px;">Total valor de compra: <strong>R$ {{ __moeda($somaCompra) }}</strong></span>
@endsection
