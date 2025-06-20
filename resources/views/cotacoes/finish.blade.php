<!DOCTYPE html>
<html>
<head>
	<title>Resposta de Cotação</title>
	<meta name = "viewport" content = "width = device-width, initial-scale = 1">      

	<link rel="dns-prefetch" href="//fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

	<!-- Scripts -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

</head>
<body>
	<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
		<div class="container">
			
		</div>
	</nav>
	<main class="py-4">
		<div class="container">
			@if(session()->has('mensagem_sucesso'))
			<div class="alert alert-success" role="alert">
				{{ session()->get('mensagem_sucesso') }}
			</div>
			@endif

			@if(session()->has('mensagem_erro'))
			<div class="alert alert-danger" role="alert">
				{{ session()->get('mensagem_erro') }}
			</div>
			@endif
		</div>
	</main>

	<script type = "text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
	<script type="text/javascript">
		$('.moeda').mask('000.000.000.000.000,00', {reverse: true});
	</script>
	<script src="/js/quotes.js"></script>
	<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</body>
</html>