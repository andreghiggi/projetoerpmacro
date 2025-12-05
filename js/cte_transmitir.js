function transmitir(id){
	console.clear()
	$.post(path_url + "api/cte_painel/emitir", {
		id: id,
	})
	.done((success) => {
		swal("Sucesso", "CTe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
		.then(() => {
			window.open(path_url + 'cte/imprimir/' + id, "_blank")
			setTimeout(() => {
				location.reload()
			}, 100)
		})
	})
	.fail((err) => {
		console.log(err.responseJSON)
		try{
			if(err.responseJSON.error){
				swal("Algo deu errado", err.responseJSON.error, "error")
				.then(() => {
					location.reload()
				})
			}else{
				swal("Algo deu errado", err.responseJSON.message, "error")
			}
		}catch{

			try{
				swal("Algo deu errado", err.responseJSON, "error")
				.then(() => {
					location.reload()
				})
			}catch{
				swal("Algo deu errado", err.responseJSON[0], "error")
				.then(() => {
					location.reload()
				})
			}
		}
		
	})
}

var IDCTe = null
function cancelar(id, numero){
	IDCTe = id
	$('.ref-numero').text(numero)
	$('#modal-cancelar').modal('show')
}

function corrigir(id, numero){
	IDCTe = id
	$('.ref-numero').text(numero)
	$('#modal-corrigir').modal('show')
}

$('#btn-cancelar').click(() => {
	if(IDCTe != null){
		$.post(path_url + "api/cte_painel/cancelar", {
			id: IDCTe,
			motivo: $('#inp-motivo-cancela').val()
		})
		.done((success) => {
			swal("Sucesso", success, "success")
			.then(() => {
				window.open(path_url + 'cte/imprimir-cancela/' + IDCTe, "_blank")
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
	if(IDCTe != null){
		$.post(path_url + "api/cte_painel/corrigir", {
			id: IDCTe,
			motivo: $('#inp-motivo-corrigir').val(),
			campo: $('#inp-campo').val(),
			grupo: $('#inp-grupo').val(),
		})
		.done((success) => {
			swal("Sucesso", success, "success")
			.then(() => {
				window.open(path_url + 'cte/imprimir-correcao/' + IDCTe, "_blank")
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
	$.post(path_url + "api/cte_painel/consultar", {
		id: id,
	})
	.done((success) => {
		swal("Sucesso", success, "success")
	})
	.fail((err) => {
		
		swal("Algo deu errado", err.responseJSON, "error")

	})
}