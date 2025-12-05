
$(document).on("click", ".campo-numero .decrementar", function () {
	var valor = parseInt( $(this).siblings('input').val());
	var eid = $(this).attr('sacola-eid');

	var newvalor = valor-1;
	if( newvalor >= 1 ) {
		$(this).siblings('input').val(newvalor);
	} else {
		$(this).siblings('input').val(1);
		newvalor = 1
	}

	$(this).siblings('input').trigger("change");
	$subTotal = $(this).closest('tr').find('.sub_total_item')

	atualizaTotal(eid, newvalor, $subTotal)

});

$(document).on("click", ".campo-numero .incrementar", function () {

	var eid = $(this).attr('sacola-eid');
	var valor = parseInt( $(this).siblings('input').val() );
	var newvalor = valor+1;
	$inpQtd = $(this).siblings('input')
	$subTotal = $(this).closest('tr').find('.sub_total_item')

	$.post(path_url + 'api/delivery-link/valida-estoque', { item_id: eid, quantidade: newvalor })
	.done(function(resEstoque) {

		$inpQtd.val(newvalor);
		$(this).siblings('input').trigger("change");
		atualizaTotal(eid, newvalor, $subTotal)
	})
	.fail(function(err) {
		// console.log(err)
		toastr.error('Estoque insuficiente!');
	})

});

function atualizaTotal(eid, quantidade, subTotal){
	$.post(path_url + 'api/delivery-link/atualiza-quantidade', { item_id: eid, quantidade: quantidade })
	.done(function(data) {
		// console.log(subTotal)
		subTotal.text("R$ " + convertFloatToMoeda(data.sub_total))
		$('.subtotal-valor').text("R$ " + convertFloatToMoeda(data.total_carrinho))
		if($('#btn-buscar-horarios')){
			setTimeout(() => {
				$('#btn-buscar-horarios').trigger('click')
			}, 10)
		}
	})
}

$( ".sacola-remover" ).click(function() {

	var eid = $(this).attr('sacola-eid');
	var tipo = $(this).attr('sacola-tipo');
	var modo = "remover";
	let msg = 'Deseja remover este produto?'
	if(tipo == 'servico'){
		msg = 'Deseja remover este serviço?'
	}
	if( confirm(msg) ) {

		$.post(path_url + 'api/delivery-link/remove-item', { item_id: eid })
		.done(function(data) {
				toastr.success("Item removido")

			$(".sacola-"+eid).fadeOut("800", function() {
				$(this).remove();
				$("#the_form").trigger("change");
				sacola_count();
			});
			$('.subtotal-valor').text("R$ " + convertFloatToMoeda(data.valor_total))
			
		});
	}
});


function convertMoedaToFloat(value) {
	if (!value) {
		return 0;
	}

	var number_without_mask = value.replaceAll(".", "").replaceAll(",", ".");
	return parseFloat(number_without_mask.replace(/[^0-9\.]+/g, ""));
}

function convertFloatToMoeda(value) {
	value = parseFloat(value)
	return value.toLocaleString("pt-BR", {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2
	});
}