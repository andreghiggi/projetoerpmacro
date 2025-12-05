$('.btn-clone-caixa').on("click", function() {
	console.clear()
	var $elem = $(this)
	.closest(".row")
	.prev();

	var hasEmpty = false;

	$elem.find("input, select").each(function() {
		if (($(this).val() == "" || $(this).val() == null) && $(this).attr("type") != "hidden" && $(this).attr("type") != "file" && !$(this).hasClass("ignore")) {
			hasEmpty = true;
			console.log($(this))
		}
	});

	if (hasEmpty) {
		swal(
			"Atenção",
			"Preencha todos os campos antes de adicionar novos.",
			"warning"
			);
		return;
	}

	$("select.select2").select2("destroy");
	var $tr = $elem.find(".dynamic-form").first();
	var $clone = $tr.clone();

	$clone.show();

	$clone.find(".select2, .descricao, .valor_linha").val("");
	$elem.find('.appends').append($clone);

	setTimeout(() => {

		$(".select2").select2({
			language: "pt-BR",
			width: "100%",
			placeholder: "Selecione uma opção",

		});
		somaValores()
	}, 200);

})

$(function(){
	// somaValores()
	somaRestante()
})

$('.valor_linha').blur(() => {
	// somaValores()
	somaRestante()

})

$(document).on("blur", ".valor_linha", function() {
	// somaValores()
	somaRestante()
})

function somaValores(){
	let soma = 0
	$('.valor_linha').each(function(e){
		let v = convertMoedaToFloat($(this).val())
		soma += v
		$elem = $(this).closest(".dynamic-form").next();

		if(!$elem[0]){
			$elem2 = $(this).closest(".line-row").next();
			$elem2.find('.soma-valor').text("R$ " + convertFloatToMoeda(soma))
			soma = 0
		}

	})
}

function somaRestante(){
	let soma = 0
	$('.valor_linha').each(function(e){
		let v = convertMoedaToFloat($(this).val())
		soma += v
		$elem = $(this).closest(".dynamic-form").next();

		if(!$elem[0]){
			$elem2 = $(this).closest(".line-row").next();
			$total = $elem2.find('.valor_total').val()
			let res = $total - soma
			$elem2.find('.total-restante').text("R$ " + convertFloatToMoeda(res))
			soma = 0
		}
	})
	setTimeout(() => {
		validaBtnStore()
	}, 200);

}

function validaBtnStore(){
	let isValid = true
	$('.btn-store').attr('disabled', 1)
	$('.total-restante').each(function(e){
		let str = $(this).text().replace(/[^0-9,]*/g, '')
		if(convertMoedaToFloat(str) > 0){
			isValid = false
		}
	})

	setTimeout(() => {
		if(isValid){
			$('.btn-store').removeAttr('disabled')
		}
	}, 200);


}


$('.btn-clone').on("click", function() {
	console.clear()
	var $elem = $(this)
	.closest(".row")
	.prev();

	var hasEmpty = false;

	$elem.find("input, select").each(function() {
		if (($(this).val() == "" || $(this).val() == null) && $(this).attr("type") != "hidden" && $(this).attr("type") != "file" && !$(this).hasClass("ignore")) {
			hasEmpty = true;
		}
	});

	if (hasEmpty) {
		swal(
			"Atenção",
			"Preencha todos os campos antes de adicionar novos.",
			"warning"
			);
		return;
	}

	$(".select2-custom").select2("destroy");
	var $tr = $elem.find(".dynamic-form").first();
	var $clone = $tr.clone();

	$clone.show();

	$clone.find(".select2-custom, .descricao, .valor_linha").val("");
	$elem.find('.appends').append($clone);

	setTimeout(() => {

		$(".select2-custom").select2({
			language: "pt-BR",
			width: "100%",
			placeholder: "Selecione uma opção",

		});
		somaValores()
	}, 200);

})

$(function(){
	// somaValores()
	somaRestante()
})

$('.valor_linha').blur(() => {
	// somaValores()
	somaRestante()

})

$(document).on("blur", ".valor_linha", function() {
	// somaValores()
	somaRestante()
})

function somaValores(){
	let soma = 0
	$('.valor_linha').each(function(e){
		let v = convertMoedaToFloat($(this).val())
		soma += v
		$elem = $(this).closest(".dynamic-form").next();

		if(!$elem[0]){
			$elem2 = $(this).closest(".line-row").next();
			$elem2.find('.soma-valor').text("R$ " + convertFloatToMoeda(soma))
			soma = 0
		}

	})
}

function somaRestante(){
	let soma = 0
	$('.valor_linha').each(function(e){
		let v = convertMoedaToFloat($(this).val())
		soma += v
		$elem = $(this).closest(".dynamic-form").next();

		if(!$elem[0]){
			$elem2 = $(this).closest(".line-row").next();
			$total = $elem2.find('.valor_total').val()
			let res = $total - soma
			$elem2.find('.total-restante').text("R$ " + convertFloatToMoeda(res))
			soma = 0
		}
	})
	setTimeout(() => {
		validaBtnStore()
	}, 200);

}

function validaBtnStore(){
	let isValid = true
	$('.btn-store').attr('disabled', 1)
	$('.total-restante').each(function(e){
		let str = $(this).text().replace(/[^0-9,]*/g, '')
		if(convertMoedaToFloat(str) > 0){
			isValid = false
		}
	})

	setTimeout(() => {
		if(isValid){
			$('.btn-store').removeAttr('disabled')
		}
	}, 200);


}


