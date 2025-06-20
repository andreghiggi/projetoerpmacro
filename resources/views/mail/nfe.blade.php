<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h2>Olá, documento(s) em anexo</h2>

	<p>Chave: <strong>{{ $nfe->chave }}</strong></p>
	<p>Número da NFe: <strong>{{ $nfe->numero }}</strong></p>
	<p>Valor da NFe: <strong>R$ {{ __moeda($nfe->total) }}</strong></p>
	<p>Data de emissão: <strong>{{ __data_pt($nfe->data_emissao_saida) }}</strong></p>

	<h4>Enviado por <strong>{{ $nfe->empresa->info }}</strong></h4>
	
</body>
</html>