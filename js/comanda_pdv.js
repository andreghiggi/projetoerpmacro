$(function(){

})

var valorProduto = 0
var total = 0
var element = null
$("body").on("dblclick", ".line-product", function () {
	element = $(this)
	let produto_id = $(this).find('.produto_row').val()
	total = valorProduto = convertMoedaToFloat($(this).find('.value-unit').val())
	console.log($(this))
	if(produto_id){
		$.get(path_url + "api/produtos/get-adicionais-modal", 
		{ 
			produto_id: produto_id
		}).done((res) => {
			// console.log(res)
			if(res != ""){
				$('#adicionais-pdv-modal').modal('show')
				$('#adicionais-pdv-modal .modal-body').html(res)

				$('#adicionais-pdv-modal .valor-produto').html('R$ ' + convertFloatToMoeda(valorProduto))
				console.log($(this).find(".add"))
				$(this).find(".add").each(function (e, v) {
					$('.check-adicional-'+$(this).val()).prop("checked", 1)
				})

			}

		})
		.fail((err) => {
			console.log(err)
		})
	}
})

$("body").on("click", "#btn-save-adicionais", function () {
	$('.inputs-adicional').remove()
	var inputs = "<div class='inputs-adicional'>"
	var ids = []
	$(".check-adicional").each(function (e, v) {
		if($(this).is(":checked")){
			inputs += "<input class='add' type='hidden' value='"+$(this).attr('adicional-id')+"' />"
			ids.push($(this).attr('adicional-id'))
		}
	})
	// console.log(ids)
	inputs += "</div>"
	element.find('.adicionais').val(ids)

	element.append(inputs)
	let valor = convertMoedaToFloat($('.valor-produto').text())
	let quantidade = convertMoedaToFloat(element.find('.qtd_row').val())

	// console.log("valor", valor)
	element.find('.value-unit').val(convertFloatToMoeda(valor))
	element.find('.subtotal-item').val(convertFloatToMoeda(valor*quantidade))
	$('#adicionais-pdv-modal').modal('hide')
	if (typeof calcTotal === "function") {
		calcTotal()
	}

});

$("body").on("click", ".check-adicional", function () {
	let quantidade = convertMoedaToFloat(element.find('.qtd_row').val())
	let valor = parseFloat($(this).attr('adicional-valor'));

	if($(this).is(":checked")){
		total += valor
	}else{
		total -= valor
	}

	setTimeout(() => {
		$('#adicionais-pdv-modal .valor-produto').html('R$ ' + convertFloatToMoeda(total*quantidade))
	}, 100)
})

