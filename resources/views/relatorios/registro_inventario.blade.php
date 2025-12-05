<!DOCTYPE html>
<html>
<head>

    <style type="text/css">
        @page {
            margin: 1px;
        }
        body {
            margin: 10px;
            font-size: 15px;
            font-family: "Lucida Console", "Courier New", monospace;
        }
    </style>

</head>

<body>

    <table>
        <tr>
            <td style="width: 400px">{{ $empresa->nome }}</td>
            <td style="width: 360px; text-align: center;">REGISTRO DE INVENTÁRIO</td>
            <td style="width: 325px; text-align: right;">Livro.: {{ $livro }}</td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="width: 350px">INSC. ESTADUAL.: {{ $empresa->ie }}</td>
            <td style="width: 380px; text-align: center;">CNPJ(MF): {{ $empresa->cpf_cnpj }}</td>
            <td style="width: 355px; text-align: right;">existente em: {{ date('Y-m-d H:i') }}</td>
        </tr>
    </table>

    <br>
    <table>
        <thead>
            <tr>
                <td style="width: 80px">NCM</td>
                <td style="width: 420px">DESCRIÇÃO DO ARTIGO</td>
                <td style="width: 150px; text-align: right;">QUANTIDADE</td>
                <td style="width: 120px; text-align: right;">UNIDADE</td>
                <td style="width: 150px; text-align: right;">CUSTO UNITÁRIO</td>
                <td style="width: 150px; text-align: right;">CUSTO TOTAL</td>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            @if($item->quantidade > 0)
            <tr>
                <td>{{ preg_replace('/[^0-9]/', '', $item->produto->ncm)}}</td>
                <td>{{ $item->produto->nome }}</td>
                <td style="text-align: right;">{{ number_format($item->quantidade, 4, ',', '') }}</td>
                <td style="text-align: right;">{{ $item->produto->unidade }}</td>
                <td style="text-align: right;">{{ __moeda($item->valor_unitario) }}</td>
                <td style="text-align: right;">{{ __moeda($item->sub_total) }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;">Total........:</td>
                <td style="text-align: right;">{{ __moeda($data->sum('sub_total')) }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>