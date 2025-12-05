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

@if($estoque_minimo == 1)
<p style="color: red">Relatório para estoque mínimo</p><br>
@endif

@if($localizacao != null)
<h5>Local: <strong>{{ $localizacao->descricao }}</strong></h5>
@endif
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th style="width: 300px">Produto</th>
            <th>Categoria</th>
            <th>Valor de compra</th>
            <th>Valor de venda</th>
            <th>Valor de lucro</th>

            <th>Quantidade</th>
            <th>Estoque mínimo</th>
            <th>Data de cadastro</th>
        </tr>
    </thead>
    <tbody>
        @php
        $somaVenda = 0;
        $somaCompra = 0;
        $somaQtd = 0;
        @endphp
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item['produto'] }}</td>
            <td>{{ $item['categoria'] }}</td>
            <td>{{ __moeda($item['valor_compra']) }}</td>
            <td>{{ __moeda($item['valor_venda']) }}</td>
            <td>{{ __moeda((float)$item['valor_venda'] - (float)$item['valor_compra']) }}</td>
            <td>
                {{ number_format((float)$item['quantidade'], __casas_decimais_quantidade(), ',' , '.') }}
            </td>
            <td>{{ $item['estoque_minimo'] }}</td>
            <td>{{ $item['data_cadastro'] }}</td>
        </tr>

        @php
        $somaVenda += (float)$item['valor_venda']*(float)$item['quantidade'];
        $somaCompra += (float)$item['valor_compra']*(float)$item['quantidade'];
        $somaQtd += (float)$item['quantidade'];
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Soma</td>
            <td>{{ __moeda($somaCompra) }}</td>
            <td>{{ __moeda($somaVenda) }}</td>
            <td>{{ $somaQtd }}</td>
        </tr>
    </tfoot>
    
</table>

@endsection
