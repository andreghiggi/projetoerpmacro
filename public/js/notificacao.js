$(function(){
	// alert('oi')
	intervalVar = setInterval(() => {
		notificacoesPedido()
	}, 5000)
})

function notificacoesPedido(){

	$('.control-loading').remove("div");
	if($('#modal-notificacao').is(':visible')){
	}else{

		$.get(path_url + "api/notificacoes-pedido", {empresa_id: $('#empresa_id').val()})
		.done((success) => {

			var audio = new Audio('/audio/song3.wav');
			audio.addEventListener('canplaythrough', function() {
				audio.play();
			});
			$('#modal-notificacao').modal('show')
			$('#modal-notificacao .modal-body').html(success)
		})
		.fail((err) => {
			if(err.status != 401){
				// swal("erro", "erro ao buscar notificações", "error")
			}
		})
	}
	setTimeout(() => {
		$('.control-loading').add('<div class="modal-loading loading-class"></div>')
	}, 100)
}

$('body').on('click', '.btn-set-status', function () {
	let id = $(this).prev().val()

	$.post(path_url + "api/notificacoes-set-status", {id: id})
	.done((success) => {
		$('#modal-notificacao').modal('hide')
		if(success.tipo == 'fechar_mesa'){
			if(success.pedido){
				location.href = '/pedidos-cardapio/'+success.pedido.id
			}
		}

	})
	.fail((err) => {
		console.log(err)
	})
});