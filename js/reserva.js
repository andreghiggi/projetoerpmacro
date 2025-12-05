
$(document).on("click", ".btn-procura-acomodacoes", function () {
	let data_checkin = $('#inp-data_checkin').val()
	let data_checkout = $('#inp-data_checkout').val()
	let qtd_hospedes = $('#inp-qtd_hospedes').val()

	if (!data_checkin || !data_checkout) {
		swal(
			"Atenção",
			"Preencha a quantidade de hóspedes e as datas de checkin e checkout!",
			"warning"
			);
		return;
	}

	$.get(path_url + "api/reservas/disponiveis", {
		empresa_id: $('#empresa_id').val(),
		data_checkin: data_checkin,
		data_checkout: data_checkout,
		qtd_hospedes: qtd_hospedes
	})
	.done((success) => {

		console.log(success)
		if(success.resultados > 0){
			toastr.success('Total de acomodações encontradas ' + success.resultados);
		}else{
			toastr.error('Nenhuma acomodação encontrada!');
			audioError()
		}
		$('.acomodacoes-view').html(success.view)
		setTimeout(() => {
			initSelect2()
			initClient()

		}, 100)
	})
	.fail((err) => {
		console.log(err)

	})
});

function initSelect2(){
	$(".select2").select2({
		width: $(this).data("width")
		? $(this).data("width")
		: $(this).hasClass("w-100")
		? "100%"
		: "style",
		placeholder: $(this).data("placeholder"),
		allowClear: Boolean($(this).data("allow-clear")),
	});
}

function initClient(){
	$("#inp-cliente_id").select2({
		minimumInputLength: 2,
		language: "pt-BR",
		placeholder: "Digite para buscar o cliente",

		ajax: {
			cache: true,
			url: path_url + "api/clientes/pesquisa",
			dataType: "json",
			data: function (params) {
				console.clear();
				var query = {
					pesquisa: params.term,
					empresa_id: $("#empresa_id").val(),
				};
				return query;
			},
			processResults: function (response) {
				var results = [];

				$.each(response, function (i, v) {
					var o = {};
					o.id = v.id;

					o.text = v.razao_social + " - " + v.cpf_cnpj;
					o.value = v.id;
					results.push(o);
				});
				return {
					results: results,
				};
			},
		},
	});
	$("#inp-novo_cidade_id").select2({
		minimumInputLength: 2,
		language: "pt-BR",
		placeholder: "Digite para buscar a cidade",
		width: "100%",
		dropdownParent: $("#modal_novo_cliente"),
		ajax: {
			cache: true,
			url: path_url + "api/buscaCidades",
			dataType: "json",
			data: function (params) {
				console.clear();
				var query = {
					pesquisa: params.term,
				};
				return query;
			},
			processResults: function (response) {
				var results = [];

				$.each(response, function (i, v) {
					var o = {};
					o.id = v.id;

					o.text = v.info;
					o.value = v.id;
					results.push(o);
				});
				return {
					results: results,
				};
			},
		},
	});
}

$(document).on("change", "#inp-acomodacao_id", function () {
	let acomodacao_id = $(this).val()
	let data_checkin = $('#inp-data_checkin').val()
	let data_checkout = $('#inp-data_checkout').val()
	if(acomodacao_id){
		$.get(path_url + "api/reservas/dados-acomodacao", {
			acomodacao_id: acomodacao_id,
			data_checkin: data_checkin,
			data_checkout: data_checkout,
		})
		.done((success) => {

			console.log(success)
			$('#inp-valor_estadia').val(convertFloatToMoeda(success.valor_estadia))
		})
		.fail((err) => {
			console.log(err)
		})
	}
});

$('body').on('change', '#inp-produto_id', function () {
	let product_id = $(this).val()
	$.get(path_url + "api/produtos/findId/"+product_id)
	.done((success) => {
		console.log(success)
		$('#inp-quantidade_produto').val('1,00')
		$('#inp-valor_unitario_produto').val(convertFloatToMoeda(success.valor_unitario))
		$('#inp-sub_total_produto').val(convertFloatToMoeda(success.valor_unitario))
	})
	.fail((e) => {
		console.log(e)
	})
})

$('body').on('blur', '#inp-valor_unitario_produto', function () {
	$qtd = $(this).closest('.col').prev().find('input');
	$sub = $(this).closest('.col').next().find('input');
	let value_unit = $(this).val();
	value_unit = convertMoedaToFloat(value_unit)
	let qtd = convertMoedaToFloat($qtd.val())
	$sub.val(convertFloatToMoeda(qtd * value_unit))
});

$('body').on('blur', '#inp-quantidade_produto', function () {
	$value_unit = $(this).closest('.col').next().find('input');
	$sub = $(this).closest('.col').next().next().find('input');
	let qtd = $(this).val();
	qtd = convertMoedaToFloat(qtd)
	let value_unit = convertMoedaToFloat($value_unit.val())
	$sub.val(convertFloatToMoeda(qtd * value_unit))
})

$('body').on('blur', '#inp-valor_unitario_servico', function () {
	$qtd = $(this).closest('.col').prev().find('input');
	$sub = $(this).closest('.col').next().find('input');
	let value_unit = $(this).val();
	value_unit = convertMoedaToFloat(value_unit)
	let qtd = convertMoedaToFloat($qtd.val())
	$sub.val(convertFloatToMoeda(qtd * value_unit))
});

