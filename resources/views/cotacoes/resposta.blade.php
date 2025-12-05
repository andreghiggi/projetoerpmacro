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
			<form class="col-lg-12" method="post" action="{{ route('cotacoes.resposta-store') }}">
				@csrf
				<input type="hidden" name="cotacao_id" value="{{ $cotacao->id }}">
				<h3 class="w-100 text-center">COTAÇÃO <strong class="text-primary">#{{ $cotacao->referencia }}</strong></h3>

				
				<div class="row">
					<div class="col-lg-8 col-12">
						<h5>SOLICITANTE: <strong>{{ $empresa->nome }}</strong></h5>
					</div>
					<div class="col-lg-4 col-12">
						<h5>TELEFONE: <strong>{{ $empresa->celular }}</strong></h5>
					</div>

					<div class="col-lg-8 col-12">
						<h5>FORNECEDOR: <strong>{{strtoupper($cotacao->fornecedor->razao_social)}}</strong></h5>
					</div>
					<div class="col-lg-4 col-12">
						<h5>CIDADE: <strong>{{ $cotacao->fornecedor->cidade->info }}</strong>
						</h5>
					</div>
					<div class="col-lg-3 col-12">
						<h5>CNPJ: <strong>{{ $cotacao->fornecedor->cpf_cnpj }}</strong></h5>
					</div>
				</div>

				@if($cotacao->observacao)
				<h5>Observação: <strong>{{ $cotacao->observacao }}</strong></h5>
				@endif

				<div class="card">
					<div class="card-header">
						<h5 style="font-weight: bold;">Itens da Cotação</h5>
					</div>

					<div class="card-body">
						<p class="text-danger">* Campos obrigatórios</p>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>Produto</th>
										<th>Quantidade</th>
										<th>Valor Unitário<span class="text-danger">*</span></th>
										<th>Subtotal<span class="text-danger">*</span></th>
										<th>Observação do item</th>
									</tr>
								</thead>
								<tbody>

									@foreach($cotacao->itens as $linha => $p)
									<tr>
										<input type="hidden" class="" name="item_id[]" value="{{ $p->id }}">
										<td>
											<input style="width: 450px" disabled type="tel" class="form-control" name="produto_nome[]" value="{{ $p->descricao_produto }}">
										</td>
										<td id="quantity">
											@php
											$casasDecimais = 2;
											@endphp
											<input style="width: 150px" required readonly type="tel" class="form-control moeda" value="{{ number_format($p->quantidade, $casasDecimais) }}" name="quantidade[]">
										</td>

										<td>
											<input style="width: 150px" required type="tel" class="form-control moeda value" id="value" name="valor_unitario[]">
										</td>

										<td>
											<input style="width: 150px" readonly type="text" name="subtotal[]" class="form-control subtotal">
										</td>

										<td>
											<input style="width: 300px" type="tel" name="observacao_item[]" class="form-control ">
										</td>
									</tr>
									@endforeach

								</tbody>
							</table>
							
						</div>
						<h5>Soma dos produtos <strong class="total">R$ 0,00</strong></h5>
						<hr>
						<div class="row g-2">
							<div class="form-group col-lg-2 col-6">
								<label>Desconto</label>
								<input type="tel" id="desconto" name="desconto" class="form-control moeda">
							</div>
							<div class="form-group col-lg-2 col-6">
								<label>Valor do frete</label>
								<input type="tel" id="valor_frete" name="valor_frete" class="form-control moeda">
							</div>
							<div class="form-group col-lg-6 col-12">
								<label>Observação do frete</label>
								<input type="text" name="observacao_frete" class="form-control">
							</div>
							<div class="form-group col-lg-2 col-6">
								<label>Previsão de entrega<span class="text-danger">*</span></label>
								<input required type="date" id="previsao_entrega" name="previsao_entrega" class="form-control">
							</div>
							<div class="form-group col-lg-3 col-12">
								<label>Resposável<span class="text-danger">*</span></label>
								<input required type="text" name="responsavel" class="form-control">
							</div>
							<div class="form-group col-lg-7 col-12">
								<label>Observação</label>
								<input type="text" name="observacao" class="form-control">
							</div>
						</div>

						<h4 class="mt-3 text-primary">Valor total da cotação <strong class="total-cotacao">R$ 0,00</strong></h4>
						<hr>
						<div class="table-responsive mt-2">
							<h5>Fatura da cotação (opcional)</h5>
							<div>
								<table class="table table-dynamic">
									<thead>
										<tr>
											<th>Data de vencimento</th>
											<th>Valor da parcela</th>
											<th>Tipo de pagamento</th>
										</tr>
									</thead>
									<tbody>
										<tr class="dynamic-form">
											<td>
												<input style="width: 150px" type="date" name="data_vencimento[]" class="form-control">
											</td>
											<td>
												<input style="width: 150px" type="tel" name="valor_parcela[]" class="form-control moeda valor_parcela">
											</td>
											<td>
												<select style="width: 200px" class="form-control form-select" name="tipo_pagamento[]">
													@foreach(App\Models\FaturaCotacao::tiposPagamento() as $key => $tp)
													<option value="{{ $key }}">{{ $tp }}</option>
													@endforeach
												</select>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="row m-1 col-6 col-lg-2">
								<button type="button" class="btn btn-dark btn-add-tr">
									Adicionar parcela
								</button>
							</div>
						</div>
						<h5 class="mt-3 text-primary">Soma fatura <strong class="total-fatura">R$ 0,00</strong></h5>

						<div class="row">
							<div class="col-12">
								<button type="submit" class="btn btn-success btn-lg" style="float: right;">Enviar cotação</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</main>

	<script type = "text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
	<script type="text/javascript">
		$('.moeda').mask('000.000.000.000.000,00', {reverse: true});
	</script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>

	<script src="/js/cotacao_response.js"></script>
	<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</body>
</html>