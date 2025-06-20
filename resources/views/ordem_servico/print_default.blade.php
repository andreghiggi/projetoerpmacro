<html>
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
    <link rel="stylesheet" href="/css/style_pdf.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css?family=Pinyon+Script" rel="stylesheet">
</head>
<body onload="gerarArquivo()">
    <div class="row" id="pdf">
        <div class="row topo">
            <div class="col s5 logo center-align">
                @if($config->logo != '')
                <img class="logo" src="/uploads/logos/{{$config->logo}}" style="height: 50px">
                @else
                <img class="logo" src="/imgs/logo.png" style="height: 50px">
                @endif
                <p class="">Email: {{ env("MAILMASTER") }}</p>
            </div>
            <div class="col s7 center-align">
                <h5>{{$config->razao_social}}</h5>
                <label>
                    <strong>Relatório de OS: {{$ordem->id}}</strong><br>
                    <strong>CNPJ: {{$config->cpf_cnpj}}</strong>
                </label><br>
                <span>{{$config->rua}}, {{$config->numero}} - {{$config->bairro}}</span><br>
                <span>CEP: {{$config->cep}} - {{$config->cidade->nome}} - {{$config->cidade->uf}}</span>
            </div>
        </div>
        <div class="row identificacao-paciente">
            <div class="col s7">
                <label>Data de criação: <strong id="data-exame">{{\Carbon\Carbon::parse($ordem->created_at)->format('d/m/Y')}}</strong></label><br>
                <label>Cliente: <strong>{{$ordem->cliente->razao_social}}</strong></label><br>
                <label>Endreço: <strong>{{$ordem->cliente->rua}}, {{$ordem->cliente->numero}} - {{$ordem->cliente->bairro}}
                    </strong></label><br>
                <label>Telefone: <strong>{{$ordem->cliente->telefone}}</strong></label><br>
            </div>
            <div class="col s3">
                <label></label><br>
                <label>Cidade: <strong>{{$ordem->cliente->cidade->nome}} - {{$ordem->cliente->cidade->uf}}</strong></label><br>
                <label>Celular: <strong>{{$ordem->cliente->celular}}</strong></label><br>
                <label></label>
            </div>
        </div>
        @yield('content')
        <div class="row rodape">
            <div class="col s6 center-align">
                <br>
                <div class="traco-assinatura"></div>
                <span class=""><strong>_________________________________</strong></span><br>
                <span class=""><strong>Assinatura responsável</strong></span><br>
            </div>
            <div class="col s6 center-align">
                <br>
                <div class="traco-assinatura"></div>
                <span><strong>___________________________________</strong></span><br>
                <span><strong>Assinatura Cliente</strong></span>
                <br>
            </div>
        </div>
        <div class="row rodape-info center-align">
            <div class="col s12">
                <h6>{{$config->cidade->nome}} , ____de_______________de________</h6>
                <h6>Ordem de Serviço: <strong>{{$ordem->id}}</strong></h6>
                <span></span>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/js/html2canvas.min.js"></script>
    <script type="text/javascript" src="/js/jspdf.min.js"></script>
    <script type="text/javascript" src="/js/gerarPdf.js"></script>
</body>
</html>
