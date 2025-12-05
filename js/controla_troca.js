TOTALOLD = 0
$(function(){
	TOTALOLD = $('#valor_total_old').val()

})

function comparaValor(){
	setTimeout(() => {
		let total = convertMoedaToFloat($('#inp-valor_total').val())
		TOTALOLD = parseFloat(TOTALOLD)

		if(total > TOTALOLD){
			$('.h-valor_pagar').removeClass('d-none')
			$('.h-valor_restante').addClass('d-none')
			$('.valor_pagar').text('R$ ' + convertFloatToMoeda(total - TOTALOLD))
			$('#inp-valor_pagar').val(total - TOTALOLD)
			$('#inp-valor_credito').val('0')
		}else if(total < TOTALOLD){
			$('.h-valor_pagar').addClass('d-none')
			$('.h-valor_restante').removeClass('d-none')
			$('.valor_restante').text('R$ ' + convertFloatToMoeda(TOTALOLD - total))
			$('#inp-valor_pagar').val('0')
			$('#inp-valor_credito').val(TOTALOLD - total)
		}else{
			$('.valor_pagar').text('R$ ' + convertFloatToMoeda(0))
			$('.h-valor_pagar').removeClass('d-none')
			$('.h-valor_restante').addClass('d-none')
			$('.valor_restante').text('R$ ' + convertFloatToMoeda(0))
			$('#inp-valor_pagar').val('0')
			$('#inp-valor_credito').val('0')
		}
	}, 500)
}

$("body").on("click", ".cards-categorias, .card-group", function () {
	comparaValor()
})

$("body").on("click", ".btn-add-item", function () {
	comparaValor()
})

$("body").on("click", ".btn-qtd", function () {
	comparaValor()
})

$("body").on('click', '.salvar_troca', function () {
	let cliente = $("#inp-cliente_id").val();
	if(!cliente){
		swal("Alerta", "Informe o cliente para finalizar a troca!", "warning")
		.then(() => {
			setTimeout(() => {
				$('#finalizar_troca .btn-close').trigger('click')
			}, 100)
		})
		
	}
})
$(".table-itens").on('click', '.btn-delete-row', function () {
	comparaValor()		
})

$("body").on('change', '#inp-tipo_pagamento', function () {
	let tipo_pagamento = $(this).val()
	let cliente = $("#inp-cliente_id").val();
	$('#salvar_venda').removeAttr("disabled")
	
	if(tipo_pagamento == '00' && !cliente){
		$(this).val('').change()
		swal("Alerta", "Informe o cliente!", "warning")
		$('#cliente').modal('show')
	}
});

$("body").on('click', '#btn-comprovante-troca', function () {
	$("#form-troca").submit()
})

$("#form-troca").on("submit", function (e) {

	e.preventDefault();
	const form = $(e.target);
	var json = $(this).serializeFormJSON();

	json.empresa_id = $('#empresa_id').val()
	json.usuario_id = $('#usuario_id').val()
	json.venda_id = $('#venda_id').val()
	json.tipo = $('#tipo').val()

	// console.log(json)
	// return;

	$.post(path_url + 'api/trocas/store', json)
	.done((success) => {
		// console.log(success)
		
		swal({
			title: "Sucesso",
			text: "Troca finalizada com sucesso, deseja imprimir o comprovante?",
			icon: "success",
			buttons: true,
			buttons: ["Não", "Sim"],
			dangerMode: true,
		}).then((isConfirm) => {
			if (isConfirm) {
				window.open(path_url + 'trocas/imprimir/' + success.id, "_blank")
			} else {
			}

			location.href = '/trocas';
		});

	}).fail((err) => {
		console.log(err)
	})
})