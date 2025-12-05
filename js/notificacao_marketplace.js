
var intervalVar = null
$(function(){
	intervalVar = setInterval(() => {
		notificacoesPedidoDelivery()
	}, 5000)
})

function notificacoesPedidoDelivery(){


	if($('#modal-notificacao-delivery').is(':visible')){
	}else{

		$.ajax({
			url: path_url + 'api/notificacoes-delivery',
			method: "GET",
			global: false,
			data: {empresa_id: $('#empresa_id').val()}
		}).done((success) => {
			if(success){
				var audio = new Audio('/audio/song3.wav');
				audio.addEventListener('canplaythrough', function() {
					audio.play();
				});
				$('#modal-notificacao-delivery').modal('show')
				$('#modal-notificacao-delivery .modal-body').html(success)
			}
		}).fail((err) => {
		});
	}
	
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

