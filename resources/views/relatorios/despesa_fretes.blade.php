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
            <th>Tipo de despesa</th>
            <th>Frete</th>
            <th>Fornecedor</th>
            <th>Valor</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item->tipoDespesaFrete->nome }}</td>
            <td>#{{ $item->frete->numero_sequencial }}</td>
            <td>{{ $item->fornecedor ? $item->fornecedor->info : '' }}</td>
            <td>{{ __moeda($item->valor) }}</td>
            <td>{{ $item->observacao }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Soma</td>
            <td>{{ __moeda($data->sum('valor')) }}</td>
        </tr>
    </tfoot>
    
</table>

@endsection
