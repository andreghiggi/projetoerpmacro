<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h2>Envio de cotação</h2>

	<p>Olá <strong>{{ $cotacao->fornecedor->razao_social }}</strong>, <a href="{{ route('cotacoes.resposta', $cotacao->hash_link) }}">clique aqui para responder a cotação</a></p>

	<p>att, {{ $cotacao->empresa->nome }}</p>
</body>
</html>