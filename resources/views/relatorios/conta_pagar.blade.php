@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
	<thead>
		<tr>
			<th>Fornecedor</th>
			<th>Valor</th>
			<th>Valor Pago</th>
			<th>Data Vencimento</th>
			<th>Data Pagamento</th>
			<th>Estado</th>
			@if(__countLocalAtivo() > 1)
			<th>Local</th>
			@endif
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key => $item)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td style="text-align: left">{{ $item->fornecedor ? $item->fornecedor->razao_social : '--' }}</td>
			<td style="text-align: left">{{ __moeda($item->valor_integral) }}</td>
			<td style="text-align: left">{{ $item->status ? __moeda($item->valor_pago) : '--' }}</td>
			<td style="text-align: left">{{ __data_pt($item->data_vencimento, 0) }}</td>
			<td style="text-align: left">{{ $item->status ? __data_pt($item->data_pagamento, 0) : '--' }}</td>
			<td style="text-align: left">
				@if($item->status == 0)

				@if(strtotime($item->data_vencimento) < strtotime(date('Y-m-d')))
				Em atraso
				@else
				Pendente
				@endif

				@else
				Quitado
				@endif
			</td>
			@if(__countLocalAtivo() > 1)
			<td style="text-align: left" class="text-danger">{{ $item->localizacao->descricao }}</td>
			@endif
		</tr>

		@if($item->status == 1)
		<tr class="@if($key%2 == 0) pure-table-odd @endif">
			<td style="text-align: left">
				Desconto <strong style="color: red">R$ {{ __moeda($item->desconto) }}</strong>
			</td>
			<td style="text-align: left">
				Acréscimo <strong style="color: red">{{ __moeda($item->acrescimo) }}</strong>
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
<h4>Total: R$ {{ __moeda($data->sum('valor_integral')) }}</h4>
@endsection
