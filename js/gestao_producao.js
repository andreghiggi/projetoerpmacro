$(function(){

})

$('.btn-calcular').click(() => {

	if(!$('#produto_composto_id').val()){
		toastr.error("Informe a produto")
		return;
	}

	if(!$('#inp-quantidade').val()){
		toastr.error("Informe a quantidade")
		return;
	}

	let data = {
		produto_id: $('#produto_composto_id').val(),
		quantidade: $('#inp-quantidade').val(),
		empresa_id: $('#empresa_id').val(),
	}

	$.get(path_url + "api/gestao-producao/calcular", data)
	.done((success) => {
		// console.log(success)
		$('.table-produtos tbody').html(success)
		calcTotalProdutos()
	})
	.fail((error) => {
		console.log(error)
	})
})

$('.btn-add-tr-servico').on("click", function () {
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

	var $tr = $table.find(".dynamic-form").first();

	$tr.find("select.select2").select2("destroy");
	var $clone = $tr.clone();
	$clone.show();

	$clone.find("input,select").val("");
	$clone.find("select.terceiro_servico").val(0).change();
	let lines = $('.dynamic-form').length
	$table.append($clone);

	setTimeout(function () {

		$(".servico_id").select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar o seviço",
			width: "100%",
			ajax: {
				cache: true,
				url: path_url + "api/servicos",
				dataType: "json",
				data: function (params) {
					let empresa_id = $('#empresa_id').val()
					console.clear();
					var query = {
						pesquisa: params.term,
						empresa_id: empresa_id
					};
					return query;
				},
				processResults: function (response) {
					var results = [];

					$.each(response, function (i, v) {
						var o = {};
						o.id = v.id;

						o.text = v.nome + ' R$ ' + convertFloatToMoeda(v.valor);
						o.value = v.id;
						results.push(o);
					});
					return {
						results: results,
					};
				},
			},
		});
	}, 100);

})

$("body").on("change", ".servico_id", function () {
	let servico_id = $(this).val()
	$.get(path_url + "api/planejamento-custo/find-servico", {servico_id: servico_id})
	.done((success) => {
		// console.log(success)
		$qtd = $(this).closest('td').next().find('input');
		$valorUnit = $(this).closest('td').next().next().find('input');
		$sub = $(this).closest('td').next().next().next().find('input');
		$qtd.val('1')
		$valorUnit.val(convertFloatToMoeda(success.valor))
		$sub.val(convertFloatToMoeda(success.valor))

		calcTotalServicos()
	})
	.fail((error) => {
		console.log(error)
	})
})

function calcTotalOutros(){
	let total = 0
	$('.table-outros .sub_total_outros').each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	$('#inp-total_custo_outros').val(convertFloatToMoeda(total))
	calcTotal()
}

function calcTotalServicos(){
	let total = 0
	$('.table-servicos .sub_total_servico').each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	$('#inp-total_custo_servicos').val(convertFloatToMoeda(total))
	calcTotal()
}

function calcTotalProdutos(){
	let total = 0
	$('.table-produtos .sub_total_produto').each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	$('#inp-total_custo_produtos').val(convertFloatToMoeda(total))
	calcTotal()
}

$("body").on("blur", "#inp-desconto", function () {
	calcTotal()
})

$("body").on("blur", "#inp-frete", function () {
	calcTotal()
})

function calcTotal(){
	let total_custo_produtos = convertMoedaToFloat($('#inp-total_custo_produtos').val())
	let total_custo_servicos = convertMoedaToFloat($('#inp-total_custo_servicos').val())
	let total_custo_outros = convertMoedaToFloat($('#inp-total_custo_outros').val())
	let desconto = convertMoedaToFloat($('#inp-desconto').val())
	let frete = convertMoedaToFloat($('#inp-frete').val())

	$('#inp-total_final').val(convertFloatToMoeda(total_custo_servicos + total_custo_outros + total_custo_produtos + frete - desconto))
}

$(".servico_id").select2({
	minimumInputLength: 2,
	language: "pt-BR",
	placeholder: "Digite para buscar o seviço",
	width: "100%",
	ajax: {
		cache: true,
		url: path_url + "api/servicos",
		dataType: "json",
		data: function (params) {
			let empresa_id = $('#empresa_id').val()
			console.clear();
			var query = {
				pesquisa: params.term,
				empresa_id: empresa_id
			};
			return query;
		},
		processResults: function (response) {
			var results = [];

			$.each(response, function (i, v) {
				var o = {};
				o.id = v.id;

				o.text = v.nome + ' R$ ' + convertFloatToMoeda(v.valor);
				o.value = v.id;
				results.push(o);
			});
			return {
				results: results,
			};
		},
	},
});

$("body").on("blur", ".quantidade_servico", function () {
	let quantidade = convertMoedaToFloat($(this).val())
	$valorUnit = $(this).closest('td').next().find('input');
	$sub = $(this).closest('td').next().next().find('input');
	let valor = convertMoedaToFloat($valorUnit.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalServicos()
})

$("body").on("blur", ".valor_unitario_servico", function () {
	let valor = convertMoedaToFloat($(this).val())
	$qtd = $(this).closest('td').prev().find('input');
	$sub = $(this).closest('td').next().find('input');
	let quantidade = convertMoedaToFloat($qtd.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalServicos()
})

$("body").on("blur", ".valor_unitario_produto", function () {
	let valor = convertMoedaToFloat($(this).val())
	$qtd = $(this).closest('td').prev().find('input');
	$sub = $(this).closest('td').next().find('input');
	let quantidade = convertMoedaToFloat($qtd.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalProdutos()
})

$("body").on("blur", ".quantidade_outros", function () {
	let quantidade = convertMoedaToFloat($(this).val())
	$valorUnit = $(this).closest('td').next().find('input');
	$sub = $(this).closest('td').next().next().find('input');
	let valor = convertMoedaToFloat($valorUnit.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalOutros()
})

$("body").on("blur", ".valor_unitario_outros", function () {
	let valor = convertMoedaToFloat($(this).val())
	$qtd = $(this).closest('td').prev().find('input');
	$sub = $(this).closest('td').next().find('input');
	let quantidade = convertMoedaToFloat($qtd.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalOutros()
})

$("body").on("click", ".btn-salvar", function () {

	if(!$('.table-produtos .produto_id').val()){
		toastr.error('Informe 1 ou mais produtos!');
		beepErro()
		return;
	}

	$('form').submit()
});

function beepErro(){
	var audio = new Audio('/audio/beep_error.mp3');
	audio.addEventListener('canplaythrough', function() {
		audio.play();
	});
}
