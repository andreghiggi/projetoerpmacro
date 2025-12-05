@extends('loja.default', ['title' => 'Pagamento'])
@section('css')
<link rel="stylesheet" type="text/css" href="/css/ecommerce_pagamento.css">
@endsection
@section('content')

<div class="section">
	<div class="container">

		<div class="col-md-4 billing-details">
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
					<div><strong> {{ $carrinho->tipo_frete != 0 ? $carrinho->tipo_frete : ''}} R${{ __moeda($carrinho->valor_frete) }}</strong></div>
				</div>

				<div class="order-col">
					<div><strong>TOTAL</strong></div>
					<div><strong class="order-total">R${{ __moeda($carrinho->valor_total) }}</strong></div>
				</div>
			</div>

			@if($carrinho->endereco)
			<div class="section-title text-center">
				<h4 class="title">Endereço de entrega</h4>

				<h5>{{ $carrinho->endereco->info }}</h5>
			</div>
			@endif
			<label>Observação do pedido</label>
			<textarea class="form-control" id="observacao"></textarea>
		</div>

		<div class="col-md-8 billing-details">

			<div class="row header-pay">
				@if(in_array('Pix', $tiposPagamento))
				<div class="{{ $config->sizeColumn() }} select-pix select-pay" onclick="selectPay('pix')">PIX</div>
				@endif
				@if(in_array('Boleto', $tiposPagamento))
				<div class="col-md-{{ sizeof($tiposPagamento) == 2 ? '6' : '4'}} select-boleto select-pay" onclick="selectPay('boleto')">BOLETO</div>
				@endif
				@if(in_array('Cartão de credito', $tiposPagamento))
				<div class="col-md-{{ sizeof($tiposPagamento) == 2 ? '6' : '4'}} select-cartao select-pay" onclick="selectPay('cartao')">CARTÃO DE CRÉDITO</div>
				@endif
				@if(in_array('Depósito bancário', $tiposPagamento))
				<div class="col-md-{{ sizeof($tiposPagamento) == 2 ? '6' : '4'}} select-deposito select-pay" onclick="selectPay('deposito')">DEPÓSITO BANCÁRIO/TRANSFERÊNCIA</div>
				@endif
			</div>

			<div class="row body-pay">
				<div class="body body-pix d-none">
					<h4>Pagamento com PIX</h4>
					<div class="row">
						<form method="post" id="paymentFormPix" action="{{ route('loja.pagamento-pix', ['link='.$config->loja_id]) }}">
							@csrf
							<input type="hidden" name="observacao" class="observacao">
							<div class="col-md-6">
								<label>Nome</label>
								<input required value="" name="payerFirstName" data-checkout="payerFirstName" type="text" class="form-control">
							</div>

							<div class="col-md-6">
								<label>Sobre nome</label>
								<input required value="" name="payerLastName" data-checkout="payerLastName" type="text" class="form-control">
							</div>

							<div class="col-md-6">
								<label>Email</label>
								<input required value="" name="payerEmail" data-checkout="payerEmail" id="payerEmail" type="email" class="form-control">
							</div>

							<div class="col-md-3">
								<label>Tipo de documento</label>
								<select required name="docType" id="docType" data-checkout="docType" class="form-control">
								</select>
							</div>

							<div class="col-md-6">
								<label>Número do documento</label>
								<input required value="" name="docNumber" data-checkout="docNumber" type="tel" class="form-control cpf_cnpj">
							</div>

							<div class="col-md-6">
								<br>
								<button id="btn-pix" style="width: 100%; margin-top: 7px;" class="btn btn-success" type="submit">Pagar com PIX</button>
							</div>

						</form>

					</div>
				</div>

				<div class="body body-boleto d-none">
					<h4>Pagamento com BOLETO</h4>
					<div class="row">
						<form method="post" id="paymentFormBoleto" action="{{ route('loja.pagamento-boleto', ['link='.$config->loja_id]) }}">
							@csrf
							<input type="hidden" name="observacao" class="observacao">

							<div class="col-md-6">
								<label>Nome</label>
								<input required value="" name="payerFirstName" data-checkout="payerFirstName" type="text" class="form-control">
							</div>

							<div class="col-md-6">
								<label>Sobre nome</label>
								<input required value="" name="payerLastName" data-checkout="payerLastName" type="text" class="form-control">
							</div>

							<div class="col-md-6">
								<label>Email</label>
								<input required value="" name="payerEmail" data-checkout="payerEmail" id="payerEmail" type="email" class="form-control">
							</div>

							<div class="col-md-3">
								<label>Tipo de documento</label>
								<select required name="docType" id="docType2" data-checkout="docType" class="form-control">
								</select>
							</div>

							<div class="col-md-6">
								<label>Número do documento</label>
								<input required value="" name="docNumber" data-checkout="docNumber" type="tel" class="form-control cpf_cnpj">
							</div>

							<div class="col-md-6">
								<br>
								<button id="btn-boleto" style="width: 100%; margin-top: 7px;" class="btn btn-success" type="submit">Pagar com BOLETO</button>
							</div>

						</form>

					</div>
				</div>

				<div class="body body-cartao d-none">
					<h4>Pagamento com CARTÃO DE CRÉDITO</h4>

					<!-- pagamento cartao -->
					<div class="row">
						<form method="post" id="paymentFormCartao" action="{{ route('loja.pagamento-cartao', ['link='.$config->loja_id]) }}">
							@csrf
							<input type="hidden" name="observacao" class="observacao">

							<div class="col-md-8">
								<label>Titular do cartão</label>
								<input required id="cardholderName" data-checkout="cardholderName" type="text" class="form-control">
							</div>

							<div class="col-md-3">
								<label>Tipo de documento</label>
								<select required name="docType" id="docType3" data-checkout="docType" class="form-control">
								</select>
							</div>

							<div class="col-md-4">
								<label>Número do documento</label>
								<input required name="docNumber" data-checkout="docNumber" type="tel" class="form-control cpf_cnpj cpf-cartao">
							</div>

							<div class="col-md-6">
								<label>Email</label>
								<input required name="email" data-checkout="email" id="email" type="email" class="form-control">
							</div>

							<div class="col-md-5">
								<label>Número do cartão</label>
								<div class="row">
									<div class="col-md-10">
										<input required data-checkout="cardNumber" id="cardNumber" type="tel" class="form-control" data-mask="0000000000000000">
									</div>
									<div class="col-md-2">
										<img id="band-img" style="width: 30px;">
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<label>Parcelas</label>
								<select required name="installments" data-checkout="installments" id="installments" type="tel" class="form-control"></select>
							</div>

							<div class="col-md-3">
								<label>Código de segurança</label>
								<input required data-checkout="securityCode" id="securityCode" type="tel" class="form-control">
							</div>

							<div class="col-md-4">
								<label>Data de Vencimento</label>
								<div class="row">
									<div class="col-md-6">
										<input required placeholder="MM" data-checkout="cardExpirationMonth" id="cardExpirationMonth" type="tel" class="form-control" data-mask="00">
									</div>
									<div class="col-md-6">
										<input required placeholder="AA" data-checkout="cardExpirationYear" id="cardExpirationYear" type="tel" class="form-control" data-mask="00">
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<br>
								<button id="btn-cartao" style="width: 100%;" class="btn btn-success" type="submit">Pagar com CARTÃO DE CRÉDITO</button>
							</div>

							<div style="visibility: hidden" class="form-group">
								<select class="custom-select" id="issuer" name="issuer" data-checkout="issuer">
								</select>
							</div>

							<input style="visibility: hidden" name="paymentMethodId" id="paymentMethodId"/>
							<input style="visibility: hidden" name="transactionAmount" id="transactionAmount" value="{{$carrinho->valor_total}}" />
						</form>
					</div>
					<!-- fim pagamento cartao -->
				</div>

				<div class="body body-deposito d-none">
					<h4>Pagamento com Deposito bancário/transferência</h4>
					<div class="row">
						<form method="post" id="paymentFormDeposito" action="{{ route('loja.pagamento-deposito', ['link='.$config->loja_id]) }}">
							@csrf
							<input type="hidden" name="observacao" class="observacao">

							<div class="container">
								{!! $config->dados_deposito !!}
							</div>

							<div class="col-md-6">
								<br>
								<button id="btn-boleto" style="width: 100%; margin-top: 7px;" class="btn btn-success" type="submit">Pagar com Depósito</button>
							</div>

						</form>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('js')
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
<script type="text/javascript">
	$(function(){
		$('.select-pay').first().trigger('click')
		window.Mercadopago.setPublishableKey('{{ $config->mercadopago_public_key }}');
		window.Mercadopago.getIdentificationTypes();

		setTimeout(() => {
			let s = $('#docType').html()
			$('#docType2').html(s)
			$('#docType3').html(s)
		}, 2000)
	})

	function selectPay(tipo){
		$('.select-pay').removeClass('select')
		$('.select-'+tipo).addClass('select')

		$('.body').addClass('d-none')
		$('.body-'+tipo).removeClass('d-none')
	}

	$('#cardNumber').keyup(() => {
		let cardnumber = $('#cardNumber').val().replaceAll(" ", "");
		if (cardnumber.length >= 6) {
			let bin = cardnumber.substring(0,6);

			window.Mercadopago.getPaymentMethod({
				"bin": bin
			}, setPaymentMethod);
		}
	})

	function setPaymentMethod(status, response) {
		if (status == 200) {
			let paymentMethod = response[0];
			document.getElementById('paymentMethodId').value = paymentMethod.id;

			$('#band-img').attr("src", paymentMethod.thumbnail);
			getIssuers(paymentMethod.id);
		} else {
			alert(`payment method info error: ${response}`);
		}
	}

	function getIssuers(paymentMethodId) {
		window.Mercadopago.getIssuers(
			paymentMethodId,
			setIssuers
			);
	}

	function setIssuers(status, response) {
		if (status == 200) {
			let issuerSelect = document.getElementById('issuer');
			$('#issuer').html('');
			response.forEach( issuer => {
				let opt = document.createElement('option');
				opt.text = issuer.name;
				opt.value = issuer.id;
				issuerSelect.appendChild(opt);
			});

			getInstallments(
				document.getElementById('paymentMethodId').value,
				document.getElementById('transactionAmount').value,
				issuerSelect.value
				);
		} else {
			alert(`issuers method info error: ${response}`);
		}
	}

	function getInstallments(paymentMethodId, transactionAmount, issuerId){
		window.Mercadopago.getInstallments({
			"payment_method_id": paymentMethodId,
			"amount": parseFloat(transactionAmount),
			"issuer_id": parseInt(issuerId)
		}, setInstallments);
	}

	function setInstallments(status, response){
		if (status == 200) {
			document.getElementById('installments').options.length = 0;
			response[0].payer_costs.forEach( payerCost => {
				let opt = document.createElement('option');
				opt.text = payerCost.recommended_message;
				opt.value = payerCost.installments;
				document.getElementById('installments').appendChild(opt);
			});
		} else {
			alert(`installments method info error: ${response}`);
		}
	}

	doSubmit = false;
	document.getElementById('paymentFormCartao').addEventListener('submit', getCardToken);
	function getCardToken(event){
		event.preventDefault();
		if(!doSubmit){
			let docNumber = $('.cpf-cartao').val().replace(/[^0-9]/g,'')
			$('.cpf-cartao').val(docNumber)
			setTimeout(() => {
				let $form = document.getElementById('paymentFormCartao');
				window.Mercadopago.createToken($form, setCardTokenAndPay);
				return false;
			}, 50)
		}
	};

	function setCardTokenAndPay(status, response) {

		if (status == 200 || status == 201) {
			let form = document.getElementById('paymentFormCartao');
			let card = document.createElement('input');
			card.setAttribute('name', 'token');
			card.setAttribute('type', 'hidden');
			card.setAttribute('value', response.id);
			// console.log(response.id)
			form.appendChild(card);
			doSubmit=true;
			$('button').attr('disabled', true)
			form.submit();
		} else {
			alert("Verify filled data!\n"+JSON.stringify(response, null, 4));
		}
	};

	$('#observacao').focusout(() => {
		$('.observacao').val($('#observacao').val())
	})

</script>
@endsection
