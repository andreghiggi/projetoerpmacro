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
			<td style="text-align: left">{{ $item->cliente ? $item->cliente->razao_social : '' }}</td>
			<td style="text-align: left">{{ __moeda($item->valor_integral) }}</td>
			<td style="text-align: left">{{ __data_pt($item->data_vencimento, 0) }}</td>
			<td style="text-align: left">
				@if($item->status == 0)

				@if(strtotime($item->data_vencimento) < strtotime(date('Y-m-d')))
				Em atraso
				@else
				Pendente
				@endif

				@else
				Recebido
				@endif
			</td>
			@if($item->nfe)
			<td style="text-align: left">{{ $item->contaFatura() }}</td>
			<td style="text-align: left">
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
			<td style="text-align: left" class="text-danger">{{ $item->localizacao->descricao }}</td>
			@endif
		</tr>

		@if($item->status == 1)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td style="text-align: left">
				Valor recebido <strong style="color: red">R$ {{ __moeda($item->valor_recebido) }}</strong>
			</td>
			<td style="text-align: left">
				Recebimento <strong style="color: red">{{ __data_pt($item->data_recebimento, 0) }}</strong>
			</td>
			<td style="text-align: left" colspan="4">
				@if($item->contaEmpresa)
				Conta <strong style="color: red">{{ $item->contaEmpresa->nome }}</strong>
				@endif
			</td>
		</tr>
		@endif

		@endforeach
	</tbody>
</table>
<h4>Total a receber: R$ {{ __moeda($data->sum('valor_integral')) }}</h4>
@endsection
