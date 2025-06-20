<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download de Arquivos</title>
</head>
<body>
    <h1>Arquivos de Exportação</h1>

    <!-- Links para os arquivos de exportação -->
    <a id="downloadCadTxt" href="{{ url('balanca/filizola?arquivo=cad') }}" style="display: none;">
        Baixar CADTXT.TXT
    </a>
    <a id="downloadSetorTxt" href="{{ url('balanca/filizola?arquivo=setor') }}" style="display: none;">
        Baixar SETORTXT.TXT
    </a>

    <script>
        // Função para disparar o download do arquivo
        function triggerDownload(url) {
            var link = document.createElement('a');
            link.href = url;
            link.click();
        }

        // Baixa os arquivos automaticamente quando a página carregar
        window.onload = function() {
            // Adicionando o log de URLs para depuração
            console.log('URL CAD: ', '{{ url('balanca/filizola?arquivo=cad') }}');
            console.log('URL SETOR: ', '{{ url('balanca/filizola?arquivo=setor') }}');

            triggerDownload('{{ url('balanca/filizola?arquivo=cad') }}');
            // Atraso para o segundo download
            setTimeout(function() {
                triggerDownload('{{ url('balanca/filizola?arquivo=setor') }}');
            }, 2000); // 2 segundos de espera antes de baixar o segundo arquivo
        };
    </script>
</body>
</html>
