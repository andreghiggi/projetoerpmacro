@extends('loja.default', ['title' => 'Pagamento'])
@section('css')
<style type="text/css">
	.w-100{
		width: 100%;
		margin-bottom: 10px;
	}

	.ml-2{
		border-left: 10px;
	}

	.select{
		border-bottom: 2px solid #D10024;
	}
	.header-pay{
		text-align: center;
	}
	.header-pay:hover{
		cursor: pointer;
	}
	.body-pay{
		margin-top: 20px;
	}
	.d-none{
		display: none;
	}
	label{
		margin-top: 10px;
		margin-bottom: -4px;
	}

</style>
@endsection
@section('content')

<div class="section">
	<div class="container">

		<div class="col-md-4 order-details">
			<div class="section-title text-center">
				<h4 class="title">Pedido <strong style="font-size: 14px; color: #D10024">#{{ $item->hash_pedido }}</strong></h4>
			</div>
			<div class="order-summary">
				<div class="order-col">
					<div><strong>PRODUTO</strong></div>
					<div><strong>SUBTOTAL</strong></div>
				</div>
				<div class="order-products">
					@foreach($item->itens as $i)
					<div class="order-col">
						<div>{{ number_format($i->quantidade, 0) }}x {{ $i->produto->nome }}</div>
						<div>R${{ __moeda($i->sub_total) }}</div>
					</div>
					@endforeach
				</div>
				<div class="order-col">
					<div>Entrega</div>
					<div><strong> {{ $item->tipo_frete != 0 ? $item->tipo_frete : ''}} R${{ __moeda($item->valor_frete) }}</strong></div>
				</div>

				<div class="order-col">
					<div><strong>TOTAL</strong></div>
					<div><strong class="order-total">R${{ __moeda($item->valor_total) }}</strong></div>
				</div>
			</div>

			@if($item->endereco && strlen(trim($item->endereco) > 10))
			<div class="section-title text-center">
				<h4 class="title">Endereço de entrega</h4>

				<h5>{{ $item->endereco }}</h5>
			</div>
			@endif

		</div>

		<div class="col-md-8 order-details">

			<div class="row body-pay">
				<div class="body body-pix">
					<h4>Pagamento com PIX</h4>
					<div class="row">
						<form method="post" id="paymentFormPix" action="{{ route('loja.pagamento-novo-pix', ['link='.$config->loja_id]) }}">
							@csrf
							<input type="hidden" value="{{ $item->id }}" name="pedido_id">
							<div class="col-md-6">
								<label>Nome</label>
								<input required value="{{ $item->nome }}" name="payerFirstName" data-checkout="payerFirstName" type="text" class="form-control">
							</div>

							<div class="col-md-6">
								<label>Sobre nome</label>
								<input required value="{{ $item->sobre_nome }}" name="payerLastName" data-checkout="payerLastName" type="text" class="form-control">
							</div>

							<div class="col-md-6">
								<label>Email</label>
								<input required value="{{ $item->email }}" name="payerEmail" data-checkout="payerEmail" id="payerEmail" type="email" class="form-control">
							</div>

							<div class="col-md-3">
								<label>Tipo de documento</label>
								<select required name="docType" id="docType" data-checkout="docType" class="form-control">
								</select>
							</div>

							<div class="col-md-6">
								<label>Número do documento</label>
								<input required value="{{ $item->numero_documento }}" name="docNumber" data-checkout="docNumber" type="tel" class="form-control cpf_cnpj">
							</div>

							<div class="col-md-6">
								<br>
								<button id="btn-pix" style="width: 100%; margin-top: 7px;" class="btn btn-success" type="submit">Pagar com PIX</button>
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

	})
</script>
@endsection
