function transmitir(id){
	console.clear()
	$.post(path_url + "api/nfse/transmitir", {
		id: id,
	})
	.done((success) => {

		swal("Sucesso", success.mensagem, "success").then(() => {
			location.reload()
		})

	}).fail((err) => {
		console.log(err)
		if(err.status == 404){
			let json = err.responseJSON

			let motivo = json.mensagem
			let erros = json.erros

			if(erros){
				erros.map((e) => {
					motivo += e.erro
				})
			}

			let icon = "error"
			let title = "Algo deu errado"

			swal({
				title: title,
				text: motivo,
				icon: icon,
				buttons: ["Fechar"],
				dangerMode: true,
			})
		}else{
			swal("Algo deu errado", err.responseJSON, "error")
		}
	})
}

function consultar(id){
	console.clear()
	$.post(path_url + "api/nfse/consultar", {
		id: id,
	})
	.done((success) => {
		swal("Sucesso", success.mensagem, "success").then(() => {
			window.open(success.link_pdf)
		})
	}).fail((err) => {
		console.log(err)
		try{
			swal("Erro", err.responseJSON.mensagem, "error").then(() => {
				location.reload()
			})
		}catch{
			swal("Erro", "Erro consulte o console", "error")
		}
	})
}

$('#btn-cancelar').click(() => {
	console.clear()

	$.post(path_url + "api/nfse/cancelar", {
		id: IDNFSE,
		motivo: $('#inp-motivo-cancela').val()
	})
	.done((success) => {
		swal("Sucesso", e.mensagem, "success")
	}).fail((err) => {
		console.log(err)
		try{
			let json = JSON.parse(err.responseJSON)
			swal("Erro", json.mensagem, "error").then(() => {
				location.reload()
			})
		}catch{
			try{
				swal("Erro", err.responseJSON.mensagem, "error")
			}catch{
				swal("Erro", err.responseJSON, "error")
			}
		}
	})
})

var IDNFSE = null
function cancelar(id, numero){
	IDNFSE = id
	$('.ref-numero').text(numero)
	$('#modal-cancelar').modal('show')
}
