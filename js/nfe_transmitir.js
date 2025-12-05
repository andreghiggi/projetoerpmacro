function transmitir(id){
	console.clear()
	$.post(path_url + "api/nfe_painel/emitir", {
		id: id,
	})
	.done((success) => {
		swal("Sucesso", "NFe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
		.then(() => {
			if($('#nao_imprimir').length == 0){
				window.open(path_url + 'nfe/imprimir/' + id, "_blank")
			}
			setTimeout(() => {
				location.reload()
			}, 100)
		})
	})
	.fail((err) => {
		console.log(err)
		try{
			if(err.responseJSON.error){
				let o = err.responseJSON.error.protNFe.infProt
				swal("Algo deu errado", o.cStat + " - " + o.xMotivo, "error")
				.then(() => {
					location.reload()
				})
			}else{
				swal("Algo deu errado", err[0], "error")
			}
		}catch{
			if(err.responseJSON.message){
				swal("Algo deu errado", err.responseJSON.message, "error")
				.then(() => {
					location.reload()
				})
			}else{
				try{
					if(err.responseJSON.xMotivo){
						swal("Algo deu errado", err.responseJSON.xMotivo, "error")
						.then(() => {
							location.reload()
						})
					}else{
						if(err.responseJSON.error){
							swal("Algo deu errado", err.responseJSON.error, "error")
							.then(() => {
								location.reload()
							})
						}else{
							swal("Algo deu errado", err.responseJSON, "error")
							.then(() => {
								location.reload()
							})
						}
					}
				}catch{
					swal("Algo deu errado", err.responseJSON[0], "error")
					.then(() => {
						location.reload()
					})
				}
			}
		}
		
	})
}

var IDNFE = null
function cancelar(id, numero){
	IDNFE = id
	$('.ref-numero').text(numero)
	$('#modal-cancelar').modal('show')
}

function imprimir(id, numero){
	IDNFE = id
	$('.ref-numero').text(numero)
	$('#modal-print').modal('show')
}

function corrigir(id, numero){
	IDNFE = id
	$('.ref-numero').text(numero)
	$('#modal-corrigir').modal('show')
}

function gerarDanfe(tipo){
	if(tipo == 'danfe'){
		window.open('/nfe/imprimir/'+IDNFE)
	}else if(tipo == 'simples'){
		window.open('/nfe/danfe-simples/'+IDNFE)
	}else{
		window.open('/nfe/danfe-etiqueta/'+IDNFE)
	}
	$('#modal-print').modal('hide')
}

$('#btn-cancelar').click(() => {
	if(IDNFE != null){
		$.post(path_url + "api/nfe_painel/cancelar", {
			id: IDNFE,
			motivo: $('#inp-motivo-cancela').val()
		})
		.done((success) => {
			swal("Sucesso", "NFe cancelada " + success, "success")
			.then(() => {
				if($('#nao_imprimir').length == 0){
					window.open(path_url + 'nfe/imprimir-cancela/' + IDNFE, "_blank")
				}
				setTimeout(() => {
					location.reload()
				}, 100)
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
$('#btn-corrigir').click(() => {
	if(IDNFE != null){
		$.post(path_url + "api/nfe_painel/corrigir", {
			id: IDNFE,
			motivo: $('#inp-motivo-corrigir').val()
		})
		.done((success) => {
			swal("Sucesso", "NFe corrigida " + success, "success")
			.then(() => {
				if($('#nao_imprimir').length == 0){
					window.open(path_url + 'nfe/imprimir-correcao/' + IDNFE, "_blank")
				}
				setTimeout(() => {
					location.reload()
				}, 100)
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
	$.post(path_url + "api/nfe_painel/consultar", {
		id: id,
	})
	.done((success) => {
		console.log(success)
		swal("Sucesso", success, "success")
		.then(() => {
			location.reload()
		})
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
	$('#nfe_email_id').val(id)
	$('#inp-email').val('')
	$.get(path_url + "api/nfe_painel/find", {
		id: id,
	})
	.done((success) => {
		if(success.cliente){
			$('#inp-email').val(success.cliente.email)
		}
	})
	.fail((err) => {
		// console.log(err)
	})
}

$('.btn-enviar-email').click(() => {
	$('.modal-loading').modal('show')
})

