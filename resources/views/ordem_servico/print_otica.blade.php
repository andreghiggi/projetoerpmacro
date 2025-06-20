<html>

<head>

    <style type="text/css">
        @page {
            margin: 0cm 0cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 2cm;
        }


        /** Define the header rules **/
        header {
            position: relative;
            margin-top: 0px;
            margin-left: 40px;
            margin-right: 40px;
            margin-bottom: 25px;
            height: 20px;
        }

        .banner {
            text-align: center;
            display: flex;
            align-items: flex-start;
        }

        td {
            text-align: center;
        }

        p {
            font-size: 12px;
            margin-top: 0px;
            margin-bottom: 2px;
        }

        .pure-table-odd {
            background: #EBEBEB;
        }

        .logoBanner img {
            float: left;
            max-width: 70px;
        }

        .banner h1 {
            position: absolute;
            margin-top: 0;
        }

        .banner hr {
            margin-top: 29px;
            margin-left: 120px;
        }

        .date {
            float: right;
        }

        .provider {
            text-align: left;
            margin-top: 5px;
            margin-bottom: 10px;
        }


        .client {
            margin-bottom: 0.6rem;
        }

        footer {
            position: fixed;
            bottom: 1.9cm;
            left: 0.4cm;
            right: 0cm;
            height: 0cm;
        }

        img {
            max-width: 100px;
            height: auto;
        }


        table {
            font-size: 0.8rem;
            margin: 0;
        }

        table thead {
            border-bottom: 1px solid rgb(206, 206, 206);
            border-top: 1px solid rgb(206, 206, 206);
        }

        .caption {
            /* Make the caption a block so it occupies its own line. */
            display: block;
        }

        .row {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-1,
        .col-2,
        .col-3,
        .col-4,
        .col-5,
        .col-6,
        .col-7,
        .col-8,
        .col-9,
        .col-10,
        .col-11,
        .col-12,
        .col,
        .col-auto,
        .col-sm-1,
        .col-sm-2,
        .col-sm-3,
        .col-sm-4,
        .col-sm-5,
        .col-sm-6,
        .col-sm-7,
        .col-sm-8,
        .col-sm-9,
        .col-sm-10,
        .col-sm-11,
        .col-sm-12,
        .col-sm,
        .col-sm-auto,
        .col-md-1,
        .col-md-2,
        .col-md-3,
        .col-md-4,
        .col-md-5,
        .col-md-6,
        .col-md-7,
        .col-md-8,
        .col-md-9,
        .col-md-10,
        .col-md-11,
        .col-md-12,
        .col-md,
        .col-md-auto,
        .col-lg-1,
        .col-lg-2,
        .col-lg-3,
        .col-lg-4,
        .col-lg-5,
        .col-lg-6,
        .col-lg-7,
        .col-lg-8,
        .col-lg-9,
        .col-lg-10,
        .col-lg-11,
        .col-lg-12,
        .col-lg,
        .col-lg-auto,
        .col-xl-1,
        .col-xl-2,
        .col-xl-3,
        .col-xl-4,
        .col-xl-5,
        .col-xl-6,
        .col-xl-7,
        .col-xl-8,
        .col-xl-9,
        .col-xl-10,
        .col-xl-11,
        .col-xl-12,
        .col-xl,
        .col-xl-auto {
            position: relative;
            width: 100%;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col {
            -ms-flex-preferred-size: 0;
            flex-basis: 0;
            -webkit-box-flex: 1;
            -ms-flex-positive: 1;
            flex-grow: 1;
            max-width: 100%;
        }

        .col-auto {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 auto;
            flex: 0 0 auto;
            width: auto;
            max-width: none;
        }

        .col-1 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 8.333333%;
            flex: 0 0 8.333333%;
            max-width: 8.333333%;
        }

        .col-2 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 16.666667%;
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
        }

        .col-3 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 25%;
            flex: 0 0 25%;
            max-width: 25%;
        }

        .col-4 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 33.333333%;
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }

        .col-5 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 41.666667%;
            flex: 0 0 41.666667%;
            max-width: 41.666667%;
        }

        .col-6 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 50%;
            flex: 0 0 50%;
            max-width: 50%;
        }

        .col-7 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 58.333333%;
            flex: 0 0 58.333333%;
            max-width: 58.333333%;
        }

        .col-8 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 66.666667%;
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        }

        .col-9 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 75%;
            flex: 0 0 75%;
            max-width: 75%;
        }

        .col-10 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 83.333333%;
            flex: 0 0 83.333333%;
            max-width: 83.333333%;
        }

        .col-11 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 91.666667%;
            flex: 0 0 91.666667%;
            max-width: 91.666667%;
        }

        .col-12 {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 100%;
            flex: 0 0 100%;
            max-width: 100%;
        }

        .text-justify {
            text-align: justify !important;
        }

        .text-nowrap {
            white-space: nowrap !important;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .w-25 {
            width: 25% !important;
        }

        .w-50 {
            width: 50% !important;
        }

        .w-75 {
            width: 75% !important;
        }

        .w-100 {
            width: 100% !important;
        }

        .h-25 {
            height: 25% !important;
        }

        .h-50 {
            height: 50% !important;
        }

        .h-75 {
            height: 75% !important;
        }

        .h-100 {
            height: 100% !important;
        }

        .mw-100 {
            max-width: 100% !important;
        }

        .mh-100 {
            max-height: 100% !important;
        }

        .m-0 {
            margin: 0 !important;
        }

        .mt-0,
        .my-0 {
            margin-top: 0 !important;
        }

        .mr-0,
        .mx-0 {
            margin-right: 0 !important;
        }

        .mb-0,
        .my-0 {
            margin-bottom: 0 !important;
        }

        .ml-0,
        .mx-0 {
            margin-left: 0 !important;
        }

        .m-1 {
            margin: 0.25rem !important;
        }

        .mt-1,
        .my-1 {
            margin-top: 0.25rem !important;
        }

        .mr-1,
        .mx-1 {
            margin-right: 0.25rem !important;
        }

        .mb-1,
        .my-1 {
            margin-bottom: 0.25rem !important;
        }

        .ml-1,
        .mx-1 {
            margin-left: 0.25rem !important;
        }

        .m-2 {
            margin: 0.5rem !important;
        }

        .mt-2,
        .my-2 {
            margin-top: 0.5rem !important;
        }

        .mr-2,
        .mx-2 {
            margin-right: 0.5rem !important;
        }

        .mb-2,
        .my-2 {
            margin-bottom: 0.5rem !important;
        }

        .ml-2,
        .mx-2 {
            margin-left: 0.5rem !important;
        }

        .m-3 {
            margin: 1rem !important;
        }

        .mt-3,
        .my-3 {
            margin-top: 1rem !important;
        }

        .mr-3,
        .mx-3 {
            margin-right: 1rem !important;
        }

        .mb-3,
        .my-3 {
            margin-bottom: 1rem !important;
        }

        .ml-3,
        .mx-3 {
            margin-left: 1rem !important;
        }

        .m-4 {
            margin: 1.5rem !important;
        }

        .mt-4,
        .my-4 {
            margin-top: 1.5rem !important;
        }

        .mr-4,
        .mx-4 {
            margin-right: 1.5rem !important;
        }

        .mb-4,
        .my-4 {
            margin-bottom: 1.5rem !important;
        }

        .ml-4,
        .mx-4 {
            margin-left: 1.5rem !important;
        }

        .m-5 {
            margin: 3rem !important;
        }

        .mt-5,
        .my-5 {
            margin-top: 3rem !important;
        }

        .mr-5,
        .mx-5 {
            margin-right: 3rem !important;
        }

        .mb-5,
        .my-5 {
            margin-bottom: 3rem !important;
        }

        .ml-5,
        .mx-5 {
            margin-left: 3rem !important;
        }

        .p-0 {
            padding: 0 !important;
        }

        .pt-0,
        .py-0 {
            padding-top: 0 !important;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pb-0,
        .py-0 {
            padding-bottom: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .p-1 {
            padding: 0.25rem !important;
        }

        .pt-1,
        .py-1 {
            padding-top: 0.25rem !important;
        }

        .pr-1,
        .px-1 {
            padding-right: 0.25rem !important;
        }

        .pb-1,
        .py-1 {
            padding-bottom: 0.25rem !important;
        }

        .pl-1,
        .px-1 {
            padding-left: 0.25rem !important;
        }

        .p-2 {
            padding: 0.5rem !important;
        }

        .pt-2,
        .py-2 {
            padding-top: 0.5rem !important;
        }

        .pr-2,
        .px-2 {
            padding-right: 0.5rem !important;
        }

        .pb-2,
        .py-2 {
            padding-bottom: 0.5rem !important;
        }

        .pl-2,
        .px-2 {
            padding-left: 0.5rem !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .pt-3,
        .py-3 {
            padding-top: 1rem !important;
        }

        .pr-3,
        .px-3 {
            padding-right: 1rem !important;
        }

        .pb-3,
        .py-3 {
            padding-bottom: 1rem !important;
        }

        .pl-3,
        .px-3 {
            padding-left: 1rem !important;
        }

        .p-4 {
            padding: 1.5rem !important;
        }

        .pt-4,
        .py-4 {
            padding-top: 1.5rem !important;
        }

        .pr-4,
        .px-4 {
            padding-right: 1.5rem !important;
        }

        .pb-4,
        .py-4 {
            padding-bottom: 1.5rem !important;
        }

        .pl-4,
        .px-4 {
            padding-left: 1.5rem !important;
        }

        .p-5 {
            padding: 3rem !important;
        }

        .pt-5,
        .py-5 {
            padding-top: 3rem !important;
        }

        .pr-5,
        .px-5 {
            padding-right: 3rem !important;
        }

        .pb-5,
        .py-5 {
            padding-bottom: 3rem !important;
        }

        .pl-5,
        .px-5 {
            padding-left: 3rem !important;
        }

        .m-auto {
            margin: auto !important;
        }

        .mt-auto,
        .my-auto {
            margin-top: auto !important;
        }

        .mr-auto,
        .mx-auto {
            margin-right: auto !important;
        }

        .mb-auto,
        .my-auto {
            margin-bottom: auto !important;
        }

        .ml-auto,
        .mx-auto {
            margin-left: auto !important;
        }

        * {
            font-family: "Lucida Console", "Courier New", monospace;
        }

        .headReport{
            border: .1px solid #000;
            border-radius: 5px;
        }
        .text-table{
            font-size: 10px;
            margin-left: 10px;
        }
        .bold{
            font-weight: bold;
        }

        .produtos_servicos th{
            font-size: 11px;
        }
        .produtos_servicos td{
            font-size: 10px;
            text-align: left;
        }

        .print-cut{
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body style="margin: 25px">

    @php $printCut = 0; @endphp

    @if($viaCliente)
    @php $printCut = 1; @endphp

    <div class="headReport">

        <table>
            <tr>
                <td style="text-align: left; width: 50px;">
                    @if($config->logo != null)
                    <img style="height: 50px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('/uploads/logos/'. $config->logo)))}}" alt="Logo" class="mb-2">
                    @else
                    <img style="height: 50px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('logo.png')))}}">
                    @endif
                </td>
                <td style="text-align: left; width: 500px;">
                    <span class="text-table bold">{{ $config->nome }}</span><br>
                    <span class="text-table">CNPJ: {{ $config->cpf_cnpj }}</span><br>
                    <span class="text-table">{{ $config->rua }}, {{ $config->numero }}, {{ $config->bairro }} 
                        {{ $config->cidade->info }}   {{ $config->celular }}
                    </span><br>
                </td>

                <td style="text-align: right; width: 150px;">
                    <span class="text-table">Ordem de Serviço: 
                        <label class="bold">{{ $ordem->codigo_sequencial }}</label>
                    </span>
                </td>
            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td style="width: 420px; text-align: left;">
                    <span class="text-table"><label class="bold">Cliente:</label>
                        {{ $ordem->cliente->razao_social }}
                    </span>
                </td> 
                <td style="width: 300px;">
                    <span class="text-table">
                        <label class="bold">Data:</label> {{ __data_pt($ordem->created_at, 0) }}
                    </span>
                    <span class="text-table">
                        <label class="bold">Prev. Entrega:</label> {{ __data_pt($ordem->data_entrega, 0) }}
                    </span>
                </td> 
            </tr>
        </table>
    </div>
    <label style="font-size: 8px; float: right; margin-top: 2px;">Via do cliente</label>
    @endif



    @if($viaLaboratorio)
    @if($printCut)
    <div class="print-cut">
        ..............................................................................
    </div>
    @endif
    @php $printCut = 1; @endphp

    <div class="headReport">

        <table>
            <tr>
                <td style="text-align: left; width: 50px;">
                    @if($config->logo != null)
                    <img style="height: 50px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('/uploads/logos/'. $config->logo)))}}" alt="Logo" class="mb-2">
                    @else
                    <img style="height: 50px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('logo.png')))}}">
                    @endif
                </td>
                <td style="text-align: left; width: 500px;">
                    <span class="text-table bold">{{ $config->info }}</span><br>
                    <span class="text-table">{{ $config->rua }}, {{ $config->numero }}, {{ $config->bairro }} 
                        {{ $config->cidade->info }}   {{ $config->celular }}
                    </span><br>
                </td>

            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td style="width: 350px; text-align: left;">
                    <span class="text-table"><label class="bold">Cliente:</label>
                        {{ $ordem->cliente->razao_social }}
                    </span>
                </td> 
                <td style="width: 200px; text-align: left;">
                    <span class="text-table">
                        <label class="bold">Data:</label> {{ __data_pt($ordem->created_at, 0) }}
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">
                    <span class="text-table">
                        <label class="bold">Prev. Entrega:</label> {{ __data_pt($ordem->data_entrega, 0) }}
                    </span>
                </td> 
            </tr>
            <tr>
                <td style="width: 350px; text-align: left;">
                    <span class="text-table"><label class="bold">Ordem de Serviço:</label>
                        {{ $ordem->codigo_sequencial }}
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">
                    <span class="text-table">
                        <label class="bold">Tipo de armação:</label> {{ $ordem->oticaOs->tipo_armacao_id ? $ordem->oticaOs->tipoArmacao->nome : '' }}
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">
                    <span class="text-table">
                        <label class="bold">Atendente:</label> {{ $ordem->usuario->name }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="width: 350px; text-align: left;">
                    <span class="text-table"><label class="bold">Armação </label>
                        Própria:
                        @if($ordem->oticaOs)
                        {{ $ordem->oticaOs->armacao_propria ? 'Sim' : 'Não' }}
                        @else
                        '--'
                        @endif

                        - Segue:
                        @if($ordem->oticaOs)
                        {{ $ordem->oticaOs->armacao_segue ? 'Sim' : 'Não' }}
                        @else
                        '--'
                        @endif
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">
                    <span class="text-table">
                        <label class="bold">Lente:</label>
                        @if($ordem->oticaOs)
                        {{ $ordem->oticaOs->tipo_lente }}
                        @else
                        '--'
                        @endif
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">

                </td>
            </tr>

        </table>
        <hr>
        @if($ordem->oticaOs)
        <table>
            <tr>
                <td style="width: 700px; text-align: left;">
                    <span class="text-table"><label class="bold">Observação da receita:</label>
                        @if($ordem->oticaOs->observacao_receita)
                        {!! $ordem->oticaOs->observacao_receita !!}
                        @else
                        Não possui receita
                        @endif
                    </span>
                </td> 
            </tr>
        </table>
        @endif
        @if($ordem->descricao)
        <table>
            <tr>
                <td style="width: 700px; text-align: left;">
                    <span class="text-table"><label class="bold">Observação geral:</label>
                        {!! $ordem->descricao !!}
                    </span>
                </td> 
            </tr>
        </table>
        @endif

    </div>
    <label style="font-size: 8px; float: right; margin-top: 2px;">Via do laboratório</label>
    @endif

    <!-- OS -->

    @if($os)

    @if($printCut)
    <div class="print-cut">
        ..............................................................................
    </div>
    @endif

    <div class="headReport">

        <table>
            <tr>
                <td style="text-align: left; width: 50px;">
                    @if($config->logo != null)
                    <img style="height: 50px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('/uploads/logos/'. $config->logo)))}}" alt="Logo" class="mb-2">
                    @else
                    <img style="height: 50px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('logo.png')))}}">
                    @endif
                </td>
                <td style="text-align: left; width: 500px;">
                    <span class="text-table bold">{{ $config->info }}</span><br>
                    <span class="text-table">{{ $config->rua }}, {{ $config->numero }}, {{ $config->bairro }} 
                        {{ $config->cidade->info }}   {{ $config->celular }}
                    </span><br>
                </td>

            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td style="width: 700px; text-align: left;">
                    <span class="text-table"><label class="bold">Cliente:</label>
                        {{ $ordem->cliente->razao_social }} - {{ $ordem->cliente->telefone }}
                    </span>
                </td> 
            </tr>
            <tr>
                <td style="width: 700px; text-align: left;">
                    <span class="text-table"><label class="bold">Endereço:</label>
                        {{ $ordem->cliente->rua }}, {{ $ordem->cliente->numero }}, {{ $ordem->cliente->bairro }}
                        {{ $ordem->cliente->cidade->info }} - {{ $ordem->cliente->cep }}
                    </span>
                </td> 
            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td style="width: 350px; text-align: left;">
                    <span class="text-table"><label class="bold">Ordem de Serviço:</label>
                        {{ $ordem->codigo_sequencial }}
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">
                    <span class="text-table">
                        <label class="bold">Tipo de armação:</label> {{ $ordem->oticaOs->tipoArmacao ? $ordem->oticaOs->tipoArmacao->nome : '' }}
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">
                    <span class="text-table">
                        <label class="bold">Atendente:</label> {{ $ordem->usuario->name }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="width: 350px; text-align: left;">
                    <span class="text-table"><label class="bold">Armação </label>
                        Própria:
                        @if($ordem->oticaOs)
                        {{ $ordem->oticaOs->armacao_propria ? 'Sim' : 'Não' }}
                        @else
                        '--'
                        @endif

                        - Segue:
                        @if($ordem->oticaOs)
                        {{ $ordem->oticaOs->armacao_segue ? 'Sim' : 'Não' }}
                        @else
                        '--'
                        @endif
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">
                    <span class="text-table">
                        <label class="bold">Lente:</label>
                        @if($ordem->oticaOs)
                        {{ $ordem->oticaOs->tipo_lente }}
                        @else
                        '--'
                        @endif
                    </span>
                </td>
                <td style="width: 200px; text-align: left;">

                </td>
            </tr>

        </table>
        <hr>

        <table class="produtos_servicos">
            <thead>
                <tr>
                    <th style="width: 425px; text-align: left;">Produto/Serviço</th>
                    <th style="width: 100px; text-align: left;">Qtde</th>
                    <th style="width: 100px; text-align: left;">Vl. Un. (R$)</th>
                    <th style="width: 100px; text-align: left;">Subtotal (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordem->servicos as $item)
                <tr>
                    <td>{{ $item->servico->nome }}</td>
                    <td>{{ $item->quantidade }}</td>
                    <td>{{ __moeda($item->valor) }}</td>
                    <td>{{ __moeda($item->subtotal) }}</td>
                </tr>
                @endforeach

                @foreach($ordem->itens as $item)
                <tr>
                    <td>{{ $item->produto->nome }}</td>
                    <td>{{ $item->quantidade }}</td>
                    <td>{{ __moeda($item->valor) }}</td>
                    <td>{{ __moeda($item->subtotal) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="background-color: #E7E9EB">
                <tr>
                    <td colspan="3">Total</td>
                    <td>{{ __moeda($ordem->valor) }}</td>
                </tr>
            </tfoot>
        </table>
        <hr>
        @if($ordem->oticaOs)
        <table>
            <tr>
                <td style="width: 700px; text-align: left;">
                    <span class="text-table"><label class="bold">Observação da receita:</label>
                        @if($ordem->oticaOs->observacao_receita)
                        {!! $ordem->oticaOs->observacao_receita !!}
                        @else
                        Não possui receita
                        @endif
                    </span>
                </td> 
            </tr>
        </table>
        @endif
        @if($ordem->descricao)
        <table>
            <tr>
                <td style="width: 700px; text-align: left;">
                    <span class="text-table"><label class="bold">Observação geral:</label>
                        {!! $ordem->descricao !!}
                    </span>
                </td> 
            </tr>
        </table>
        @endif

    </div>
    <label style="font-size: 8px; float: right; margin-top: 2px;">Via da ótica</label>
    @endif

</body>

</html>
