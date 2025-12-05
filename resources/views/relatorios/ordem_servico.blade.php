@extends('relatorios.default')
@section('content')

<style type="text/css">
    .circulo {
        background: lightblue;
        border-radius: 50%;
        width: 100px;
        height: 100px;
    }
    tbody td{
        text-align: left !important;
    }
    th{
        text-align: left !important;
    }
</style>


<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Valor</th>
            <th>Data de ínicio</th>
            <th>Data de entrega</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item->codigo_sequencial }}</td>
            <td>{{ $item->cliente->info }}</td>
            <td>{{ __moeda($item->valor) }}</td>
            <td>{{ __data_pt($item->data_inicio, 0) }}</td>
            <td>{{ $item->data_entrega ? __data_pt($item->data_entrega, 0) : '--' }}</td>
            <td>
                @if($item->estado == 'pd')
                PENDENTE
                @elseif($item->estado == 'ap')
                APROVADO
                @elseif($item->estado == 'rp')
                REPROVADO
                @elseif($item->estado == 'fz')
                FINALIZADO
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Soma</td>
            <td style="text-align: left;">{{ __moeda($data->sum('valor')) }}</td>
        </tr>
    </tfoot>
    
</table>

@endsection
