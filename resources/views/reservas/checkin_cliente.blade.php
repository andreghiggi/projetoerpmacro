<!DOCTYPE html>
<html>
<head>
	<title>CheckIn Reserva</title>
	<meta name = "viewport" content = "width = device-width, initial-scale = 1">      

	<link rel="dns-prefetch" href="//fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

	<!-- Scripts -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
	<link href="/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
		.select2.select2-container .select2-selection {
			border: 1px solid #DEE2E6;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			height: 35px;
			margin-bottom: 15px;
			outline: none !important;
			transition: all .15s ease-in-out;
		}
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
		<div class="container">
			<a class="navbar-brand">
				@if($empresa->logo != '')
				<img src="{{ $empresa->img }}" width="120">
				@else
				{{ $empresa->nome }}
				@endif
			</a>
		</div>
	</nav>
	<main class="py-4">
		<div class="m-4">
			<form class="col-lg-12" method="post" action="{{ route('reservas.checkin-start-cliente', [$item->id]) }}">
				@csrf

				<h3 class="w-100 text-center">RESERVA <strong class="text-primary">#{{ $item->codigo_reseva }}</strong></h3>

				
				<div class="row">
					<div class="col-lg-8 col-12">
						<h6>SOLICITANTE: <strong>{{ $empresa->nome }}</strong></h6>
					</div>
					<div class="col-lg-4 col-12">
						<h6>TELEFONE: <strong>{{ $empresa->celular }}</strong></h6>
					</div>

					<div class="col-lg-8 col-12">
						<h6>FORNECEDOR: <strong>{{ strtoupper($item->cliente->razao_social) }}</strong></h6>
					</div>
					<div class="col-lg-4 col-12">
						<h6>CIDADE: <strong>{{ $item->cliente->cidade->info }}</strong>
						</h6>
					</div>
					<div class="col-lg-3 col-12">
						<h6>DOCUMENTO: <strong>{{ $item->cliente->cpf_cnpj }}</strong></h6>
					</div>
				</div>

				@if($item->observacao)
				<h6>Observação: <strong>{{ $item->observacao }}</strong></h6>
				@endif

				@foreach($item->hospedes as $key => $hospede)

				<div class="card mt-1">
					<div class="card-body">
						<h6>Hóspede {{ $key+1 }}</h6>
						<div class="row g-2">
							<input type="hidden" name="hospede_id[]" value="{{ $hospede->id }}">
							<div class="col-md-4">
								{!!Form::text('nome_completo[]', 'Nome completo*')
								->required()
								->value($hospede->nome_completo)
								!!}
							</div>
							<div class="col-md-2">
								{!!Form::tel('cpf[]', 'CPF*')
								->required()
								->attrs(['class' => 'cpf'])
								->value($hospede->cpf)
								!!}
							</div>
							<hr class="mt-1">
							<div class="col-md-2 col">
								{!!Form::tel('cep[]', 'CEP*')
								->required()
								->attrs(['class' => 'cep'])
								->value($hospede->cep)
								!!}
							</div>
							<div class="col-md-4 col">
								{!!Form::text('rua[]', 'Rua*')
								->required()
								->value($hospede->cpf)
								!!}
							</div>
							<div class="col-md-2 col">
								{!!Form::text('numero[]', 'Número*')
								->required()
								->value($hospede->numero)
								!!}
							</div>
							<div class="col-md-3 col">
								{!!Form::text('bairro[]', 'Bairro*')
								->required()
								->value($hospede->bairro)
								!!}
							</div>

							<div class="col-md-3 col">
								{!!Form::select('cidade_id[]', 'Cidade*')
								->required()
								->id('cidade_'.$key)
								->attrs(['class' => 'cidade'])
								->options(($hospede != null && $hospede->cidade) ? [$hospede->cidade_id => $hospede->cidade->info] : [])
								!!}
							</div>

							<div class="col-md-2">
								{!!Form::tel('telefone[]', 'Telefone*')
								->required()
								->attrs(['class' => 'fone'])
								->value($hospede->telefone)
								!!}
							</div>

							<div class="col-md-3">
								{!!Form::text('email[]', 'Email')
								->type('email')
								->value($hospede->email)
								!!}
							</div>
						</div>
					</div>
				</div>
				@endforeach

				@if($item->estado == 'pendente')
				<div class="row mt-2">
					<div class="col-12">
						<button type="submit" class="btn btn-success btn-lg" style="float: right;">
							Salvar Reserva
						</button>
					</div>
				</div>
				@else
				<p class="text-success mt-2 text-end">Os dados já estão preenchidos!</p>
				@endif
			</form>
		</div>
	</main>

	<script type = "text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
	<script src="/assets/vendor/select2/js/select2.min.js"></script>

	<script type="text/javascript">
		$('.moeda').mask('000.000.000.000.000,00', {reverse: true});
		$('.cpf').mask('000.000.000-00', {reverse: true});
		$('.cep').mask('00000-000', {reverse: true});

		var SPMaskBehavior = function (val) {
			return val.replace(/\D/g, "").length === 11
			? "(00) 00000-0000"
			: "(00) 0000-00009";
		},
		spOptions = {
			onKeyPress: function (val, e, field, options) {
				field.mask(SPMaskBehavior.apply({}, arguments), options);
			},
		};

		$(".fone").mask(SPMaskBehavior, spOptions);
		let prot = window.location.protocol;
		let host = window.location.host;
		const path_url = prot + "//" + host + "/";
		$(".cidade").select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar a cidade",
			width: "100%",
			ajax: {
				cache: true,
				url: path_url + "api/buscaCidades",
				dataType: "json",
				data: function (params) {
					console.clear();
					var query = {
						pesquisa: params.term,
					};
					return query;
				},
				processResults: function (response) {
					var results = [];

					$.each(response, function (i, v) {
						var o = {};
						o.id = v.id;

						o.text = v.info;
						o.value = v.id;
						results.push(o);
					});
					return {
						results: results,
					};
				},
			},
		});
	</script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>

	<script src="/js/cotacao_response.js"></script>
	<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

	<script type="text/javascript">
		@if(session()->has('flash_success'))
		swal('Sucesso', '{{ session()->get('flash_success') }}', 'success')
		@endif
	</script>
</body>
</html>