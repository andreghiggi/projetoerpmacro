$(function(){

})

var $linha = null

$(document).on("click", ".btn-dimensao", function () {

	$linha = $(this).closest(".dynamic-form")
	let produto_id = $linha.find('.produto_id').val()
	if(!produto_id){
		swal("Alerta", "Selecione o produto", "warning")
		return;
	}

	$('#modal_dimensao_planejamento').modal('show')
	var nome = $linha.find('.produto_id option:selected').text();
	$('#modal_dimensao_planejamento .modal-title').text(nome)
	$('#modal_dimensao_planejamento input').each(function () {
		$(this).val('')
	});

	let valor_unitario = convertMoedaToFloat($linha.find('.valor_unitario_produto').val())
	$('#dimensao_valor_unitario').val($linha.find('.valor_unitario_produto').val())

	$('#dimensao_quantidade').val($linha.find('.quantidade_produto').val())
	$('#dimensao_largura').val($linha.find('.largura').val())
	$('#dimensao_espessura').val($linha.find('.espessura').val())
	$('#dimensao_comprimento').val($linha.find('.comprimento').val())

	let peso = $linha.find('.peso_especifico').val()
	$('#dimensao_peso_especifico').val(convertFloatToMoeda(peso ? peso : '0'))

	let peso_bruto = $linha.find('.peso_bruto').val()
	$('#dimensao_peso_bruto').val(convertFloatToMoeda(peso_bruto ? peso_bruto : '0'))

	if(peso_bruto){
		$('#dimensao_largura').val('')
		$('#dimensao_espessura').val('')
		$('#dimensao_largura').attr('readonly', 1) 
		$('#dimensao_espessura').attr('readonly', 1) 
		$('#calcular_peso_bruto').attr('checked', 1)
	}else{
		$('#dimensao_largura').removeAttr('readonly') 
		$('#dimensao_espessura').removeAttr('readonly') 
		$('#calcular_peso_bruto').removeAttr('checked')
	}

	setTimeout(() => {
		$('#dimensao_quantidade').focus()
		calcularDimensao()
	}, 200)
})


$("body").on("blur", ".calcular-dimensao", function () {
	calcularDimensao()
})

$('#dimensao_comprimento, #dimensao_espessura').on('keypress', function (e) {
	if (e.which === 13) {
		$('#btn-salvar-dimensao').focus()
	}
})

$('.inp-dimensao').on('input', function () {
	this.value = this.value.replace(/[^0-9,]/g, '');
});

var calc = 0

$(document).on('change', '#calcular_peso_bruto', function () {
	if ($(this).is(':checked')) {
		$('#dimensao_largura').val('')
		$('#dimensao_espessura').val('')
		$('#dimensao_largura').attr('readonly', 1) 
		$('#dimensao_espessura').attr('readonly', 1) 
	}else{
		$('#dimensao_largura').removeAttr('readonly') 
		$('#dimensao_espessura').removeAttr('readonly')
	}
	calcularDimensao()
});

function calcularDimensao(){
	let valor_unitario = convertMoedaToFloat($('#dimensao_valor_unitario').val())
	let largura = convertMoedaToFloat($('#dimensao_largura').val())
	let espessura = convertMoedaToFloat($('#dimensao_espessura').val())
	let comprimento = convertMoedaToFloat($('#dimensao_comprimento').val())
	let peso_especifico = convertMoedaToFloat($('#dimensao_peso_especifico').val())
	let calcular_peso_bruto = $('#calcular_peso_bruto').is(':checked')
	let qtd = convertMoedaToFloat($('#dimensao_quantidade').val())

	if(calcular_peso_bruto){
		calc = (peso_especifico/1000) * comprimento * qtd
		// console.log("calc", calc)
		$('#dimensao_peso_bruto').val(calc)
		$('#dimensao_sub_total').val(convertFloatToMoeda(valor_unitario*calc))

	}else{
		calc = (largura/1000) * (comprimento/1000) * (espessura/1000) * peso_especifico
		$('#dimensao_peso_bruto').val(calc)
		$('#dimensao_sub_total').val(convertFloatToMoeda(valor_unitario*(qtd*calc)))
	}
	// console.log("calc", calc)
}

