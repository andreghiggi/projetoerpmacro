@extends('loja.default', ['title' => $pedido->tipo_pagamento == 'cartao' ? 'Pagamento finalizado' : 'Finalizando'])
@section('css')
<style type="text/css">
	input[type="file"] {
		display: none;
	}

	.custom-file-upload {
		border: 1px solid #ccc;
		display: inline-block;
		padding: 6px 12px;
		cursor: pointer;
		width: 400px;
	}
</style>
@endsection
@section('content')
<div class="section">
	<div class="container">
		<div class="row">

			<input type="hidden" value="{{$pedido->transacao_id}}" id="transacao_id" name="">
			<input type="hidden" value="{{$pedido->status_pagamento}}" id="status" name="">
			<input type="hidden" value="{{$pedido->tipo_pagamento}}" id="tipo_pagamento" name="">

			<h3>Valor total do pedido: <strong class="text-danger">R${{ __moeda($pedido->valor_total) }}</strong></h3>
			@if($pedido->tipo_pagamento == 'pix')
			<div class="row div-pix">
				<div class="col-md-12">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<img style="width: 400px; height: 400px;" src="data:image/jpeg;base64,{{$pedido->qr_code_base64}}"/>
					</div>		
				</div>
				<div class="col-md-11">
					<input type="text" readonly class="form-control" value="{{$pedido->qr_code}}" id="qrcode_input" />
				</div>
				<div class="col-md-1">
					<button class="btn" onclick="copy()">
						<i class="fa fa-copy"></i> Copiar
					</button>
				</div>
			</div>
			@elseif($pedido->tipo_pagamento == 'boleto')
			<a target="_blank" href="{{$pedido->link_boleto}}" class="btn btn-danger btn-boleto">
				<i class="fa fa-print"></i>
				Imprimir Boleto
			</a>
			<input type="hidden" value="{{$pedido->link_boleto}}" id="link_boleto" name="">

			@elseif($pedido->tipo_pagamento == 'deposito')
			{!! $config->dados_deposito !!}

			<form method="post" action="{{ route('loja.enviar-comprovante') }}" enctype="multipart/form-data">
				<br>
				@csrf
				<input type="hidden" name="link" value="{{ $config->loja_id }}">
				<input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
				<label for="file-upload" class="custom-file-upload">
					Selecione o comprovante
				</label>
				<input required id="file-upload" name="file" type="file" accept="image/*, .pdf" />

				<button class="btn btn-success" type="submit">Enviar</button>
				<br>
				<label class="text-danger" id="filename"></label>
			</form>

			@elseif($pedido->tipo_pagamento == 'cartao')
			<div class="row status-approved col-md-12">

				<h2 class="text-success" style="">
					<i class="fa fa-check"></i>
					PAGAMENTO APROVADO
				</h2>
				<a href="{{ route('loja.index', ['link='.$config->loja_id]) }}" class="btn btn-success">
					Tela inicial
				</a>
			</div>
			@endif

		</div>
	</div>
</div>

@endsection

@section('js')
<script type="text/javascript">
	var intervalVar = null

	function copy(){
		const inputTest = document.querySelector("#qrcode_input");

		inputTest.select();
		document.execCommand('copy');

		swal("", "Código pix copiado!!", "success")
	}

	if($('#status').val() != "approved" && $('#tipo_pagamento').val() == "pix"){
		intervalVar =setInterval(() => {
			let transacao_id = $('#transacao_id').val();
			$.get(path_url+'api/ecommerce/consulta-pix/', {transacao_id: transacao_id})
			.done((success) => {
				console.log(success)
				if(success == "approved"){
					// location.reload()
					clearInterval(intervalVar)
					$('.div-pix').css('display', 'none')
					$('.status-approved').css('display', 'block')
				}
			})
			.fail((err) => {
				console.log(err)
			})
		}, 2000)
	}

	$(function(){
		setTimeout(() => {
			@if($pedido->tipo_pagamento == 'boleto')
			window.open($('#link_boleto').val())
			@endif
		}, 200)
	})

	$('input[type=file]').change(() => {
		var filename = $('input[type=file]').val().replace(/.*(\/|\\)/, '');
		$('#filename').html(filename)
	})
</script>
@endsection