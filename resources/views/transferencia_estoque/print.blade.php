@extends('relatorios.default')
@section('content')

<h4>
    Local de saída: <strong>{{ $item->local_saida->descricao }}</strong> - Local de entrada: <strong>{{ $item->local_entrada->descricao }}</strong>
</h4>
<h4 style="margin-top: -20px">Data: <strong>{{ __data_pt($item->created_at) }}</strong></h4>
<h4 style="margin-top: -20px">Total de itens: <strong>{{ sizeof($item->itens) }}</strong></h4>
@if($item->observacao)
<h4 style="margin-top: -20px">Observação: <strong>{{ $item->observacao }}</strong></h4>
@endif
<h5 style="margin-top: -20px">#{{ $item->codigo_transacao }}</h5>
<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th style="width: 400px">Produto</th>
            <th style="width: 100px">Quantidade</th>
            <th style="width: 200px">Observação</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach($item->itens as $key => $p)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $p->produto->nome }}</td>
            <td>{{ number_format($p->quantidade, 2) }}</td>
            <td>{{ $p->observacao }}</td>

        </tr>
        @endforeach
    </tbody>
</table>

@endsection
