@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Cliente</th>
			<th>Valor</th>
            <th>Data Vencimento</th>
			<th>Estado</th>
			<th>Parcela</th>
			<th>Nº Pedido/ Nº NFe</th>
			@if(__countLocalAtivo() > 1)
			<th>Local</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td>{{ $item->cliente ? $item->cliente->razao_social : '' }}</td>
			<td>{{ __moeda($item->valor_integral) }}</td>
			<td>{{ __data_pt($item->data_vencimento, 0) }}</td>
            <td>
                @if($item->status == 0)
                Pendente
                @else
                Recebido
                @endif
            </td>
            @if($item->nfe)
            <td>{{ $item->contaFatura() }}</td>
            <td>
            	{{ $item->nfe->numero_sequencial }}
            	@if($item->nfe->estado == 'aprovado')
            	/{{ $item->nfe->numero }}
            	@endif
            </td>
            @else
            <td>--</td>
            <td>--</td>
            @endif
            @if(__countLocalAtivo() > 1)
			<td class="text-danger">{{ $item->localizacao->descricao }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>
<h4>Total a receber: R$ {{ __moeda($data->sum('valor_integral')) }}</h4>
@endsection
