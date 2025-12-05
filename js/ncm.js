$(function(){
	$.get(path_url + "api/ncm/valida")
	.done((res) => {
		console.log(res)

	}).fail((err) => {

		if(err.status == 403){

			swal({
				buttons: ["Não carregar", "Carregar"],
				title: "Tabela NCM vazia",
				text: "Deseja carregar essa tabela com os dados pré definidos? isso pode demorar alguns segundos!",
				icon: "warning"
			}).then((x) => {
				if(x){
					$.get(path_url + "api/ncm/carregar")
					.done((res) => {

						swal("Sucesso", "Tabela importada!", "success")
						.then(() => {
							location.href = '/ncm'
						})

					}).fail((err) => {
						// console.log(err)
						swal("Erro", "Algo deu errado", "error")
					})
				}
			})
		}

		if(err.status == 404){
			swal("Erro", err.responseJSON, "error")
			console.log(err)
		}
	})
})