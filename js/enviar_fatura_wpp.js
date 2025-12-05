var VENDAID = 0;
var TIPO = 0;
function enviarWpp(id, tipo){

	VENDAID = id
	TIPO = tipo
	$.get(path_url + "api/envio-fatura-wpp", {id: id, tipo: tipo})
	.done((data) => {
		// console.log(data)
		$('#modal-wpp-envio').modal('show')

		if(data.enviar_danfe_wpp_link == 1){
			$('#inp-enviar_danfe').attr('checked', true)
		}
		if(data.enviar_xml_wpp_link == 1){
			$('#inp-enviar_xml').attr('checked', true)
		}
		if(data.enviar_pedido_a4_wpp_link == 1){
			$('#inp-enviar_pedido_a4').attr('checked', true)
		}
		if(!data.telefone){
			swal("Alerta", "Telefone do cliente não cadastrado", "info")
		}
		$('#inp-telefone').val(data.telefone)
		$('#inp-mensagem').val(data.mensagem)
		$('.cliente_info').text(data.cliente_info)


	})
	.fail((err) => {
		console.log(err)
	})
}

$('.btn-enviar-wpp').click(() => {
	if(!$('#inp-mensagem').val()){
		toastr.error("Informe a mensagem")
		return
	}
	let telefone = $('#inp-telefone').val()

	if(telefone.length < 14){
		toastr.error("Informe o telefone corretamente")
		return
	}
	let data = {
		mensagem: $('#inp-mensagem').val(),
		telefone: telefone,
		enviar_danfe: $('#inp-enviar_danfe').is(':checked') ? 1 : 0,
		enviar_xml: $('#inp-enviar_xml').is(':checked') ? 1 : 0,
		enviar_pedido_a4: $('#inp-enviar_pedido_a4').is(':checked') ? 1 : 0,
		id: VENDAID,
		tipo: TIPO
	}


	$.post(path_url + "api/envio-fatura-wpp/create-files", data)
	.done((data) => {
		// console.log(data)
		telefone = telefone.replace(/\D/g, '');
		window.open(`https://wa.me/55${telefone}?text=${data}`, "_blank");

	})
	.fail((err) => {
		console.log(err)
	})
})

