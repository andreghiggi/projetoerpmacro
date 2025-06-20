@extends('relatorios.default')
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th>#</th>
            <th>Produto</th>
            <th>Quantidade</th>

        </tr>
    </thead>
    <tbody>
        @php
        $qtd = 0;
        @endphp
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>
                {{ $item['numero_sequencial'] }}
            </td>
            <td>
                {{ $item['produto_nome'] }}
            </td>
            <td>
                {{ $item['quantidade'] }}
            </td>
        </tr>

        @php
        $qtd += $item['quantidade'];
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total de Itens: <strong>{{ sizeof($data) }}</strong></td>
            <td><strong>{{ $qtd }}</strong></td>
        </tr>
    </tfoot>
</table>

@endsection
