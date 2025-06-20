@extends('relatorios.default')
@section('content')
<style type="text/css">
	.b-top{
		border-top: 1px solid #000; 
	}
	.b-bottom{
		border-bottom: 1px solid #000; 
	}
</style>
@if($start_date && $end_date)
<p>Periodo: {{ __data_pt($start_date, 0) }} - {{ __data_pt($end_date, 0) }}</p>
@endif

@php
$somaLucro = 0;
$somaVenda = 0;
$somaCompra = 0;
$somaQuantidade = 0;
@endphp

<table class="table-sm table-borderless"
style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
@foreach($data as $i)
@if(sizeof($i['itens']) > 0)
<tr>
	<td colspan="10"></td>
</tr>
<tr>
	<td colspan="10"></td>
</tr>
<tr>
	<td>
		Data: <strong style="color: #0BB7AF">{{ __data_pt($i['data'], 0) }}</strong>
	</td>
</tr>
<tr>
	<td class="" style="width: 485px;">
		Produto
	</td>
	<td class="" style="width: 110px;">
		Quantidade
	</td>
	<td class="" style="width: 110px;">
		Valor venda
	</td>
	<td class="" style="width: 110px;">
		Valor venda média
	</td>
	<td class="" style="width: 110px;">
		Sub total
	</td>
	<td class="" style="width: 110px;">
		Lucro
	</td>
</tr>
@foreach($i['itens'] as $d)
<tr>
	<th class="b-top">{{ $d['produto']->nome}} {{$d['produto']->referencia }}</th>
	<th class="b-top">{{ __moeda($d['quantidade']) }}</th>
	<th class="b-top">
		{{ __moeda($d['valor'])}}
	</th>
	<th class="b-top">{{ __moeda($d['media']) }}</th>
	<th class="b-top">{{ __moeda($d['subtotal']) }}</th>
	<th class="b-top">
		{{ __moeda($d['subtotal'] - ($d['quantidade']*$d['produto']->valor_compra))}}
	</th>
</tr>
@php
$somaQuantidade += ($d['quantidade']);
$somaVenda += $d['media'] * $d['quantidade'];
$somaCompra += $d['produto']->valor_compra * $d['quantidade'];
$somaLucro += $d['subtotal'] - ($d['quantidade']*$d['produto']->valor_compra);
@endphp
@endforeach
@endif
@endforeach
</table>

@endsection
