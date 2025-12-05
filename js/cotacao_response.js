
$('body').on('blur', '.value', function() {
	var value = convertMoedaToFloat($(this).val());
	var $subtotal = $(this).closest('td').next().find('input');
	var $qtd = $(this).closest('td').prev().find('input');

	let qtd = convertMoedaToFloat($qtd.val())

	$subtotal.val(convertFloatToMoeda(value*qtd));
	calcTotal()
})

$('body').on('blur', '.valor_parcela', function() {
	calcFatura()
})

$('body').on('blur', '#desconto', function() {
	calcTotal()
})

$('body').on('blur', '#valor_frete', function() {
	calcTotal()
})

function calcTotal(){
	var total = 0

	$(".subtotal").each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	let desconto = convertMoedaToFloat($('#desconto').val())
	let valor_frete = convertMoedaToFloat($('#valor_frete').val())

	setTimeout(() => {
		$('.total').html("R$ " + convertFloatToMoeda(total))
		$('.total-cotacao').html("R$ " + convertFloatToMoeda(total-desconto+valor_frete))
	}, 100)
}

function calcFatura(){
	var total = 0

	$(".valor_parcela").each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	setTimeout(() => {
		$('.total-fatura').html("R$ " + convertFloatToMoeda(total))
	}, 100)
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

$('.btn-add-tr').on("click", function () {
	console.clear()
	var $table = $(this)
	.closest(".row")
	.prev()
	.find(".table-dynamic");

	var hasEmpty = false;

	$table.find("input, select").each(function () {
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
    // $table.find("select.select2").select2("destroy");
    var $tr = $table.find(".dynamic-form").first();
    var $clone = $tr.clone();
    $clone.show();

    $clone.find("input,select").val("");
    $table.append($clone);
    

})