@extends('relatorios.default')
@section('content')
<h5>Vendedor <strong style="color: #49526B">{{ $funcionario->nome }}</strong></h5>
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Data</th>
            <th>Valor</th>
            @if(__countLocalAtivo() > 1)
            <th>Local</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>
                {{ $item['id'] }}
            </td>
            <td>
                {{ $item['cliente'] }}
            </td>
            <td>
                {{ __data_pt($item['data']) }}
            </td>
            <td>
                {{ __moeda($item['total']) }}
            </td>
            @if(__countLocalAtivo() > 1)
            <td class="text-danger">{{ $item['localizacao']->descricao }}</td>
            @endif

        @php $total += $item['total']; @endphp

        </tr>
        @endforeach
    </tbody>
</table>
<h4>Total de Vendas: <strong style="color: #49526B">R$ {{ __moeda($total) }}</strong></h4> 
@endsection
