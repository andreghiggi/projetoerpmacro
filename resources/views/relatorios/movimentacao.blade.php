@extends('relatorios.default')
@section('content')
<h5>Total de registros: <strong>{{ sizeof($data) }}</strong></h5>
@if($start_date)
<p>Data inicial de filtro: <strong>{{ __data_pt($start_date , 0) }}</strong></p>
@endif
@if($end_date)
<p>Data final de filtro: <strong>{{ __data_pt($end_date , 0) }}</strong></p>
@endif

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th style="width: 250px;">Produto</th>
			<th>Qtd. vendida</th>
			<th>Qtd. comprada</th>
			<th>Vl. venda</th>
			<th>Vl. compra</th>
			<th>Sub. venda</th>
			<th>Sub. compra</th>
			@if(__countLocalAtivo() > 1)
			<th>Local</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@php 
		$somaQtdCompra = 0;
		$somaQtdSaida = 0;
		$somaValorCompra = 0;
		$somaValorVenda = 0;
		@endphp
		@foreach($data as $key => $item)

		@php 
		$somaQtdCompra += $item['qtd_compra'];
		$somaQtdSaida += $item['qtd_saida'];
		$somaValorCompra += $item['subtotal_compra'];
		$somaValorVenda += $item['subtotal_venda'];
		@endphp

		@if(sizeof($data) == 0)
		<tr>
			<td colspan="8">Nenhum registro</td>
		</tr>
		@endif

		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item['nome_produto'] }}</td>
			<td>{{ $item['qtd_saida'] }}</td>
			<td>{{ $item['qtd_compra'] }}</td>
			<td>R$ {{ __moeda($item['vl_venda']) }}</td>
			<td>R$ {{ __moeda($item['vl_compra']) }}</td>
			<td>R$ {{ __moeda($item['subtotal_venda']) }}</td>
			<td>R$ {{ __moeda($item['subtotal_compra']) }}</td>
		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<td></td>
			<td><label style="color: #49526B">{{ $somaQtdSaida }}</label></td>
			<td><label style="color: #e8012d">{{ $somaQtdCompra }}</label></td>
			<td colspan="2"></td>
			<td><label style="color: #49526B">R$ {{ __moeda($somaValorVenda) }}</label></td>
			<td><label style="color: #e8012d">R$ {{ __moeda($somaValorCompra) }}</label></td>
		</tr>
	</tfoot>
</table>

@endsection
