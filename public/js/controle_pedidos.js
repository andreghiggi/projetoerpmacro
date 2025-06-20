var intervalVar = null
$(function(){
	getItens()
	intervalVar = setInterval(() => {
		getItens()
	}, 5000)
})

function getItens(){
	$.get(path_url + 'api/pedidos/itens-pendentes', { empresa_id: $('#empresa_id').val() })
	.done((success) => {
		$('.append').html(success)
	})
	.fail((err) => {
		console.log(err)
		swal("Ops", "erro ao buscar itens", "error")
	})
}

function openModal(){
	clearInterval(intervalVar)
}

