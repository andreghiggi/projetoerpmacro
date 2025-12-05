<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>

    * {
        font-family: "Lucida Console", "Courier New", monospace;
    }

    body {
        margin: -20px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2px solid #000;
        padding-bottom: 6px;
        margin-bottom: 1px;
    }

    .header img {
        width: 80px;
        margin-top: -30px;
    }

    .title {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        flex: 1;
        margin-top: 1px;
    }

    .emissao {
        font-size: 10px;
        color: #555;
        text-align: right;
        min-width: 150px;
        margin-top: -12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
    }

    th {
        font-size: 10px;
        font-weight: bold;
        border-bottom: 1px solid #000;
        padding: 4px 0;
        text-align: left;
    }

    td {
        font-size: 11px;
        padding: 3px 0;
        border-bottom: 1px dotted #888;
    }

    .col-produto { width: 45%; }
    .col-barra   { width: 22%; }
    .col-est     { width: 15%; text-align: left; }
    .col-cont    { width: 18%; }

    .input-box {
        border: 1px solid #777;
        height: 16px;
        border-radius: 3px;
        width: 90%;
        padding-left: 3px;
    }

    .resume {
        margin-top: 0px;
        font-size: 13px;
    }

</style>

</head>

<body>

    <!-- CABEÇALHO -->
    <div class="header">
        <img src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('logo.png')))}}" alt="Logo">

        <div class="title">
            Relatório de Inventário
        </div>

        <div class="emissao">
            Emissão: {{ date('d/m/Y - H:i') }}
        </div>
    </div>

    <!-- RESUMO -->
    <div class="resume">
        Total de produtos: <strong>{{ count($item->itensImpresso ) }}</strong>
    </div>

    <!-- TABELA -->
    <table>
        <thead>
            <tr>
                <th class="col-produto">PRODUTO</th>
                <th class="col-barra">CÓD. BARRAS</th>
                <th class="col-est">EST. ATUAL</th>
                <th class="col-cont">QTD. CONTADA</th>
            </tr>
        </thead>

        <tbody>
        @foreach($item->itensImpresso as $i)
            <tr>
                @php $produto = $i->produto; @endphp
                <td class="col-produto">{{ $produto->nome }}</td>

                <td class="col-barra">
                    {{ $produto->codigo_barras ?: '—' }}
                </td>

                <td class="col-est">
                    {{ $produto->estoque ? number_format($produto->estoque->quantidade, 4, '.', '') : '—' }}
                </td>

                <td class="col-cont">
                    <input class="input-box">
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</body>
</html>
