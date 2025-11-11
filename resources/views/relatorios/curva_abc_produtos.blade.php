@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th class="text-left">Produto</th>
			<th class="text-left">Variação</th>
			<th class="text-left">Qtd. vendas</th>
			<th class="text-left">Valor Total</th>
			<th class="text-left">Percentual</th>
			<th class="text-left">Categoria</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $item)
		<tr>
			<td class="text-left">{{ $item['produto_nome'] }}</td>
			<td class="text-left">{{ $item['produto_variacao_nome'] }}</td>
			<td class="text-left">{{ $item['count'] }}</td>
			<td class="text-left">{{ __moeda($item['valor_total']) }}</td>
			<td class="text-left">{{ $item['percentual'] }}</td>
			<td class="text-left">{{ $item['categoria'] }}</td>
		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<th colspan="3" class="text-left">Soma</th>
			<th colspan="4" class="text-left">R$ {{ __moeda($soma) }}</th>
		</tr>
	</tfoot>
</table>

@endsection
