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

@if($local)
<p style="color: red">Local: <strong>{{ $local->nome }}</strong></p><br>
@endif
<p style="color: red">Total de registros: <strong>{{ sizeof($data)}}</strong></p><br>

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th style="width: 300px">Produto</th>
            <th>Categoria</th>
            <th>Custo médio</th>
            <th>Valor de venda</th>

            <th>Quantidade</th>
            <th>Data de cadastro</th>
            <th>NCM</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item['nome'] }}</td>
            <td>{{ $item['categoria_nome'] }}</td>
            <td>{{ __moeda($item['custo_medio']) }}</td>
            <td>{{ __moeda($item['valor_unitario']) }}</td>
            <td>{{ $item['quantidade'] ? $item['quantidade'] : '--' }}</td>
            <td>{{ __data_pt($item['created_at']) }}</td>
            <td>{{ $item['ncm'] }}</td>

        </tr>
        @endforeach
    </tbody>
    
</table>

@endsection
