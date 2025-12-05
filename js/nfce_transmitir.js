function transmitir(id){
	console.clear()
	$.post(path_url + "api/nfce_painel/emitir", {
		id: id,
	})
	.done((success) => {
		console.log(success)
		if(success.recibo == '' && success.contigencia){
			swal("Sucesso", "NFCe emitida em contigência  - chave: [" + success.chave + "]", "success")
			.then(() => {
				if($('#nao_imprimir').length == 0){
					window.open(path_url + 'nfce/imprimir/' + id, "_blank")
				}
				setTimeout(() => {
					location.reload()
				}, 100)
			})
		}else{
			swal("Sucesso", "NFCe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
			.then(() => {
				if($('#nao_imprimir').length == 0){
					window.open(path_url + 'nfce/imprimir/' + id, "_blank")
				}
				setTimeout(() => {
					location.reload()
				}, 100)
			})
		}
	})
	.fail((err) => {
		console.log(err)
		if(err.responseJSON.message){
			swal("Algo deu errado", err.responseJSON.message, "error")
			.then(() => {
				location.reload()
			})
		}else{
			swal("Algo deu errado", err.responseJSON, "error")
		}
	})
}

function transmitirContigencia(id){

	console.clear()
	$.post(path_url + "api/nfce_painel/transmitir-contigencia", {
		id: id,
	})
	.done((success) => {
		console.log(success)
		if(success.recibo == '' && success.contigencia){
			swal("Sucesso", "NFCe emitida em contigência  - chave: [" + success.chave + "]", "success")
			.then(() => {
				window.open(path_url + 'nfce/imprimir/' + id, "_blank")
				setTimeout(() => {
					location.reload()
				}, 100)
			})
		}else{
			swal("Sucesso", "NFCe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
			.then(() => {
				window.open(path_url + 'nfce/imprimir/' + id, "_blank")
				setTimeout(() => {
					location.reload()
				}, 100)
			})
		}
	})
	.fail((err) => {
		console.log(err)
		if(err.responseJSON.message){
			swal("Algo deu errado", err.responseJSON.message, "error")
			.then(() => {
				location.reload()
			})
		}else{
			swal("Algo deu errado", err.responseJSON, "error")
		}
	})
}

var IDNFE = null
function cancelar(id, numero){
	IDNFE = id
	$('.ref-numero').text(numero)
	$('#modal-cancelar').modal('show')
}

function corrigir(id, numero){
	IDNFE = id
	$('.ref-numero').text(numero)
	$('#modal-corrigir').modal('show')
}

$('#btn-cancelar').click(() => {

	if(IDNFE != null){
		$.post(path_url + "api/nfce_painel/cancelar", {
			id: IDNFE,
			motivo: $('#inp-motivo-cancela').val()
		})
		.done((success) => {
			swal("Sucesso", "NFe cancelada " + success, "success")
			.then(() => {
				location.reload()
			})
		})
		.fail((err) => {
			console.log(err)

			swal("Algo deu errado", err.responseJSON, "error")

		})
	}else{
		swal("Erro", "Nota não selecionada", "error")
	}
})


function consultar(id, numero){
	$.post(path_url + "api/nfce_painel/consultar", {
		id: id,
	})
	.done((success) => {
		swal("Sucesso", success, "success")
	})
	.fail((err) => {
		console.log(err)
		swal("Algo deu errado", err.responseJSON, "error")

	})
}

function enviarEmail(id, numero){
	$('.ref-numero').text(numero)
	$('#modal-email').modal('show')
	$('#inp-danfe').prop('checked', 1)
	$('#inp-xml').prop('checked', 1)
	IDNFE = id

}

$('#btn-enviar-email').click(() => {
	let email = $('#inp-email').val()
	let danfe = $('#inp-danfe').is(':checked') ? 1 : 0
	let xml = $('#inp-xml').is(':checked') ? 1 : 0
	let data = {
		email: email,
		id: IDNFE,
		danfe: danfe,
		xml: xml,
	}

	$.post(path_url + "api/nfce_painel/send-mail", data)
	.done((success) => {
		// console.log(success)
		swal("Sucesso", "Email enviado!", "success")
		$('#modal-email').modal('hide')
	})
	.fail((err) => {
		// console.log(err)
		swal("Erro", err.responseJSON, "error")
	})
})