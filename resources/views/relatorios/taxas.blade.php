@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Total</th>
            <th>% Taxa</th>
            <th>Data</th>
            <th>Tipo Pagamento</th>
            <th>Tipo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            {{-- <td>
                {{ $item['id'] }}
            </td> --}}
            <td>
                {{ $item['cliente'] }}
            </td>
            <td>
                {{ $item['total'] }}
            </td>
            <td>
                {{ __moeda($item['taxa_perc']) }}
            </td>
            <td>
                {{ $item['data'] }}
            </td>
            <td>
                {{ $item['tipo_pagamento'] }}
            </td>
            <td>
                {{ $item['tipo'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{-- <h4>Total de Vendas: R$ {{ $item->sum(['total']) }}</h4> --}}
@endsection
