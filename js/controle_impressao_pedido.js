var timeout = 5
$(function(){
	if($('#timeout').length){
		timeout = $('#timeout').val()
	}
	$('.control-loading').remove()
	setInterval(() => {
		buscaPedidos()
	}, (timeout*1000))
})

function buscaPedidos(){
	let impressora_id = $('#impressora_id').val()

	// $.get(path_url + "api/impressao-pedido", 
	// { 
	// 	impressora_id: impressora_id
	// })
	// .done((data) => {
	// 	if(data.log){
	// 		let html = $('.logs').html()
	// 		$('.logs').html(data.log + html)
	// 		window.open(path_url+"impressao-pedido-comando?"+data.itens)
	// 	}

	// })
	// .fail((err) => {
	// 	console.log(err)
	// })

	$.ajax({
		url: path_url + 'api/impressao-pedido',
		method: "GET",
		global: false,
		data: {impressora_id: impressora_id}
	}).done((data) => {
		if(data.log){
			let html = $('.logs').html()
			$('.logs').html(data.log + html)
			window.open(path_url+"impressao-pedido-comando?"+data.itens)
		}
	}).fail((err) => {
		console.log(err)
	});
}

