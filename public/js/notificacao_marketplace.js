
var intervalVar = null
$(function(){
	intervalVar = setInterval(() => {
		notificacoesPedidoDelivery()
	}, 5000)
})

function notificacoesPedidoDelivery(){

	$('.control-loading').remove("div");
	if($('#modal-notificacao-delivery').is(':visible')){
	}else{

		$.get(path_url + "api/notificacoes-delivery", {empresa_id: $('#empresa_id').val()})
		.done((success) => {
			if(success){
				var audio = new Audio('/audio/song3.wav');
				audio.addEventListener('canplaythrough', function() {
					audio.play();
				});
				$('#modal-notificacao-delivery').modal('show')
				$('#modal-notificacao-delivery .modal-body').html(success)
			}
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

$(document).on("focus", ".btn-confirmar", function () {
	var form = $(this).parents("form");
	var estado = $(this).closest(".card-footer").find("#estado");
	estado.val('aprovado')
	setTimeout(() => {
		form.submit();
	}, 10)
});

$(document).on("focus", ".btn-recusar", function () {
	var form = $(this).parents("form");
	var estado = $(this).closest(".card-footer").find("#estado");
	estado.val('cancelado')
	setTimeout(() => {
		form.submit();
	}, 10)
});

