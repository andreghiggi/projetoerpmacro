<table>
    <thead>
        <tr>
            <th style="width: 300px">Cliente</th>
            <th style="width: 300px">Fornecedor</th>
            <th style="width: 300px">Vendedor</th>
            <th style="width: 300px">Assunto</th>
            <th style="width: 120px">Data de cadastro</th>
            <th style="width: 120px">Data de retorno</th>
            <th style="width: 120px">Conclusão</th>
            <th style="width: 120px">Status</th>

        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr>
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
