@extends('loja.default', ['title' => 'Minha conta'])
@section('css')
<style type="text/css">
	.card {
		box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
		transition: 0.3s;
		padding: 30px;
	}

	.card:hover {
		box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
	}

	.select2-selection__rendered {
		line-height: 38px !important;
	}
	.select2-container .select2-selection--single {
		height: 40px !important;
		border: 1px solid #E4E7ED;
	}
	.select2-selection__arrow {
		height: 38px !important;
	}
	.img{
		height: 100px;
		border-radius: 10px;
	}
	.produtos{
		margin: 3px;
	}

	.produtos h4{
		margin-top: 20px;
	}
	.produtos h5{
		margin-top: 20px;
	}

	.btn-success{
		background: var(--color-main)!important;
		border: none;
	}

	.btn-success i{
		color: #fff!important;
	}

	.order-submit i{
		color: #fff!important;
	}

	.order-submit{
		border: none;
	}
	
</style>
@endsection
@section('content')


<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<form class="row" method="post" action="{{ route('loja.update-cliente', [$cliente->id]) }}">
			@csrf
			@method('put')
			<input type="hidden" name="link" value="{{ $config->loja_id }}">
			<input type="hidden" id="empresa_id" value="{{ $config->empresa_id }}">
			<div class="container">
				<div class="billing-details row">
					<div class="section-title col-md-12">
						<h3 class="title">Seus Dados</h3>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<input required class="input" type="text" value="{{ $cliente->razao_social }}" name="nome" placeholder="Nome">
							@if($errors->has('nome'))
							<br>
							<span class="invalid-feedback">{{ $errors->first('nome') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<input required class="input" type="email" value="{{ $cliente->email }}" name="email" placeholder="Email">
							@if($errors->has('email'))
							<br>
							<span class="invalid-feedback">{{ $errors->first('email') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<input class="input" type="password" name="senha" placeholder="Nova Senha" value="">
							@if($errors->has('senha'))
							<br>
							<span class="invalid-feedback">{{ $errors->first('senha') }}</span>
							@endif
						</div>
					</div>
				</div>
				<button type="submit" class="primary-btn order-submit">
					<i class="fa fa-check"></i>
					Salvar Cadastro
				</button>
				<a href="{{ route('loja.logoff', ['link='.$config->loja_id])}}" style="float: right;" class="btn btn-danger order-submit">
					<i class="fa fa-sign-out"></i>
					Sair
				</a>

			</div>
		</form>

		<hr>

		<div class="container">
			<div class="billing-details row">
				<div class="section-title col-md-12">
					<h3 class="title">Endereços</h3>

					<button class="btn btn-success" style="float: right;" onclick="novoEndereco()">
						<i class="fa fa-plus"></i> Novo endereço
					</button>
				</div>

				<div class="row">
					@foreach($cliente->enderecosEcommerce as $e)
					<div class="col-md-6">
						<div class="card">
							<h5>Rua: <strong>{{ $e->rua }}</strong></h5>
							<h5>Número: <strong>{{ $e->numero }}</strong></h5>
							<h5>Bairro: <strong>{{ $e->bairro }}</strong></h5>
							<h5>Cidade: <strong>{{ $e->cidade->info }}</strong></h5>
							<h5>Referência: <strong>{{ $e->referencia }}</strong></h5>
							<h5>CEP: <strong>{{ $e->cep }}</strong></h5>

							<div class="row">
								<div class="col-md-6">
									@if($e->padrao)
									<h4>Endereço padrão</h4>
									@endif
								</div>
								<div class="col-md-6">
									<button class="btn btn-warning" style="float: right;" onclick="editarEndereco('{{$e}}')">
										<i class="fa fa-edit"></i>
									</button>
								</div>
							</div>
							
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>

		<div class="container">
			<div class="billing-details row">
				<div class="section-title col-md-12">
					<h3 class="title">Pedidos</h3>

				</div>

				<div class="row">
					@foreach($cliente->pedidosEcommerce as $p)
					<div class="col-md-12" style="margin-top: 10px">
						<div class="card">
							<div class="col-md-3">
								Data: <strong>{{ __data_pt($p->created_at) }}</strong>
							</div>
							<div class="col-md-3">
								Valor total: <strong>R${{ __moeda($p->valor_total) }}</strong>
							</div>

							<div class="col-md-3">
								Valor frete: <strong>R${{ __moeda($p->valor_frete) }}</strong>
							</div>

							<div class="col-md-3">
								Desconto: <strong>R${{ __moeda($p->desconto) }}</strong>
							</div>

							<div class="row produtos">
								<br>
								<h5>Itens do pedido</h5>
								@foreach($p->itens as $i)
								<div class="row">
									<div class="col-md-2">
										<a href="{{ route('loja.produto-detalhe', [$i->produto->hash_ecommerce, 'link='.$config->loja_id])}}"><img src="{{ $i->produto->img }}" class="img">
										</a>
									</div>

									<div class="col-md-4">
										<a href="{{ route('loja.produto-detalhe', [$i->produto->hash_ecommerce, 'link='.$config->loja_id])}}"><h4>
											{{ $i->produto->nome }}
											@if($i->produtoVariacao)
											{{ $i->produtoVariacao->descricao }}
											@endif
										</h4></a>
									</div>

									<div class="col-md-2">
										<h5>Valor unitário: <strong>R${{ __moeda($i->valor_unitario) }}</strong></h5>
									</div>
									<div class="col-md-2">
										<h5>Quantidade: <strong>{{ number_format($i->quantidade, 0) }}</strong></h5>
									</div>
									<div class="col-md-2">
										<h5>Subtotal: <strong>R${{ __moeda($i->sub_total) }}</strong></h5>
									</div>
								</div>
								<hr>
								@endforeach
							</div>
							
							<div class="col-md-3">
								Tipo de pagamento: <strong>{{ strtoupper($p->tipo_pagamento) }}</strong>
							</div>

							<div class="col-md-3">
								Status de pagamento:
								@if($p->status_pagamento == 'approved')
								<strong class="text-success">PAGO</strong>
								@else
								<strong class="text-danger">PENDENTE</strong>
								@endif
							</div>

							<div class="col-md-3">
								<strong class="text-danger">#{{ $p->hash_pedido }}</strong>

							</div>

							@if($p->tipo_pagamento == 'pix' && $p->status_pagamento != 'approved')
							<div class="col-md-3">
								<a style="margin-top: -7px" href="{{ route('loja.nova-chavepix', ['link='.$config->loja_id.'&hash='.$p->hash_pedido]) }}" class="btn btn-primary btn-sm">Gerar nova chave pix</a>
							</div>
							@endif

							@if($p->tipo_pagamento == 'boleto')
							<div class="col-md-3">
								<a target="_blank" href="{{$p->link_boleto}}" class="btn btn-primary btn-sm">Imprimir boleto</a>
							</div>
							@endif
							@if($p->observacao)
							<div class="col-md-12">
								Observação: <strong>{{ $p->observacao }}</strong>
							</div>
							@endif
							<br>
						</div>
					</div>
					@endforeach
				</div>

			</div>
		</div>

	</div>
</div>

<div class="modal fade" id="modal-endereco" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form method="post" action="{{ route('loja.store-endereco', ['link='.$config->loja_id]) }}">
			@csrf
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Novo endereço</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="endereco_id" value="" id="endereco_id">
						<div class="col-md-4">
							<div class="form-group">
								<input required class="input cep" data-mask="00000-000" type="text" name="cep" placeholder="CEP" id="cep" value="{{ old('cep')}}">
							</div>
						</div>

						<div class="col-md-8">
							<div class="form-group">
								<input required class="input" type="text" name="rua" id="rua" placeholder="Rua" value="{{ old('rua')}}">
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<input required class="input" type="text" name="numero" id="numero" placeholder="Número" value="{{ old('numero')}}">
							</div>
						</div>

						<div class="col-md-8">
							<div class="form-group">
								<select required class="input" id="inp-cidade_id" type="text" name="cidade_id">
								</select>
								<input type="hidden" value="{{ old('cidade_id') }}" id="cidade_old_id">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<input required class="input" type="text" name="bairro" id="bairro" placeholder="Bairro" value="{{ old('bairro')}}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<input class="" type="checkbox" name="padrao" id="padrao"> 
								Padrão
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<input class="input" type="text" name="referencia" placeholder="Complemento" id="complemento" value="{{ old('referencia')}}">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Fehcar</button>
					<button type="submit" class="btn btn-danger">Salvar</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection

@section('js')
<script src="/assets/vendor/select2/js/select2.min.js"></script>

<script type="text/javascript">

	$(function(){
		let cidade_old_id = $('#cidade_old_id').val()
		if(cidade_old_id){
			findCidadeId(cidade_old_id)
		}
		$("#inp-cidade_id").select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar a cidade",
			width: "100%",
			dropdownParent: $("#modal-endereco"),
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
	});

	$(document).on("blur", ".cep", function () {
		let cep = $(this).val().replace(/[^0-9]/g,'')
		if(cep.length == 8){
			$.get('https://viacep.com.br/ws/'+cep+'/json')
			.done((res) => {
				console.log(res)
				findCidade(res.ibge)
				$('#rua').val(res.logradouro)
				$('#bairro').val(res.bairro)
				$('#complemento').val(res.complemento)
			})
			.fail((err) => {
				console.log(err)
			})
		}else{
			swal("Erro", "Informe o CEP corretamente", "error")
		}
	})

	function findCidade(codigo_ibge){
		$('#inp-cidade_id').html('')
		$.get(path_url + "api/cidadePorCodigoIbge/" + codigo_ibge)
		.done((res) => {
			var newOption = new Option(res.info, res.id, false, false);
			$('#inp-cidade_id').append(newOption).trigger('change');
		})
		.fail((err) => {
			console.log(err)
		})
	}

	function novoEndereco(){
		$('.modal-title').text('Novo endereço')
		$('#modal-endereco').modal('show')

		$('#endereco_id').val('')
		$('#rua').val('')
		$('#numero').val('')
		$('#bairro').val('')
		$('#cep').val('')
		$('#complemento').val('')
		$('#inp-cidade_id').html('')

	}

	function editarEndereco(endereco){
		endereco = JSON.parse(endereco)
		console.log(endereco)
		$('.modal-title').text('Editar endereço')
		$('#modal-endereco').modal('show')
		$('#endereco_id').val(endereco.id)

		$('#rua').val(endereco.rua)
		$('#numero').val(endereco.numero)
		$('#bairro').val(endereco.bairro)
		$('#cep').val(endereco.cep)
		$('#complemento').val(endereco.referencia)
		$('#padrao').prop('checked', endereco.padrao)
		
		findCidade(endereco.cidade.codigo)

	}
</script>
@endsection
