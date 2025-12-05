$(function(){
	calculaTotal()
})

$("body").on("blur", "input", function () {
	calculaTotal()
})

$("body").on("blur", "#inp-percentual_produtos", function () {
	calculaValorProdutos()
})

$("body").on("blur", ".table-produtos .qtd, .table-produtos .valor_unitario", function () {
	let somaCusto = 0
	let somaFinal = 0
	let percentual = $("#inp-percentual_produtos").val()

	$qtd = $(this).closest('tr').find('.qtd');
	$vlUnit = $(this).closest('tr').find('.valor_unitario');
	$subTotal = $(this).closest('tr').find('.sub_total');

	let qtd = convertMoedaToFloat($qtd.val())

	let vlUnit = convertMoedaToFloat($vlUnit.val())
	$subTotal.val(convertFloatToMoeda(qtd*vlUnit))

	$vlUnitFinal = $(this).closest('tr').find('.valor_unitario_final');
	$subTotalFinal = $(this).closest('tr').find('.sub_total_final');

	$vlUnitFinal.val(convertFloatToMoeda(vlUnit+(vlUnit*(percentual/100))))
	let vlUnitFinal = convertMoedaToFloat($vlUnitFinal.val())
	$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))

	setTimeout(() => {

		$(".table-produtos tr").each(function () {
			somaCusto += convertMoedaToFloat($(this).find('.sub_total').val())
			somaFinal += convertMoedaToFloat($(this).find('.sub_total_final').val())
		})
		$(".table-produtos .soma-custo").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".table-produtos .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))
	}, 100)
})

$("body").on("blur", ".table-produtos .valor_unitario_final", function () {
	let vlUnitFinal = convertMoedaToFloat($(this).val())
	$subTotalFinal = $(this).closest('td').next().find('input');
	$qtd = $(this).closest('td').prev().prev().prev().prev().find('input');
	let qtd = convertMoedaToFloat($qtd.val())
	$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))

	let somaFinal = 0

	$(".table-produtos .sub_total_final").each(function () {
		somaFinal += convertMoedaToFloat($(this).val())
	})

	setTimeout(() => {
		$(".table-produtos .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))
	}, 100)
})

function calculaValorProdutos(){
	let percentual = $("#inp-percentual_produtos").val()
	let somaCusto = 0
	let somaFinal = 0
	$(".table-produtos tbody tr").each(function () {
		$qtd = $(this).find('.qtd')
		let qtd = convertMoedaToFloat($qtd.val())

		$vlUnit = $(this).find('.valor_unitario')
		let vlUnit = convertMoedaToFloat($vlUnit.val())

		$subTotal = $(this).find('.sub_total')
		$subTotal.val(convertFloatToMoeda(qtd*vlUnit))
		somaCusto += qtd*vlUnit
		setTimeout(() => {
			$vlUnitFinal = $(this).find('.valor_unitario_final')

			$vlUnitFinal.val(convertFloatToMoeda(vlUnit+(vlUnit*(percentual/100))))
			let vlUnitFinal = convertMoedaToFloat($vlUnitFinal.val())

			$subTotalFinal = $(this).find('.sub_total_final')
			$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))
			somaFinal += qtd*vlUnitFinal

		}, 10)
	})

	setTimeout(() => {
		$(".table-produtos .soma-custo").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".table-produtos .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))

	}, 100)
}

//fim produtos

$("body").on("blur", "#inp-percentual_servicos", function () {
	calculaValorServicos()
})

$("body").on("blur", ".table-servicos .qtd, .table-servicos .valor_unitario", function () {
	let somaCusto = 0
	let somaFinal = 0
	let percentual = $("#inp-percentual_servicos").val()

	$qtd = $(this).closest('tr').find('.qtd');
	$vlUnit = $(this).closest('tr').find('.valor_unitario');
	$subTotal = $(this).closest('tr').find('.sub_total');

	let qtd = convertMoedaToFloat($qtd.val())

	let vlUnit = convertMoedaToFloat($vlUnit.val())
	$subTotal.val(convertFloatToMoeda(qtd*vlUnit))

	$vlUnitFinal = $(this).closest('tr').find('.valor_unitario_final');
	$subTotalFinal = $(this).closest('tr').find('.sub_total_final');

	$vlUnitFinal.val(convertFloatToMoeda(vlUnit+(vlUnit*(percentual/100))))
	let vlUnitFinal = convertMoedaToFloat($vlUnitFinal.val())
	$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))

	setTimeout(() => {

		$(".table-servicos tr").each(function () {
			somaCusto += convertMoedaToFloat($(this).find('.sub_total').val())
			somaFinal += convertMoedaToFloat($(this).find('.sub_total_final').val())
		})
		$(".table-servicos .soma-custo").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".table-servicos .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))
	}, 100)
})