$('#btn-salvar-dimensao').on("click", function () {
	$('#modal_dimensao_planejamento').modal('hide')

	$linha.find('.sub_total_produto').val($('#dimensao_sub_total').val())
	$linha.find('.calculo').val(calc.toFixed(4))

	let qtd = convertMoedaToFloat($('#dimensao_quantidade').val())
	let vlUnit = convertMoedaToFloat($('#dimensao_valor_unitario').val())
	$linha.find('.quantidade_produto').val(qtd)

	$linha.find('.valor_unitario_produto').val($('#dimensao_valor_unitario').val())
	$linha.find('.largura').val($('#dimensao_largura').val())
	$linha.find('.espessura').val($('#dimensao_espessura').val())
	$linha.find('.comprimento').val($('#dimensao_comprimento').val())
	$linha.find('.peso_especifico').val($('#dimensao_peso_especifico').val())
	$linha.find('.peso_bruto').val($('#dimensao_peso_bruto').val())

	// $linha.find('.sub_total_produto').val(convertFloatToMoeda((qtd*vlUnit)))

	calc = 0
})

$('.btn-add-tr-produto').on("click", function () {
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
	let lines = $('.dynamic-form').length
	$table.append($clone);

	setTimeout(function () {

		$(".produto_id").select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar o produto",
			width: "100%",
			ajax: {
				cache: true,
				url: path_url + "api/produtos",
				dataType: "json",
				data: function (params) {
					let empresa_id = $('#empresa_id').val()

					let local_id = null
					if($('#inp-local_id').length){
						let local_id = $('#inp-local_id').val()
						if(!local_id){
							swal("Alerta", "Selecione primeiramente o local", "warning")
							return;
						}
					}
					console.clear();

					var query = {
						pesquisa: params.term,
						empresa_id: empresa_id,
						usuario_id: $('#usuario_id').val(),
						local_id: $('#inp-local_id').length ? $('#inp-local_id').val() : null
					};
					return query;
				},
				processResults: function (response) {
					var results = [];
					let compra = 0
					if($('#is_compra') && $('#is_compra').val() == 1){
						compra = 1
					}

					$.each(response, function (i, v) {
						var o = {};
						o.id = v.id;
						if(v.codigo_variacao){
							o.codigo_variacao = v.codigo_variacao
						}

						if(v.tipo_dimensao == 1){
							o.espessura = v.espessura
							o.tipo_dimensao = 1
						}

						if(v.altura){
							o.altura = v.altura
						}

						if(v.largura){
							o.largura = v.largura
						}

						o.estoque_atual = v.estoque_atual

						if(v.gerenciar_estoque){
							o.gerenciar_estoque = v.gerenciar_estoque
						}

						o.text = ""
						if(v.numero_sequencial){
							o.text += "["+v.numero_sequencial+"] "
						}
						o.text += v.nome 

						if(v.codigo_barras){
							o.text += ' [' + v.codigo_barras  + ']';
						}
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
				calcTotalProdutos()
				calcTotalServicos()
				calcTotalServicosTerceiro()
				calcTotalCustosAdm()
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

$(".produto_id").select2({
	minimumInputLength: 2,
	language: "pt-BR",
	placeholder: "Digite para buscar o produto",
	width: "100%",
	ajax: {
		cache: true,
		url: path_url + "api/produtos",
		dataType: "json",
		data: function (params) {
			let empresa_id = $('#empresa_id').val()

			let local_id = null
			if($('#inp-local_id').length){
				let local_id = $('#inp-local_id').val()
				if(!local_id){
					swal("Alerta", "Selecione primeiramente o local", "warning")
					return;
				}
			}
			console.clear();

			var query = {
				pesquisa: params.term,
				empresa_id: empresa_id,
				usuario_id: $('#usuario_id').val(),
				local_id: $('#inp-local_id').length ? $('#inp-local_id').val() : null
			};
			return query;
		},
		processResults: function (response) {
			var results = [];
			let compra = 0
			if($('#is_compra') && $('#is_compra').val() == 1){
				compra = 1
			}

			$.each(response, function (i, v) {
				var o = {};
				o.id = v.id;
				if(v.codigo_variacao){
					o.codigo_variacao = v.codigo_variacao
				}

				if(v.tipo_dimensao == 1){
					o.espessura = v.espessura
					o.tipo_dimensao = 1
				}

				if(v.altura){
					o.altura = v.altura
				}

				if(v.largura){
					o.largura = v.largura
				}

				o.estoque_atual = v.estoque_atual

				if(v.gerenciar_estoque){
					o.gerenciar_estoque = v.gerenciar_estoque
				}

				o.text = ""
				if(v.numero_sequencial){
					o.text += "["+v.numero_sequencial+"] "
				}
				o.text += v.nome

				if(v.codigo_barras){
					o.text += ' [' + v.codigo_barras  + ']';
				}
				o.value = v.id;
				results.push(o);
			});
			return {
				results: results,
			};
		},
	},
});


$(document).on("change", ".produto_id", function () {
	let produto_id = $(this).val()
	$.get(path_url + "api/planejamento-custo/find-produto", {produto_id: produto_id})
	.done((success) => {
		$qtd = $(this).closest('td').next().find('input');
		$valorUnit = $(this).closest('td').next().next().find('input');
		$sub = $(this).closest('td').next().next().next().find('input');
		$qtd.val('1')

		$(this).closest('td').find('.peso_especifico').val(success.peso ? convertFloatToMoeda(success.peso) : '')
		$(this).closest('td').find('.peso_bruto').val(success.peso_bruto ? convertFloatToMoeda(success.peso_bruto) : '')

		$valorUnit.val(convertFloatToMoeda(success.valor))
		$sub.val(convertFloatToMoeda(success.valor))

		calcTotalProdutos()
	})
	.fail((error) => {
		console.log(error)
	})
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
		calcTotalServicosTerceiro()
	})
	.fail((error) => {
		console.log(error)
	})
})

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

$("body").on("blur", ".quantidade_servico_terceiro", function () {
	let quantidade = convertMoedaToFloat($(this).val())
	$valorUnit = $(this).closest('td').next().find('input');
	$sub = $(this).closest('td').next().next().find('input');
	let valor = convertMoedaToFloat($valorUnit.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalServicosTerceiro()
})

$("body").on("blur", ".valor_unitario_servico_terceiro", function () {
	let valor = convertMoedaToFloat($(this).val())
	$qtd = $(this).closest('td').prev().find('input');
	$sub = $(this).closest('td').next().find('input');
	let quantidade = convertMoedaToFloat($qtd.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalServicosTerceiro()
})

$("body").on("blur", ".quantidade_custo_adm", function () {
	let quantidade = convertMoedaToFloat($(this).val())
	$valorUnit = $(this).closest('td').next().find('input');
	$sub = $(this).closest('td').next().next().find('input');
	let valor = convertMoedaToFloat($valorUnit.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalCustosAdm()
})

$("body").on("blur", ".valor_unitario_custo_adm", function () {
	let valor = convertMoedaToFloat($(this).val())
	$qtd = $(this).closest('td').prev().find('input');
	$sub = $(this).closest('td').next().find('input');
	let quantidade = convertMoedaToFloat($qtd.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalCustosAdm()
})

$("body").on("blur", ".quantidade_produto", function () {
	let quantidade = convertMoedaToFloat($(this).val())
	$valorUnit = $(this).closest('td').next().find('input');
	$sub = $(this).closest('td').next().next().find('input');
	let valor = convertMoedaToFloat($valorUnit.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalProdutos()
})

$("body").on("blur", ".valor_unitario_produto", function () {
	let valor = convertMoedaToFloat($(this).val())
	$qtd = $(this).closest('td').prev().find('input');
	$sub = $(this).closest('td').next().find('input');
	let quantidade = convertMoedaToFloat($qtd.val())
	$sub.val(convertFloatToMoeda(valor*quantidade));
	calcTotalProdutos()
})

function calcTotalServicos(){
	let total = 0
	$('.table-servicos .sub_total_servico').each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	$('.total-servico').text("R$ " + convertFloatToMoeda(total))
}

function calcTotalServicosTerceiro(){
	let total = 0
	$('.table-servicos-terceiro .sub_total_servico_terceiro').each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	$('.total-servico-terceiro').text("R$ " + convertFloatToMoeda(total))
}

function calcTotalCustosAdm(){
	let total = 0
	$('.table-custos-adm .sub_total_custo_adm').each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	$('.total-custos-adm').text("R$ " + convertFloatToMoeda(total))
}

function calcTotalProdutos(){
	let total = 0
	$('.table-produtos .sub_total_produto').each(function () {
		total += convertMoedaToFloat($(this).val())
	})

	$('.total-produto').text("R$ " + convertFloatToMoeda(total))
}

$("body").on("click", ".btn-salvar", function () {
	// if(!$('#inp-cliente_id').val()){
	// 	toastr.error('Informe o cliente!');
	// 	beepErro()
	// 	return;
	// }

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
