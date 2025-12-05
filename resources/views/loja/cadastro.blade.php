@extends('loja.default', ['title' => 'Cadastro'])
@section('css')
<link rel="stylesheet" type="text/css" href="/css/ecommerce_cadastro.css">
@endsection
@section('content')

<div class="section">
	<div class="container">
		<form class="row" method="post" action="{{ route('loja.cadastro-store') }}">
			@csrf
			<input type="hidden" name="link" value="{{ $config->loja_id }}">
			<input type="hidden" id="empresa_id" value="{{ $config->empresa_id }}">
			<div class="@if($carrinho == []) col-md-12 @else col-md-7 @endif">
				<!-- Billing Details -->
				<div class="billing-details row">
					<div class="section-title">
						<h3 class="title">Cadastro</h3>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<input required class="input" type="text" value="{{ old('nome')}}" name="nome" placeholder="Nome">
							@if($errors->has('nome'))
							<br>
							<span class="invalid-feedback">{{ $errors->first('nome') }}</span>
							@endif
						</div>
					</div>
					
					<div class="col-md-12">
						<div class="form-group">
							<input required class="input" type="email" id="email" name="email" placeholder="Email" value="{{ old('email')}}">
							@if($errors->has('email'))
							<br>
							<span class="invalid-feedback">{{ $errors->first('email') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<input required class="input" type="password" name="senha" placeholder="Senha" value="{{ old('senha')}}">
							@if($errors->has('senha'))
							<br>
							<span class="invalid-feedback">{{ $errors->first('senha') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<input required class="input" type="password" name="repita_senha" placeholder="Repita Senha" value="{{ old('repita_senha')}}">
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<input required class="input cep" data-mask="00000-000" type="text" name="cep" placeholder="CEP" value="{{ $carrinho ? $carrinho->cep : ''}}">
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
							<input required data-mask="00 00000-0000" class="input" type="tel" name="telefone" placeholder="Celular" value="{{ old('telefone') }}">
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<input class="input" type="text" name="referencia" placeholder="Complemento" id="complemento" value="{{ old('referencia')}}">
						</div>
					</div>
				</div>
				@if($carrinho == [])
				<div class="col-12">
					<div class="input-checkbox">
						<input type="checkbox" id="termos" value="1" name="termos">
						<label for="termos">
							<span></span>
							Eu li e aceito o <a style="color: red" href="#!" data-toggle="modal" data-target="#modal-termos-condicoes">termos e condições</a>
						</label>

						@if($errors->has('termos'))
						<br>
						<span class="invalid-feedback">{{ $errors->first('termos') }}</span>
						@endif
					</div>
				</div>
				<button type="submit" class="primary-btn order-submit">Confirmar Cadastro</button>
				@endif
			</div>
			

			<!-- Order Details -->
			@if($carrinho != [])
			<div class="col-md-5 billing-details">

				<div class="section-title text-center">
					<h3 class="title">Seu Pedido</h3>
				</div>
				<div class="order-summary">
					<div class="order-col">
						<div><strong>PRODUTO</strong></div>
						<div><strong>SUBTOTAL</strong></div>
					</div>
					<div class="order-products">
						@foreach($carrinho->itens as $i)
						<div class="order-col">
							<div>{{ number_format($i->quantidade, 0) }}x {{ $i->produto->nome }}</div>
							<div>R${{ __moeda($i->sub_total) }}</div>
						</div>
						@endforeach
					</div>
					<div class="order-col">
						<div>Entrega</div>
						<div><strong>R${{ __moeda($carrinho->valor_frete) }}</strong></div>
					</div>
					<div class="order-col">
						<div><strong>TOTAL</strong></div>
						<div><strong class="order-total">R${{ __moeda($carrinho->valor_total) }}</strong></div>
					</div>
				</div>

				<div class="input-checkbox">
					<input type="checkbox" id="termos" value="1" name="termos">
					<label for="termos">
						<span></span>
						Eu li e aceito o <a style="color: red" href="#!" data-toggle="modal" data-target="#modal-termos-condicoes">termos e condições</a>
					</label>

					@if($errors->has('termos'))
					<br>
					<span class="invalid-feedback">{{ $errors->first('termos') }}</span>
					@endif
				</div>

				<button type="submit" class="primary-btn order-submit">Confirmar Cadastro</button>

				<label style="margin-left: 5px; margin-top: 5px;">
					Já tenho cadastro <a style="color: red" href="{{ route('loja.login', ['link='.$config->loja_id])}}">fazer login</a>
				</label>
			</div>
			@endif
		</form>
	</div>
</div>

<div class="modal fade" id="modal-termos-condicoes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Termos e condições</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				{!! $config->termos_condicoes !!}
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fehcar</button>
			</div>
		</div>
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

		setTimeout(() => {
			let cep = $(".cep").val().replace(/[^0-9]/g,'')
			if(cep.length > 7){
				buscaCep()
			}
		}, 10)
	});

	$(document).on("blur", "#email", function () {
		let email = $(this).val()
		let empresa_id = $('#empresa_id').val()
		if(email){
			$.get(path_url + "api/ecommerce/valida-email", 
			{
				email: email,
				empresa_id: empresa_id
			})
			.done((res) => {
				console.log(res)
			})
			.fail((err) => {
				console.log(err)
				if(err.status == 402){
					swal("Erro", "Email já cadastrado no sistema", "error")
					$('#email').val('')
				}
			})
		}
	})

	$(document).on("blur", ".cep", function () {
		buscaCep()
	})

	function buscaCep(){
		let cep = $(".cep").val().replace(/[^0-9]/g,'')
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
	}

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

	function findCidadeId(id){
		$('#inp-cidade_id').html('')
		$.get(path_url + "api/cidadePorId/" + id)
		.done((res) => {
			var newOption = new Option(res.info, res.id, false, false);
			$('#inp-cidade_id').append(newOption).trigger('change');
		})
		.fail((err) => {
			console.log(err)
		})
	}
</script>
@endsection