$("body").on("blur", ".table-servicos .valor_unitario_final", function () {
	let vlUnitFinal = convertMoedaToFloat($(this).val())
	$subTotalFinal = $(this).closest('td').next().find('input');
	$qtd = $(this).closest('td').prev().prev().prev().prev().find('input');
	let qtd = convertMoedaToFloat($qtd.val())
	$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))

	let somaFinal = 0

	$(".table-servicos .sub_total_final").each(function () {
		somaFinal += convertMoedaToFloat($(this).val())
	})

	setTimeout(() => {
		$(".table-servicos .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))
	}, 100)
})

function calculaValorServicos(){
	let percentual = $("#inp-percentual_servicos").val()
	let somaCusto = 0
	let somaFinal = 0
	$(".table-servicos tbody tr").each(function () {
		$qtd = $(this).find('.qtd')
		let qtd = convertMoedaToFloat($qtd.val())

		$vlUnit = $(this).find('.valor_unitario')
		let vlUnit = convertMoedaToFloat($vlUnit.val())

		$subTotal = $(this).find('.sub_total')
		$subTotal.val(convertFloatToMoeda(qtd*vlUnit))
		somaCusto += qtd*vlUnit
		setTimeout(() => {
			$vlUnitFinal = $(this).find('.valor_unitario_final')

			$vlUnitFinal.val(convertFloatToMoeda(vlUnit+(vlUnit*(percentual/100))))
			let vlUnitFinal = convertMoedaToFloat($vlUnitFinal.val())

			$subTotalFinal = $(this).find('.sub_total_final')
			$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))
			somaFinal += qtd*vlUnitFinal

		}, 10)
	})

	setTimeout(() => {
		$(".table-servicos .soma-custo").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".table-servicos .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))

	}, 100)
}
//fim serviços

$("body").on("blur", "#inp-percentual_servicos_terceiro", function () {
	calculaValorServicosTerceiro()
})

$("body").on("blur", ".table-servicos-terceiro .qtd, .table-servicos-terceiro .valor_unitario", function () {
	let somaCusto = 0
	let somaFinal = 0
	let percentual = $("#inp-percentual_servicos_terceiro").val()

	$qtd = $(this).closest('tr').find('.qtd');
	$vlUnit = $(this).closest('tr').find('.valor_unitario');
	$subTotal = $(this).closest('tr').find('.sub_total');

	let qtd = convertMoedaToFloat($qtd.val())

	let vlUnit = convertMoedaToFloat($vlUnit.val())
	$subTotal.val(convertFloatToMoeda(qtd*vlUnit))

	$vlUnitFinal = $(this).closest('tr').find('.valor_unitario_final');
	$subTotalFinal = $(this).closest('tr').find('.sub_total_final');

	$vlUnitFinal.val(convertFloatToMoeda(vlUnit+(vlUnit*(percentual/100))))
	let vlUnitFinal = convertMoedaToFloat($vlUnitFinal.val())
	$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))

	setTimeout(() => {

		$(".table-servicos-terceiro tr").each(function () {
			somaCusto += convertMoedaToFloat($(this).find('.sub_total').val())
			somaFinal += convertMoedaToFloat($(this).find('.sub_total_final').val())
		})
		$(".table-servicos-terceiro .soma-custo").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".table-servicos-terceiro .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))
	}, 100)
})

$("body").on("blur", ".table-servicos-terceiro .valor_unitario_final", function () {
	let vlUnitFinal = convertMoedaToFloat($(this).val())
	$subTotalFinal = $(this).closest('td').next().find('input');
	$qtd = $(this).closest('td').prev().prev().prev().prev().find('input');
	let qtd = convertMoedaToFloat($qtd.val())
	$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))

	let somaFinal = 0

	$(".table-servicos-terceiro .sub_total_final").each(function () {
		somaFinal += convertMoedaToFloat($(this).val())
	})

	setTimeout(() => {
		$(".table-servicos-terceiro .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))
	}, 100)
})
function calculaValorServicosTerceiro(){
	let percentual = $("#inp-percentual_servicos_terceiro").val()
	let somaCusto = 0
	let somaFinal = 0
	$(".table-servicos-terceiro tbody tr").each(function () {
		$qtd = $(this).find('.qtd')
		let qtd = convertMoedaToFloat($qtd.val())

		$vlUnit = $(this).find('.valor_unitario')
		let vlUnit = convertMoedaToFloat($vlUnit.val())

		$subTotal = $(this).find('.sub_total')
		$subTotal.val(convertFloatToMoeda(qtd*vlUnit))
		somaCusto += qtd*vlUnit
		setTimeout(() => {
			$vlUnitFinal = $(this).find('.valor_unitario_final')

			$vlUnitFinal.val(convertFloatToMoeda(vlUnit+(vlUnit*(percentual/100))))
			let vlUnitFinal = convertMoedaToFloat($vlUnitFinal.val())
			$subTotalFinal = $(this).find('.sub_total_final')
			$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))
			somaFinal += qtd*vlUnitFinal

		}, 10)
	})

	setTimeout(() => {
		$(".table-servicos-terceiro .soma-custo").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".table-servicos-terceiro .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))

	}, 100)
}

