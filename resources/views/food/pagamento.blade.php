@extends('food.default', ['title' => 'Pagamento'])

@section('content')
<div class="middle">
	<div class="container nopaddmobile">
		<div class="row rowtitle">
			<div class="col-md-12">
				<div class="title-icon">
					<span>Pagamento</span>

				</div>
				<div class="bread-box">
					<div class="bread">
						<a href="{{ route('food.index', ['link='.$config->loja_id]) }}"><i class="lni lni-home"></i></a>
						<span>/</span>
						<a href="{{ route('food.carrinho', ['link='.$config->loja_id]) }}">Meu carrinho</a>
						<span>/</span>
						<a href="">Pagamento</a>
					</div>
				</div>
			</div>
			<input type="hidden" value="{{ $carrinho->id }}" id="inp-carrinho_id">
			<input type="hidden" value="{{ $carrinho->empresa_id }}" id="inp-empresa_id">
			<div class="col-md-12 hidden-xs hidden-sm">
				<div class="clearline"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
			</div>
		</div>
		<div class="pedido">

			<div class="row">
				<div class="col-md-8">
					<div class="titler">
						<div class="row">
							<div class="col-md-12">
								<div class="title-line mt-0 pd-0">
									<i class="lni lni-user"></i>
									<span>Dados do cliente</span>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="elemento-usuario">
						<div class="row">
							<div class="col-md-12">
								<div class="form-field-default">
									<label>Nome completo:</label>
									<input type="text" class="muda-checkout" name="nome" placeholder="Nome:" value="{{ $cliente->razao_social }}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-field-default">
									<label>Whatsapp:</label>
									<input class="maskcel muda-checkout" type="text" name="whatsapp" placeholder="Whatsapp:" value="{{ $cliente->telefone }}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-field-default">
									<label>CPF(Somente números):</label>
									<input type="tel" name="cpf" placeholder="CPF:" class="maskcpf muda-checkout" value="{{ $cliente->cpf_cnpj }}">
								</div>
							</div>
						</div>
						
					</div>

					<input type="hidden" id="tipo_entrega" value="{{ $carrinho->tipo_entrega }}">

					@if($carrinho->tipo_entrega == 'delivery')
					<div class="titler mtminus">
						<div class="row">
							<div class="col-md-12">
								<div class="title-line mt-0 pd-0">
									<i class="lni lni-cart"></i>
									<span>Entrega</span>
								</div>
							</div>
						</div>
					</div>

					<div id="enderecoCompleto">
						<div class="elemento-forma-entrega">
							<div class="row">
								<div class="col-md-12">
									<div class="form-field-default">
										<label class="lbl-endereco">Endereço:</label>

										<div class="fake-select">
											<i class="lni lni-chevron-down"></i>
											<select id="endereco_id" name="endereco_id">
												<option value="">Selecione...</option>
												@foreach($enderecos as $e)
												<option @if($e->padrao) selected @endif value="{{ $e->id }}">
													{{ $e->info }}
												</option>
												@endforeach
											</select>
											<div class="clear"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<button type="button" id="btn-novo-endereco" class="botao-acao">
							<span>Novo endereço</span>
						</button>
						<br>
						<div class="elemento-entrega" style="display: none;">

							<div class="row">
								<div class="col-md-12">
									<div class="form-field-default">
										<label class="lbl-bairro">Bairro:</label>

										<div class="fake-select">
											<i class="lni lni-chevron-down"></i>
											<select id="input-forma-entrega" class="muda-checkout" name="bairro_id">
												<option value="" valortx="0">Selecione...</option>
												@foreach($bairros as $b)
												<option valortx="{{$b->valor_entrega}}" value="{{ $b->id }}">
													{{ $b->nome }}
												</option>
												@endforeach
											</select>
											<div class="clear"></div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-6">
									<div class="form-field-default">
										<label>CEP</label>
										<input class="maskcep muda-checkout" type="text" name="endereco_cep" placeholder="CEP" value="">
									</div>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-6">
									<div class="form-field-default">
										<label>Nº</label>
										<input type="text" name="endereco_numero" class="muda-checkout" placeholder="Nº" value="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-field-default">
										<label>Logradouro(rua,avenida...):</label>
										<input type="text" class="muda-checkout" name="endereco_rua" placeholder="Rua">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-field-default">
										<label>Ponto de referência</label>
										<input type="text" class="muda-checkout" name="endereco_referencia" placeholder="Referência" value="">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-field-default">
										<label>Tipo</label>
										<div class="fake-select">
											<i class="lni lni-chevron-down"></i>
											<select id="input-forma-entrega" name="tipo" class="muda-checkout">
												<option value="casa">Casa</option>
												<option value="trabalho">Trabalho</option>
											</select>
											<div class="clear"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					@endif
					<div class="titler mtminus">
						<div class="row">
							<div class="col-md-12">
								<div class="title-line mt-0 pd-0">
									<i class="lni lni-coin"></i>
									<span>Pagamento</span>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="elemento-forma-pagamento">
						<div class="row">
							<div class="col-md-12">
								<div class="form-field-default">
									<label class="lbl-forma-pagamento">Forma de pagamento:</label>
									<div class="fake-select">
										<i class="lni lni-chevron-down"></i>
										<select id="input-forma-pagamento" name="forma_pagamento" class="muda-checkout">
											<option value="">Selecione</option>
											@foreach($config->tipos_pagamento as $t)
											<option value="{{ $t }}">{{ $t }}</option>
											@endforeach
										</select>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="elemento-forma-pagamento-descricao" style="display: block;">
						<div class="row">
							<div class="col-md-12">
								<div class="form-field-default">
									<label>Deseja troco para:</label>
									<span class="form-tip" style="display: none;"></span>

									<input class="maskmoney muda-checkout" type="text" name="forma_pagamento_informacao" id="forma_pagamento_informacao" placeholder="Deixe em branco caso não precise" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-field-default">
								<span>Observações:</span>
								<textarea id="observacoes" rows="5" name="observacoes" class="muda-checkout" placeholder="Observações:"></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-9">
							<div class="form-field-default">
								<label>Cupom de desconto:</label>
								<input class="muda-checkout" type="text" name="cupom" placeholder="Código do cupom" value="">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-field-default">
								<label class="hidden-xs hidden-sm">&nbsp;</label>
								<span class="botao-acao botao-aplicar"><i class="lni lni-ticket"></i> Aplicar</span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
						</div>
					</div>
				</div>
				<div class="col-md-4 muda-comprovante">
					<div class="titler titlerzero">
						<div class="row">
							<div class="col-md-12">
								<div class="title-line mt-0 pd-0">
									<i class="lni lni-ticket-alt"></i>
									<span>Resumo do pedido</span>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
					<div id="sticky-wrapper" class="sticky-wrapper"><div class="comprovante-parent grudado-desktop" style="width: 360px;">
						<div class="comprovante">
							<div class="content">
							</div>
						</div>

					</div></div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="pedido-actions" style="margin-top: 30px;">

				<input type="hidden" name="formdata" value="1">
				<div class="row">
					<div class="col-md-3 col-xs-5 col-sm-5">
						<a class="back-button" href="{{ route('food.carrinho', ['link='.$config->loja_id]) }}"><i class="lni lni-arrow-left"></i> <span>Alterar</span></a>
					</div>
					<div class="col-md-6 hidden-xs hidden-sm"></div>
					<form method="post" id="form-finalizar" action="{{ route('food.finalizar-pagamento') }}" class="col-md-3 col-xs-7 col-sm-7">
						@csrf
						<input type="hidden" name="link" value="{{ $config->loja_id }}" />

						<button type="button" id="enviarPedido" class="botao-acao"><i class="lni lni-enter"></i> <span>Concluir Pedido</span></button>
					</form>
				</div>
			</div>

		</div>
	</div>
</div>

@include('food.partials.modal_pix')

@section('js')
<script type="text/javascript" src="/delivery/js/gera_comprovante.js"></script>
<script type="text/javascript" src="/delivery/js/pagamento.js"></script>
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>

<script type="text/javascript">
	@if($pedidoHoje != null)
	swal({
		title: "Atenção", 
		text: "Você já realizou um pedido hoje, deseja comprar novamente?", 
		icon: "warning", 
		buttons: true,
		buttons: ["Ver meus pedidos", "Sim"],
		dangerMode: true
	}).then((confirmed) => {
		if (confirmed) {

		}else{
			location.href = '{{ route('food.conta', ['link='.$config->loja_id]) }}'
		}
	});
	@endif
</script>

@endsection
@endsection