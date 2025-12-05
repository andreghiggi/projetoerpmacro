

$(function(){
	setTimeout(() => {
		calculaItem()
	}, 200)
})

$('body').on('click', '.add-select', function () {
	calculaItem()
})

function calculaItem(){
	let valor_unitario = parseFloat($('#valor_unitario').val())

	let quantidade = $('#inp-quantidade').val()
	// console.log($(this).data('valor'))
	let vlAdicional = 0
	$(".add-select").each(function () {
		if($(this).is(":checked")){
			vlAdicional += parseFloat($(this).data('valor'))
		}
	})

	let valorItem = (vlAdicional + valor_unitario)*quantidade
	$('#valor-item').text('R$ ' + convertFloatToMoeda(valorItem))
	$('#valor_item').val(valorItem)
}


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

$('.pro-qty').click(() => {
	calculaItem()
})