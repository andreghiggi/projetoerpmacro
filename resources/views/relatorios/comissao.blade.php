@extends('relatorios.default')
@section('content')

@section('css')
<style type="text/css">
    .circulo {
        background: lightblue;
        border-radius: 50%;
        width: 100px;
        height: 100px;
    }
</style>
@endsection

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th>Funcionário</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Valor da venda</th>
            <th>Valor da comissão</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item->funcionario->nome }}</td>
            <td>{{ $item->tabela == 'nfce' ? 'PDV' : 'Pedido' }}</td>
            <td>
                @if($item->status)
                <div class="circulo" style="background: green !important"></div>
                @else
                <div class="circulo"></div>
                @endif
            </td>
            <td>{{ __moeda($item->valor_venda) }}</td>
            <td>{{ __moeda($item->valor) }}</td>
            <td>{{ __data_pt($item->created_at) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="pure-table-sum">
            <td colspan="3">Soma</td>
            <td colspan="">{{ __moeda($data->sum('valor_venda')) }}</td>
            <td colspan="">{{ __moeda($data->sum('valor')) }}</td>
            <td colspan=""></td>
        </tr>
    </tfoot>
</table>

@endsection
