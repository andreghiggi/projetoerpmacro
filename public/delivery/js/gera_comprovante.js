$( ".muda-checkout" ).change(function() {
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
	let endereco_id = $( "#endereco_id" ).val();

	let js = {
		nome: nome,
		whatsapp: whatsapp,
		bairro_id: bairro_id,
		cpf: cpf,
		endereco_cep: endereco_cep,
		endereco_numero: endereco_numero,
		endereco_rua: endereco_rua,
		endereco_referencia: endereco_referencia,
		forma_pagamento: forma_pagamento,
		observacoes: observacoes,
		tipo: tipo,
		cupom: cupom,
		carrinho_id: carrinho_id,
		endereco_id: endereco_id,
		forma_pagamento_informacao: forma_pagamento_informacao
	}

	$.post(path_url + 'api/delivery-link/atualiza-carrinho', js).done((res) => {
		// console.clear()
		// console.log(res)
		if(res.endereco){
			let endereco = res.endereco
			if ( $("#endereco_id option[value="+endereco.id+"]").length == 0 ){
				var newOption = new Option(endereco.info, endereco.id, false, 1);
				$('#endereco_id').append(newOption);
			}else{
				$("#endereco_id option[value="+endereco.id+"]").text(endereco.info)
			}
		}
		// getEnderecos(res.endereco)

	}).fail((err) => {
		// console.log(err)
	})

	setTimeout(() => {
		atualizaComprovante()
	}, 100)
})

function getEnderecos(endereco){
	$('#endereco_id').html('')
	var newOption = new Option("Selecione...", '', false, 1);
	$('#endereco_id').append(newOption);
	// console.log(endereco)
	let carrinho_id = $( "#inp-carrinho_id" ).val();

	$.get(path_url + 'api/delivery-link/get-enderecos', {carrinho_id: carrinho_id}).done((enderecos) => {
		enderecos.map((x) => {
			// console.log(x)
			if ( $("#endereco_id option[value="+x.id+"]").length == 0 ){
				var newOption = new Option(x.info, x.id, 1, false);
				$('#endereco_id').append(newOption);
			}
		})
	}).fail((err) => {
		console.log(err)
	})

}

$('.botao-aplicar').click(() => {
	let cupom = $( "input[name='cupom']" ).val();
	if(cupom.length >= 1){
		let js = {
			cupom: cupom,
			empresa_id: $('#inp-empresa_id').val(),
			carrinho_id: $('#inp-carrinho_id').val(),
		}
		$.get(path_url + 'api/delivery-link/cupom', js).done((res) => {
			atualizaComprovante()

		}).fail((err) => {
			console.log(err)
			swal("Erro", err.responseText, "error")
			atualizaComprovante()
			
		})
	}else{
		swal("Alerta", "Informe o código do cupom!", "warning")
	}

})

function habilitaEndereco(){
	$('.elemento-entrega').css('display', 'block')
}

$('#btn-novo-endereco').click(() => {
	$( "select[name='bairro_id']" ).val('').change()
	$( "input[name='endereco_rua']" ).val('')
	$( "input[name='endereco_cep']" ).val('')
	$( "input[name='endereco_numero']" ).val('')
	$( "input[name='endereco_referencia']" ).val('')
	$( "#endereco_id" ).val('').change()
	habilitaEndereco()
})

$(function(){
	atualizaComprovante()
	setTimeout(() => {
		if($('#endereco_id').val()){
			$('#endereco_id').change()
		}
	}, 100)
})

function atualizaComprovante() {
	let carrinho_id = $( "#inp-carrinho_id" ).val();

	$.post(path_url + 'api/delivery-link/comprovante-carrinho', { carrinho_id: carrinho_id })
	.done(function( data ) {
		$( ".comprovante .content" ).html( data );
		$( window ).trigger("resize");
	});
}

$(document).on("change", "#endereco_id", function () {
	let endereco_id = $(this).val()
	if(endereco_id){
		$.get(path_url + 'api/delivery-link/find-endereco', { endereco_id: endereco_id })
		.done(function( data ) {
			// console.log(data)
			habilitaEndereco()
			$( "input[name='endereco_rua']" ).val(data.rua)
			$( "input[name='endereco_cep']" ).val(data.cep)
			$( "input[name='endereco_numero']" ).val(data.numero)
			$( "input[name='endereco_referencia']" ).val(data.referencia)
			$( "select[name='bairro_id']" ).val(data.bairro_id).change()
			$( "select[name='tipo']" ).val(data.tipo).change()
		});
	}else{
		$( "select[name='bairro_id']" ).val('').change()
		$( "input[name='endereco_rua']" ).val('')
		$( "input[name='endereco_cep']" ).val('')
		$( "input[name='endereco_numero']" ).val('')
		$( "input[name='endereco_referencia']" ).val('')
	}
})