$('body').on('blur', '#inp-quantidade_servico', function () {
	$value_unit = $(this).closest('.col').next().find('input');
	$sub = $(this).closest('.col').next().next().find('input');
	let qtd = $(this).val();
	qtd = convertMoedaToFloat(qtd)
	let value_unit = convertMoedaToFloat($value_unit.val())
	$sub.val(convertFloatToMoeda(qtd * value_unit))
})

$('body').on('blur', '.cep', function () {

	let cep = $(this).val().replace(/[^0-9]/g,'')
	$rua = $(this).closest('.col').next().find('input');
	$numero = $(this).closest('.col').next().next().find('input');
	$bairro = $(this).closest('.col').next().next().next().find('input');
	$cidade = $(this).closest('.col').next().next().next().next().find('select');

	if(cep.length == 8){
		$.get('https://viacep.com.br/ws/'+cep+'/json')
		.done((res) => {

			$rua.val(res.logradouro)
			$bairro.val(res.bairro)
			$.get(path_url + "api/cidadePorCodigoIbge/" + res.ibge)
			.done((res) => {
				var newOption = new Option(res.info, res.id, false, false);

				$cidade.append(newOption).trigger('change');
			})
			.fail((err) => {
				console.log(err)
			})
		})
		.fail((err) => {
			console.log(err)
		})
	}else{
		swal("Erro", "Informe o CEP corretamente", "error")
	}
});

$(".cidade").select2({
	minimumInputLength: 2,
	language: "pt-BR",
	placeholder: "Digite para buscar a cidade",
	width: "100%",
	ajax: {
		cache: true,
		url: path_url + "api/buscaCidades",
		dataType: "json",
		data: function (params) {
			console.clear();
			var query = {
				pesquisa: params.term,
			};
			return query;
		},
		processResults: function (response) {
			var results = [];

			$.each(response, function (i, v) {
				var o = {};
				o.id = v.id;

				o.text = v.info;
				o.value = v.id;
				results.push(o);
			});
			return {
				results: results,
			};
		},
	},
});

$('.btn-action').click(() => {
	addClassRequired()
})

$('#btn-hospedes').click(() => {

	$.get(path_url + "api/reservas/dados-hospedes", {reserva_id: $('#reserva_id').val()})
	.done((success) => {
		$('#modal_hospedes').modal('show')
		$('#modal_hospedes .append').html(success)
		
		$(".cidade").select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar a cidade",
			width: "100%",
			dropdownParent: $('#modal_hospedes'),
			ajax: {
				cache: true,
				url: path_url + "api/buscaCidades",
				dataType: "json",
				data: function (params) {
					console.clear();
					var query = {
						pesquisa: params.term,
					};
					return query;
				},
				processResults: function (response) {
					var results = [];

					$.each(response, function (i, v) {
						var o = {};
						o.id = v.id;

						o.text = v.info;
						o.value = v.id;
						results.push(o);
					});
					return {
						results: results,
					};
				},
			},
		});
	})
	.fail((e) => {
		console.log(e)
	})
})

function addClassRequired() {
	let isInalid = false
	let campos = ""
	$("body #form-checkin").find('input, select').each(function () {
		if ($(this).prop('required')) {

			if ($(this).val() == "" || $(this).val() == null) {
				$(this).addClass('is-invalid')
				isInalid = true
				if($(this).prev()[0].textContent){
					campos += $(this).prev()[0].textContent + ", "
				}
			} else {
				$(this).removeClass('is-invalid')
			}
		} else {
			$(this).removeClass('is-invalid')
		}
	})

	setTimeout(() => {
		if(isInalid){
			audioError()
			campos = campos.substring(0, campos.length-2)
			toastr.error('Campos obrigatórios não preenchidos, preencha todos os hóspedes: ' + campos);
		}else{
			$body.addClass("loading");
		}
	}, 50)
}

$("#inp-produto_id").select2({
	minimumInputLength: 2,
	language: "pt-BR",
	placeholder: "Digite para buscar o produto",
	width: "100%",
	ajax: {
		cache: true,
		url: path_url + "api/produtos-reserva",
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
				if(v.codigo_variacao){
					o.codigo_variacao = v.codigo_variacao
				}

				o.text = v.nome;

				o.text += ' R$ ' + convertFloatToMoeda(v.valor_unitario);
				
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

$("#inp-servico_id").select2({
	minimumInputLength: 2,
	language: "pt-BR",
	placeholder: "Digite para buscar o seviço",
	width: "100%",
	theme: "bootstrap4",
	ajax: {
		cache: true,
		url: path_url + "api/servicos-reserva",
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

$('body').on('change', '#inp-servico_id', function () {
	let servico_id = $(this).val()
	$.get(path_url + "api/servicos/find/"+servico_id)
	.done((success) => {
		console.log(success)
		$('#inp-quantidade_servico').val('1,00')
		$('#inp-valor_unitario_servico').val(convertFloatToMoeda(success.valor))
		$('#inp-sub_total_servico').val(convertFloatToMoeda(success.valor))
	})
	.fail((e) => {
		console.log(e)
	})
})


