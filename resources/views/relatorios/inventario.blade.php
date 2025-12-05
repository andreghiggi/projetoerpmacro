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
<p style="color: red">Local: <strong>{{ $local->info }}</strong></p><br>
@else
<p style="color: red">Local: <strong>{{ $empresa->info }}</strong></p><br>
@endif
<p style="color: red">Total de registros: <strong>{{ sizeof($data)}}</strong></p><br>

<label style="float: right; margin-top: -20px;">Livro: <strong>{{ $livro }}</strong></label>

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th style="text-align: left;">NCM</th>
            <th style="width: 300px; text-align: left;">Produto</th>
            <th style="text-align: left;">Unidade</th>
            <th style="text-align: left;">Quantidade</th>
            <th style="text-align: left;">Custo unitário</th>
            <th style="text-align: left;">Custo total</th>
            <!-- <th style="text-align: left;">Data de cadastro</th> -->
        </tr>
    </thead>
    @php $soma = 0; @endphp
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td style="text-align: left;">{{ $item['ncm'] }}</td>
            <td style="text-align: left;">{{ $item['nome'] }}</td>
            <td style="text-align: left;">{{ $item['unidade'] }}</td>
            <td style="text-align: left;">{{ $item['quantidade'] ? $item['quantidade'] : '--' }}</td>

            <td style="text-align: left;">{{ __moeda($item['custo_unuitario']) }}</td>
            <td style="text-align: left;">{{ __moeda($item['sub_total']) }}</td>
            <!-- <td style="text-align: left;">{{ __data_pt($item['created_at']) }}</td> -->

        </tr>
        @php $soma += $item['sub_total']; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr class="pure-table-odd">
            <td colspan="5" style="text-align: left;">Soma</td>
            <td colspan="2" style="text-align: left;">{{ __moeda($soma) }}</td>
        </tr>
    </tfoot>
    
</table>

@endsection