//fim serviços terceiro

$("body").on("blur", "#inp-percentual_custos_adm", function () {
	calculaValorCustosAdm()
})

$("body").on("blur", ".table-custos-adm .qtd, .table-custos-adm .valor_unitario", function () {
	let somaCusto = 0
	let somaFinal = 0
	let percentual = $("#inp-percentual_custos_adm").val()

	$qtd = $(this).closest('tr').find('.qtd');
	$vlUnit = $(this).closest('tr').find('.valor_unitario');
	$subTotal = $(this).closest('tr').find('.sub_total');

	let qtd = convertMoedaToFloat($qtd.val())

	let vlUnit = convertMoedaToFloat($vlUnit.val())
	$subTotal.val(convertFloatToMoeda(qtd*vlUnit))

	$vlUnitFinal = $(this).closest('tr').find('.valor_unitario_final');
	$subTotalFinal = $(this).closest('tr').find('.sub_total_final');

	$vlUnitFinal.val(convertFloatToMoeda(vlUnit+(vlUnit*(percentual/100))))
	let vlUnitFinal = convertMoedaToFloat($vlUnitFinal.val())
	$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))

	setTimeout(() => {

		$(".table-custos-adm tr").each(function () {
			somaCusto += convertMoedaToFloat($(this).find('.sub_total').val())
			somaFinal += convertMoedaToFloat($(this).find('.sub_total_final').val())
		})
		$(".table-custos-adm .soma-custo").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".table-custos-adm .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))
	}, 100)
})

$("body").on("blur", ".table-custos-adm .valor_unitario_final", function () {
	let vlUnitFinal = convertMoedaToFloat($(this).val())
	$subTotalFinal = $(this).closest('td').next().find('input');
	$qtd = $(this).closest('td').prev().prev().prev().prev().find('input');
	let qtd = convertMoedaToFloat($qtd.val())
	$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))

	let somaFinal = 0

	$(".table-custos-adm .sub_total_final").each(function () {
		somaFinal += convertMoedaToFloat($(this).val())
	})

	setTimeout(() => {
		$(".table-custos-adm .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))
	}, 100)
})
function calculaValorCustosAdm(){
	let percentual = $("#inp-percentual_custos_adm").val()
	let somaCusto = 0
	let somaFinal = 0
	$(".table-custos-adm tbody tr").each(function () {
		$qtd = $(this).find('.qtd')
		let qtd = convertMoedaToFloat($qtd.val())

		$vlUnit = $(this).find('.valor_unitario')
		let vlUnit = convertMoedaToFloat($vlUnit.val())

		$subTotal = $(this).find('.sub_total')
		$subTotal.val(convertFloatToMoeda(qtd*vlUnit))
		somaCusto += qtd*vlUnit
		setTimeout(() => {
			$vlUnitFinal = $(this).find('.valor_unitario_final')

			$vlUnitFinal.val(convertFloatToMoeda(vlUnit+(vlUnit*(percentual/100))))
			let vlUnitFinal = convertMoedaToFloat($vlUnitFinal.val())
			$subTotalFinal = $(this).find('.sub_total_final')
			$subTotalFinal.val(convertFloatToMoeda(qtd*vlUnitFinal))
			somaFinal += qtd*vlUnitFinal

		}, 10)
	})

	setTimeout(() => {
		$(".table-custos-adm .soma-custo").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".table-custos-adm .soma-final").text("R$ " + convertFloatToMoeda(somaFinal))

	}, 100)
}

$(document).delegate(".btn-remove-tr", "click", function (e) {
	e.preventDefault();
	swal({
		title: "Você esta certo?",
		text: "Deseja remover esse item mesmo?",
		icon: "warning",
		buttons: true
	}).then(willDelete => {
		if (willDelete) {
			var trLength = $(this)
			.closest("tr")
			.closest("tbody")
			.find("tr")
			.not(".dynamic-form-document").length;
			if (!trLength || trLength > 1) {
				$(this)
				.closest("tr")
				.remove();
				calculaTotal()
			} else {
				swal(
					"Atenção",
					"Você deve ter ao menos um item na lista",
					"warning"
					);
			}
		}
	});
});

function calculaTotal(){
	let somaCusto = 0
	let somaFinal = 0
	setTimeout(() => {
		$(".sub_total").each(function () {
			somaCusto += convertMoedaToFloat($(this).val())
		})

		$(".sub_total_final").each(function () {
			somaFinal += convertMoedaToFloat($(this).val())
		})

		$(".total-custo-geral").text("R$ " + convertFloatToMoeda(somaCusto))
		$(".total-final-geral").text("R$ " + convertFloatToMoeda(somaFinal))

	}, 100)
}