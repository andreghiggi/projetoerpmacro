function selecionaEndereco(id){
	$('.borders').removeClass('bg-main')
	$('.end-'+id).addClass('bg-main')

	$.get(path_url + 'api/delivery-link/set-endereco', { 
		endereco_id: id, 
		carrinho_id: $('#inp-carrinho_id').val(), 
	}).done((res) => {

		$('.text-entrega').text("R$ " + convertFloatToMoeda(res.valor_frete))
		$('.text-total').text("R$ " + convertFloatToMoeda(res.valor_total))
	}).fail((err) => {
		console.log(err)
	})
	validaBtnFinish()
}


$(".pay-app").on("click", function (e) {
	$(this).addClass('active-div')
	$(".pay-entrega").removeClass('active-div')
	$(".div-pay-entrega").addClass('d-none')
	$(".div-pay-app").removeClass('d-none')
})
$(".pay-entrega").on("click", function (e) {
	$(this).addClass('active-div')
	$(".pay-app").removeClass('active-div')
	$(".div-pay-entrega").removeClass('d-none')
	$(".div-pay-app").addClass('d-none')
})

var _formaPagamento = null

function setFormaPagamento(str){
	$('.cartao-escolhido').text('')
	_formaPagamento = str
	$('.btn-pay').removeClass('active-btn')
	$('.btn-'+str).addClass('active-btn')

	$('.div-troco').addClass('d-none')
	if(str == 'Dinheiro'){
		$('.div-troco').removeClass('d-none')
	}else if(str == 'cartao-entrega'){
		_formaPagamento = null
		$('#modal-escolhe-cartao').modal('show')
	}
	else if(str == 'Pix pelo App'){
		$('#modal-pix').modal('show')
	}else if(str == 'Cartão pelo App'){
		$('#modal-cartao').modal('show')
	}
	validaBtnFinish()
}

function setCartao(str){
	_formaPagamento = str
	$('#modal-escolhe-cartao').modal('hide')
	$('.cartao-escolhido').text(str)
	validaBtnFinish()
}

$("#nao_precisa_troco").on("click", function (e) {
	if($(this).is(":checked")){
		$('#inp-troco_para').val(convertFloatToMoeda($('#inp-total').val()))
		validaBtnFinish()
	}
});

$("#inp-troco_para").on("blur", function (e) {
	validaBtnFinish()
})

$(".btn-pix").on("click", function (e) {
	let cpf = $('#inp-cpf').val()
	if(cpf.length == 14){
		finalizaPix(cpf)
	}else{
		swal("Alerta", "Informe um CPF válido", "warning")
	}
})

function validaBtnFinish(){
	let troco_para = convertMoedaToFloat($('#inp-troco_para').val())
	let valida = false
	if(_formaPagamento != null){
		valida = true
	}

	if(_formaPagamento == 'Dinheiro' && troco_para <= 0){
		valida = false
	}
	$('#inp-tipo_pagamento').val(_formaPagamento)
	setTimeout(() => {
		if(valida){
			$('.btn-finish').removeAttr('disabled')
		}else{
			$('.btn-finish').attr('disabled', true)
		}
	}, 50)
}


function finalizaPix(cpf){
	console.clear()

	$('.modal-loading').modal('show')
	$('#form-pix').submit()
}


$("#input-forma-pagamento").on("change", function (e) {
	let forma_pagamento = $(this).val()
	if(forma_pagamento == 'Pix pelo App'){

		let valida = validaCampos()
		setTimeout(() => {
			if(valida == 0){
				toastr.error("Existe campos obrigatórios em branco!");
			}else{
			//submit
			$('#mdpix').modal('show')
			let cpf = $("input[name='cpf']").val();
			$('#inp-cpf').val(cpf)
			$('#inp-observacao').val($(this).find("textarea[name=observacoes]").val())
		}
	}, 100)
	}
})

$('#enviarPedido').click(() => {
	let valida = validaCampos()
	setTimeout(() => {
		if(valida == 0){
			toastr.error("Existe campos obrigatórios em branco!");
		}else{
			$('#form-finalizar').submit()
		}
	}, 100)
})

function validaCampos(){
	let nome = $("input[name='nome']").val();
	let cpf = $("input[name='cpf']").val();
	let whatsapp = $("input[name='whatsapp']").val();
	let bairro_id = $("select[name='bairro_id']").val();
	let tipo = $("select[name='tipo']").val();
	let endereco_cep = $( "input[name='endereco_cep']" ).val();
	let endereco_numero = $( "input[name='endereco_numero']" ).val();
	let endereco_rua = $( "input[name='endereco_rua']" ).val();
	let endereco_referencia = $( "input[name='endereco_referencia']" ).val();
	let forma_pagamento = $( "select[name='forma_pagamento'] option:selected" ).val();
	let observacoes = $(this).find("textarea[name=observacoes]").val();
	let cupom = $( "input[name='cupom']" ).val();
	let forma_pagamento_informacao = $( "input[name='forma_pagamento_informacao']" ).val();
	let carrinho_id = $( "#inp-carrinho_id" ).val();
	let endereco_id = $("#endereco_id").val();
	let valida = 1
	if(!nome){
		$( "input[name='nome']" ).addClass('is-invalid')
		$( "input[name='nome']" ).prev().css('color', 'red')
		valida = 0
	}

	if(!whatsapp){
		$( "input[name='whatsapp']" ).addClass('is-invalid')
		$( "input[name='whatsapp']" ).prev().css('color', 'red')
		valida = 0
	}

	if(!endereco_rua && $('#tipo_entrega').val() == 'delivery'){
		$( "input[name='endereco_rua']" ).addClass('is-invalid')
		$( "input[name='endereco_rua']" ).prev().css('color', 'red')
		valida = 0
	}

	if(!endereco_numero && $('#tipo_entrega').val() == 'delivery'){
		$( "input[name='endereco_numero']" ).addClass('is-invalid')
		$( "input[name='endereco_numero']" ).prev().css('color', 'red')
		valida = 0
	}

	if(!endereco_cep && $('#tipo_entrega').val() == 'delivery'){
		$( "input[name='endereco_cep']" ).addClass('is-invalid')
		$( "input[name='endereco_cep']" ).prev().css('color', 'red')
		valida = 0
	}

	if(!bairro_id && $('#tipo_entrega').val() == 'delivery'){
		$( "select[name='bairro_id']" ).addClass('is-invalid')
		$( ".lbl-bairro" ).css('color', 'red')
		valida = 0
	}

	if(!forma_pagamento){
		$( "select[name='forma_pagamento']" ).addClass('is-invalid')
		$( ".lbl-forma-pagamento" ).css('color', 'red')
		valida = 0
	}

	return valida
}
