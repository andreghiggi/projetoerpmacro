<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h2>Olá, documento(s) em anexo</h2>

	<p>Chave: <strong>{{ $nfce->chave }}</strong></p>
	<p>Número da NFCe: <strong>{{ $nfce->numero }}</strong></p>
	<p>Valor da NFCe: <strong>R$ {{ __moeda($nfce->total) }}</strong></p>
	<p>Data de emissão: <strong>{{ __data_pt($nfce->data_emissao) }}</strong></p>

	<h4>Enviado por <strong>{{ $nfce->empresa->info }}</strong></h4>
	
</body>
</html>