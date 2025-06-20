@extends('relatorios.default', ['title' => 'CRM'])
@section('content')

<table class="table-sm table-borderless" style="border-bottom: 1px solid rgb(206, 206, 206); margin-bottom:10px;  width: 100%;">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Fornecedor</th>
            <th>Vendedor</th>
            <th>Assunto</th>
            <th>Data de cadastro</th>
            <th>Data de retorno</th>
            <th>Conclusão</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr class="@if($key%2 == 0) pure-table-odd @endif">
            <td>{{ $item->cliente ? $item->cliente->info : '--' }}</td>
            <td>{{ $item->fornecedor ? $item->fornecedor->info : '--' }}</td>
            <td>{{ $item->funcionario ? $item->funcionario->nome : '--' }}</td>
            <td>{{ $item->assunto }}</td>
            <td style="width: 150px;">{{ __data_pt($item->created_at) }}</td>
            <td style="width: 150px;">{{ $item->data_retorno ? __data_pt($item->data_retorno, 0) : '--' }}</td>
            <td>{{ $item->conclusao }}</td>

            <td>
                {{ strtoupper($item->status) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
