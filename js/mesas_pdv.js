
var CATEGORIAID = 0
var COMANDAID = 0
var MAXSABORES = 0
var VALORPIZZA = 0
var TIPODIVISAOPIZZA = null
var SABORESSELECIONADOS = []
var $htmlComandas = ""

$(document).on('click', '.list-categoria', function(){
	CATEGORIAID = $(this).data('id')

	$('.list-categoria').removeClass('active')
	$('.categoria-'+CATEGORIAID).addClass('active')
	if(CATEGORIAID == 0){
		$('.list-categoria').first().addClass('active')
	}
	getProdutos()
})

$(function(){
	$('#inp-pesquisa').val('')
	$('#inp-pesquisa_comanda').val('')
	COMANDAID = $('#comanda_id').val()

	TIPODIVISAOPIZZA = $('#tipo_divisao_pizza').val()

	setTimeout(() => {

		$('.modal #inp-cliente_id').each(function () {
			$(this).select2({
				minimumInputLength: 2,
				dropdownParent: $(this).parent(),
				language: "pt-BR",
				placeholder: "Digite para buscar o cliente",
				theme: "bootstrap4",

				ajax: {
					cache: true,
					url: path_url + "api/clientes/pesquisa",
					dataType: "json",
					data: function (params) {
						// console.clear();
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
		});
		$htmlComandas = $('.comandas')
	}, 10)

})

$(document).on("change", "#inp-tamanho_id", function () {
	MAXSABORES = $(this).find(':selected').attr('max-sabores')
	if($(this).val()){
		SABORESSELECIONADOS = []
		buscarPizzas($(this).val())
	}
})

function buscarPizzas(tamanho_id){
	SABORESSELECIONADOS.push(_produto_id)
	$.get(path_url + "api/produtos/get-pizzas",
	{ 
		empresa_id: $('#empresa_id').val(), 
		produto_id: _produto_id,
		tamanho_id: tamanho_id
	})
	.done((success) => {
    	// console.log(success)
    	$('.modal-body .pizzas').html(success)
    	calculaValorPizza()
    })
	.fail((err) => {
		console.log(err)
	})
}

function calculaValorPizza(){
	// console.log(SABORESSELECIONADOS)
	$.get(path_url + "api/produtos/calculo-pizza",
	{ 
		sabores: SABORESSELECIONADOS,
		tamanho_id: $("#inp-tamanho_id").val(),
		empresa_id: $('#empresa_id').val()
	})
	.done((success) => {
    	// console.log(success)
    	$('#inp-valor_unitario_item').val(convertFloatToMoeda(success))
    	VALORPIZZA = success
    })
	.fail((err) => {
		console.log(err)
	})
}

function selectPizza(id){
	id = parseInt(id)
	let tempArr = []
	// console.log(SABORESSELECIONADOS)
	// console.log(id)
	if(SABORESSELECIONADOS.includes(id)){
		$('.bg-'+id).removeClass('bg-info')
		SABORESSELECIONADOS = SABORESSELECIONADOS.filter((x) => { return x != id})
	}else{
		if(SABORESSELECIONADOS.length >= MAXSABORES){
			swal("Atenção", "Máximo de sabores atingido!", "warning")
			return;
		}
		$('.bg-'+id).addClass('bg-info')
		SABORESSELECIONADOS.push(id)
	}

	setTimeout(() => {
		calculaValorPizza()
	}, 50)
}


$("#inp-produto_id").change(() => {
	let product_id = $("#inp-produto_id").val();

	if (product_id) {

		$.get(path_url + "api/produtos/findWithLista",
		{ 
			produto_id: product_id,
			lista_id: $('#lista_id').val(),
			local_id: $('#local_id').val(),
		})
		.done((e) => {
			
			$("#inp-quantidade").val("1");
			$("#inp-valor_unitario").val(convertFloatToMoeda(e.valor_unitario));
			$("#inp-subtotal").val(convertFloatToMoeda(e.valor_unitario));
			$('#inp-quantidade').focus()
		})
		.fail((e) => {
			console.log(e);
		});
	}
})

function getProdutos(){

	let data = {
		categoria_id : CATEGORIAID,
		marca_id : null,
		empresa_id: $('#empresa_id').val(),
		lista_id: $('#lista_id').val(),
		local_id: $('#local_id').val(),
		pesquisa: $('#inp-pesquisa').val()
	}

	$.get(path_url + 'api/frenteCaixa/produtos-page2?page=1', data)
	.done((res) => {
		// console.log(res)
		$('.div-produtos').html(res)
	}).fail((err) => {
		console.log(err)
	})
}

$(document).on('click', '.produtos-pagination a', function(event){
	event.preventDefault()

	let data = {
		categoria_id : CATEGORIAID,
		marca_id : null,
		empresa_id: $('#empresa_id').val(),
		lista_id: $('#lista_id').val(),
		local_id: $('#local_id').val(),
		pesquisa: $('#inp-pesquisa').val()
	}
	var page = $(this).attr('href').split('page=')[1];
	$.get(path_url + 'api/frenteCaixa/produtos-page2?page='+page, data)
	.done((res) => {
		$('.div-produtos').html(res)
	}).fail((err) => {
		console.log(err)
	})
})

$(document).on('keydown', '#inp-quantidade',function(e) {
	if (e.key === "Enter") {
		$('#inp-valor_unitario').focus()
	}
});

$(document).on('keydown', '#inp-valor_unitario',function(e) {
	if (e.key === "Enter") {
		$('.btn-add-item').trigger('click')
	}
});

$(document).on('keydown', '.not-enter',function(e) {
	if (e.key === "Enter") {
		e.preventDefault();
		return false;
	}
});

$(document).on('input', '#inp-pesquisa_comanda', function(event){
	event.preventDefault()
	let pesquisa = $(this).val()
	$('.comanda').each(function () {
		let nComanda = $(this).find('.numero_comanda').text()
		if(nComanda.includes(pesquisa)){
			$(this).removeClass('d-none')
		}else{
			$(this).addClass('d-none')
		}
	})
})

$(document).on('input', '#inp-pesquisa', function(event){
	event.preventDefault()

	let data = {
		categoria_id : CATEGORIAID,
		marca_id : null,
		empresa_id: $('#empresa_id').val(),
		lista_id: $('#lista_id').val(),
		local_id: $('#local_id').val(),
		pesquisa: $(this).val()
	}

	$.get(path_url + 'api/frenteCaixa/produtos-page2?page=', data)
	.done((res) => {
		$('.div-produtos').html(res)
	}).fail((err) => {
		console.log(err)
	})
})

function addProduto(id, variacao_id = null){

	if(!$('#numero_comanda').val()){
		swal("Atenção", "Selecione a comanda primeiro", "warning")
		return
	}

	if(infoModal == true){
		return
	}

	$('.results-list').html('').addClass('d-none')
	$('#inp-pesquisa').val('')
	let qtd = 0;

	if(variacao_id){
		buscarVariacoes(id)
		return ;
	}

	let abertura = $('#abertura').val()
	let agrupar_itens = $('#agrupar_itens').val()

	// $('.products').each(function () {
	// 	if(id == $(this).find('.produto_id').val()){
	// 		qtd += parseFloat($(this).find('.quantidade').val())
	// 	}
	// })

	if (abertura) {
		$.post(path_url + 'api/frenteCaixa/add-produto2', { 
			produto_id: id, 
			lista_id: $('#lista_id').val(), 
			qtd: 1,
			local_id: $('#local_id').val(),
			usuario_id: $('#usuario_id').val(),
		})
		.done((res) => {
			// console.log(res)
			$('.results-list').html('').addClass('d-none')
			let idDup = 0
			if(agrupar_itens == 1){
				$(".produto_row").each(function () {
					if($(this).val() == id){
						idDup = $(this).val()
					}
				})
			}
			setTimeout(() => {
				if(idDup == 0){
					$('.itens-cart').append(res)
				}else{
					$(".itens-cart .products").each(function(){
						if($(this).find('.produto_row').val() == id){
							let qtdAnt = convertMoedaToFloat($(this).find('.quantidade').val())

							$(this).find('.quantidade').val(qtdAnt+1)
							$(this).find('.qtd-row').val(qtdAnt+1)
						}
					})
				}
			},10)
			calculaTotal()
			beepSucesso()

		}).fail((err) => {
			console.log(err)
			swal("Erro", err.responseJSON, "error")
			beepErro()
		})
	}else{
		swal("Atenção", "Abra o caixa para continuar!", "warning").then(() => {
			validaCaixa()
		})

	}
}

$(".btn-add-item").click(() => {
	// console.clear()
	if(!$('#numero_comanda').val()){
		swal("Atenção", "Selecione a comanda primeiro", "warning")
		return
	}
	let qtd = $("#inp-quantidade").val();
	let value_unit = $("#inp-valor_unitario").val();
	value_unit = convertMoedaToFloat(value_unit);
	qtd = convertMoedaToFloat(qtd);
	let product_id = $("#inp-produto_id").val();

	let data = { 
		produto_id: product_id, 
		lista_id: $('#lista_id').val(),
		value_unit: value_unit,
		qtd: qtd,
		local_id: $('#local_id').val(),
		usuario_id: $('#usuario_id').val()
	}
	console.log(data)

	if (abertura) {
		$.post(path_url + 'api/frenteCaixa/add-produto2', data)
		.done((res) => {
			// console.log(res)
			$('.results-list').html('').addClass('d-none')
			let idDup = 0
			setTimeout(() => {
				if(idDup == 0){
					$('.itens-cart').append(res)
				}else{
					$(".itens-cart .products").each(function(){
						if($(this).find('.produto_row').val() == id){
							let qtdAnt = convertMoedaToFloat($(this).find('.quantidade').val())

							$(this).find('.quantidade').val(qtdAnt+1)
							$(this).find('.qtd-row').val(qtdAnt+1)
						}
					})
				}
			},10)
			calculaTotal()
			beepSucesso()
			$("#inp-quantidade").val('')
			$("#inp-valor_unitario").val('')
			$('#inp-produto_id').val('').change()

		}).fail((err) => {
			console.log(err)
			swal("Erro", err.responseJSON, "error")
			beepErro()
		})
	}else{
		swal("Atenção", "Abra o caixa para continuar!", "warning").then(() => {
			validaCaixa()
		})

	}
})


function removeItem(code){
	swal({
		title: "Você está certo?",
		text: "Deseja realmente remover este item da venda?",
		icon: "warning",
		buttons: true,
		buttons: ["Cancelar", "Excluir"],
		dangerMode: true,
	}).then((isConfirm) => {
		if (isConfirm) {

			$('.product-line-'+code).remove()
			calculaTotal()
		} else {
			swal("", "Este item está salvo!", "info");
		}
	});
}

function calculaTotal(){

	// let numero_comanda = $('#numero_comanda').val()
	// let desconto = convertMoedaToFloat($('#inp-desconto').val())
	// let acrescimo = convertMoedaToFloat($('#inp-acrescimo').val())
	// setTimeout(() => {
	// 	let total = 0
	// 	$(".subtotal_item").each(function () {
	// 		total += parseFloat($(this).val());
	// 	});
	// 	total = total - desconto + acrescimo

		// $('.total').text("R$ " + convertFloatToMoeda(total))
		// $('.total-venda-modal').text("R$ " + convertFloatToMoeda(total))

	// 	atualizaComanda()

		// if(total > 0){
		// 	$('.btn-print').removeClass('disabled')
		// 	$('.btn-delete-comanda').removeClass('disabled')
		// }
	// }, 100)
	setTimeout(() => {
		atualizaComanda()
	}, 100)


}

function atualizaComanda(){
	// console.log("atualizando comanda ...")
	// console.clear()
	let itens = []
	let numero_comanda = $('#numero_comanda').val()
	$(".d-item").each(function () {
		let produto_id = $(this).find('.produto_id').val()
		let quantidade = $(this).find('.quantidade').val()
		let valor_unitario = $(this).find('.valor_unitario').val()
		let sub_total = $(this).find('.subtotal_item').val()
		let observacao = $(this).find('.observacao').val()
		let adicionais = $(this).find('.adicionais').val()
		let tamanho_id = $(this).find('.tamanho_id').val()
		let sabores = $(this).find('.sabores').val()

		let data = {
			produto_id: produto_id,
			quantidade: quantidade,
			valor_unitario: valor_unitario,
			sub_total: sub_total,
			observacao: observacao,
			adicionais: adicionais,
			tamanho_id: tamanho_id,
			sabores: sabores,
		}
		itens.push(data)
		// console.log("data", data)

	})
	// return;
	let desconto = convertMoedaToFloat($('#inp-desconto').val())
	let acrescimo = convertMoedaToFloat($('#inp-acrescimo').val())
	let data = {
		itens: itens,
		desconto: desconto,
		acrescimo: acrescimo,
		empresa_id: $('#empresa_id').val(),
		numero_comanda: numero_comanda
	}

	// console.log(data)

	$.post(path_url + 'api/frenteCaixa/atualizar-comanda', data)
	.done((res) => {
		// console.log(res)
		COMANDAID = res.id
		$('#comanda_mesa_id').val(COMANDAID)
		$dm = $('.div-mesa')
		if(!$dm[0].innerText){
			$('.div-mesa .btn-selecionar-mesa').removeClass('d-none')
		}
		let total = res.total + res.acrescimo - res.desconto
		$('#comanda_delete_id').val(COMANDAID)
		$('.comanda-'+numero_comanda).find('.total-comanda').text("R$ " + convertFloatToMoeda(total))
		$('.comanda-'+numero_comanda).find('.vaga').removeClass("bg-primary").addClass("bg-danger")
		$('.comanda-'+numero_comanda).find('.status-comanda').text("Ocupada")

		$('#inp-acrescimo').val(convertFloatToMoeda(res.acrescimo))
		$('#inp-desconto').val(convertFloatToMoeda(res.desconto))

		$('.total').text("R$ " + convertFloatToMoeda(total))
		$('.total-venda-modal').text("R$ " + convertFloatToMoeda(total))
		// console.log("total", total)
		if(total > 0){
			$('.btn-print').removeClass('disabled')
			$('.btn-delete-comanda').removeClass('disabled')
		}
		// calculaTotal()
	})
	.fail((err) => {
		console.log(err)
	})
}

$('.btn-selecionar-mesa').click(() => {
	$('#definir_mesa').modal('show')
});

function print(){
	impressao_sem_janela_cupom = $('#impressao_sem_janela_cupom').val()
	if(impressao_sem_janela_cupom == 0){
		var disp_setting="toolbar=yes,location=no,";
		disp_setting+="directories=yes,menubar=yes,";
		disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

		var docprint=window.open(path_url+"pedidos-cardapio-print/"+COMANDAID,"",disp_setting);

		docprint.focus();
	}else{
		window.open(path_url+"pedidos-cardapio-print-html/"+COMANDAID)
	}
}

$(document).on('click', '.btn-add-tr', function(){
	let total = convertMoedaToFloat($('.total').text())
	let soma = 0;
	$('.fatura .valor_integral_row').each(function () {
		soma += convertMoedaToFloat($(this).val());
	});
	let diferenca = total - soma
	if($('.valor_integral_row').last().val() == ""){
		$('.valor_integral_row').last().val(convertFloatToMoeda(diferenca))
	}
});

var TROCODDEFINIDO = 0
$(document).on('blur', '.valor_integral_row', function(){
	let total = convertMoedaToFloat($('.total').text())
	let soma = 0;
	$('.fatura .valor_integral_row').each(function () {
		soma += convertMoedaToFloat($(this).val());
	});
	let diferenca = total - soma
	$('.total-restante').text("R$ " + convertFloatToMoeda(diferenca))

	if(diferenca < 0){
		swal({
			title: "Troco?",
			text: "O valor total é maior que a soma dos produtos, deseja definir o troco?",
			icon: "warning",
			buttons: true,
			buttons: ["Não", "Sim"],
			dangerMode: true,
		}).then((isConfirm) => {
			if (isConfirm) {
				$('.d-troco').removeClass('d-none')
				$('.troco').val(convertFloatToMoeda(diferenca*-1))
				$('.total-restante').text("R$ " + convertFloatToMoeda(0))
				TROCODDEFINIDO = 1
			}
		});
	}
});

$(document).on('click', '#btn-finalizar', function(){
	let total = convertMoedaToFloat($('.total').text())

	if(total == 0){
		swal("Erro", "Valor da venda deve ser maior que zero!", "error")
		return;
	}
	$('.valor_integral_row').val(convertFloatToMoeda(total))
	$('.total-venda-modal').text("R$ " + convertFloatToMoeda(total))
	$('.total-restante').text("R$ " + convertFloatToMoeda(0))
	$('#modal_finalizar_pdv2').modal('show')

	if(!$('.data_vencimento').val()){
		const hoje = new Date();
		const dia = String(hoje.getDate()).padStart(2, '0');
		const mes = String(hoje.getMonth() + 1).padStart(2, '0');
		const ano = hoje.getFullYear();

		const dataHoje = `${ano}-${mes}-${dia}`;
		$('.data_vencimento').first().val(dataHoje)
	}
});

function beepSucesso(){
	let alerta = $('#alerta_sonoro').val()
	if(alerta == 1){
		var audio = new Audio('/audio/beep.mp3');
		audio.addEventListener('canplaythrough', function() {
			audio.play();
		});
	}
}
function beepErro(){
	let alerta = $('#alerta_sonoro').val()
	if(alerta == 1){
		var audio = new Audio('/audio/beep_error.mp3');
		audio.addEventListener('canplaythrough', function() {
			audio.play();
		});
	}
}

var infoModal = false
function infoProduto(id){
	infoModal = true

	setTimeout(() => {
		infoModal = false
	}, 500)
	$.get(path_url + 'api/produtos/info', { produto_id: id })
	.done((res) => {
		$('#modal_info_produto .modal-body').html(res)
		$('#modal_info_produto').modal('show')

	}).fail((err) => {
		console.log(err)
	})
}

$(document).on('blur', '#inp-desconto', function(e){
	calculaTotal()
});
$(document).on('blur', '#inp-acrescimo', function(e){
	calculaTotal()
});

$('.btn-gerar-fatura').click(() => {
	calculaTotal()
	$('#modal_finalizar_pdv2').modal('hide')
	$('#modal_fatura_venda').modal('show')
	
	let total = convertMoedaToFloat($('.total').text())
	$('.lbl-total_fatura').text("R$ " + convertFloatToMoeda(total))
})

$('.btn-store-fatura').click(() => {
	// console.clear()
	let desconto = convertMoedaToFloat($('#inp-valor_desconto').val())
	let acrescimo = convertMoedaToFloat($('#inp-valor_acrescimo').val())
	let total = convertMoedaToFloat($('.total').text())

	let total_venda = total + acrescimo - desconto
	if(!$('#inp-parcelas_fatura').val()){
		swal("Erro", "Informe a quantidade de parcelas!", "error")
		return;
	}
	if(!$('#inp-intervalo_fatura').val()){
		swal("Erro", "Informe o intervalo!", "error")
		return;
	}
	let data = {
		entrada_fatura: $('#inp-entrada_fatura').val(),
		parcelas_fatura: $('#inp-parcelas_fatura').val(),
		intervalo_fatura: $('#inp-intervalo_fatura').val(),
		primeiro_vencimento_fatura: $('#inp-primeiro_vencimento_fatura').val(),
		tipo_pagamento_fatura: $('#inp-tipo_pagamento_fatura').val(),
		total: total_venda
	}
	// console.log(data)
	$.get(path_url + "api/frenteCaixa/gerar-fatura-pdv2", data)
	.done((success) => {
        // console.log(success)
        $('#modal_finalizar_pdv2').modal('show')
        setTimeout(() => {
        	$(".fatura").html(success)
        	$('#modal_fatura_venda').modal('hide')


        }, 100)
        

    })
	.fail((err) => {
		console.log(err);
	});
})

$(document).on('click', '.btn-adiciona', function(){
	$div = $(this).closest('.product-line')
	let qtd = $div.find('.quantidade').val()
	qtd = parseInt(qtd)+1
	$div.find('.quantidade').val(qtd)
	$div.find('.qtd').val(qtd)

	let vlUnit = parseFloat($div.find('.valor_unitario').val())
	// console.log(parseFloat(qtd)*vlUnit)
	$div.find('.subtotal_item').val(parseFloat(qtd)*vlUnit)
	$div.find('.sub_total').text("R$ " +convertFloatToMoeda(parseFloat(qtd)*vlUnit))
	calculaTotal()
});

$(document).on('click', '.btn-subtrai', function(){
	$div = $(this).closest('.product-line')
	let qtd = $div.find('.quantidade').val()
	qtd = parseInt(qtd)-1

	if(qtd <= 0){
		$div.find('.btn-remove').trigger('click')
	}
	$div.find('.quantidade').val(qtd)
	$div.find('.qtd').val(qtd)

	let vlUnit = parseFloat($div.find('.valor_unitario').val())
	// console.log(parseFloat(qtd)*vlUnit)
	$div.find('.subtotal_item').val(parseFloat(qtd)*vlUnit)
	$div.find('.sub_total').text("R$ " +convertFloatToMoeda(parseFloat(qtd)*vlUnit))
	calculaTotal()
});

var _code = 0
var _produto_id = 0
var _adicionaisSelecionados = []

$(document).on('click', '.btn-detalhes', function(){
	let indice = $('.btn-detalhes').index(this)
	$div = $(this).closest('.product-line')
	let produto_id = _produto_id = $div.find('.produto_id').val()
	let valor_unitario = $div.find('.valor_unitario').val()
	let sabores = $div.find('.sabores').val()
	_code = $div.find('.code').val()
	let numero_comanda = $('#numero_comanda').val()

	$('#detalhes_item_mesas').modal('show')
	$.get(path_url + "api/frenteCaixa/detalhes-item",
	{ 
		produto_id: produto_id,
		indice: indice,
		numero_comanda: numero_comanda
	})
	.done((e) => {
		// console.log(e);
		$('#detalhes_item_mesas .modal-body').html(e)

		if($('#inp-tamanho_id').val()){

			$.get(path_url + "api/produtos/get-pizzas",
			{ 
				empresa_id: $('#empresa_id').val(), 
				produto_id: _produto_id,
				tamanho_id: $('#inp-tamanho_id').val()
			})
			.done((success) => {
				$('.modal-body .pizzas').html(success)
				if(sabores){
					SABORESSELECIONADOS = []
					let arraySabores = JSON.parse(sabores);

					arraySabores.map((x) => {
						SABORESSELECIONADOS.push(parseInt(x))
						$('.bg-'+x).addClass('bg-info')
						// console.log('.bg-'+x)
					})
				}
			})
			.fail((err) => {
				console.log(err)
			})
		}

	})
	.fail((err) => {
		console.log(err);
	});
});

$(document).on('click', '#btn_fiscal', function(){
	finalizar('fiscal')
})

$(document).on('click', '.checkbox_adicional', function(){
	// console.clear()
	let soma = parseFloat($('#valor_original').val())
	if(VALORPIZZA > 0){
		soma = parseFloat(VALORPIZZA)
	}

	_adicionaisSelecionados = []
	$('.checkbox_adicional').each(function () {
		if($(this).is(':checked')){

			let valor = $(this).attr('adicional-valor')
			let id = $(this).attr('adicional-id')
			// console.log(valor)
			soma += parseFloat(valor)
			_adicionaisSelecionados.push(id)
		}
	})

	setTimeout(() => {
		$('#inp-valor_unitario_item').val(convertFloatToMoeda(soma))
	}, 10)
	
});

$(document).on('click', '#btn_salvar_detalhes', function(){
	// console.log("SABORESSELECIONADOS", SABORESSELECIONADOS)
	console.clear()
	$(".product-line").each(function(){
		if($(this).find('.code').val() == _code){
			let observacao = $('#inp-observacao_item').val()
			let valor = convertMoedaToFloat($('#inp-valor_unitario_item').val())

			$(this).find('.observacao').val(observacao)
			$(this).find('.valor_unitario').val(valor)
			let qtd = parseFloat($(this).find('.quantidade').val())
			$(this).find('.subtotal_item').val(valor*qtd)
			$(this).find('.adicionais').val(_adicionaisSelecionados)
			$(this).find('.tamanho_id').val($("#inp-tamanho_id").val())
			$(this).find('.sabores').val(JSON.stringify(SABORESSELECIONADOS))

			$(this).find('.sub_total').text("R$ " + convertFloatToMoeda(valor*qtd))
			$(this).find('.unitario').text("R$ " + convertFloatToMoeda(valor))

			calculaTotal()
		}
	})
});

$(document).on('click', '#btn_nao_fiscal', function(){
	finalizar('nao_fiscal')
})

var emitirNfce = false
function finalizar(tipo){
	let soma = 0

	let tipoPagamentoInvalido = 0
	let dataInvalida = 0
	let total = convertMoedaToFloat($('.total').text())

	$('.fatura .valor_integral_row').each(function () {
		soma += convertMoedaToFloat($(this).val());
	});

	$('.fatura .tipo_pagamento').each(function () {
		if(!$(this).val()){
			tipoPagamentoInvalido = 1
		}
	});

	$('.fatura .data_vencimento').each(function () {
		if(!$(this).val()){
			dataInvalida = 1
		}
	});

	total = parseFloat(total.toFixed(2))
	soma = parseFloat(soma.toFixed(2))

	setTimeout(() => {
		if(total != soma && TROCODDEFINIDO == 0){
			swal("Erro", "Total divergente da soma da fatura.", "error")
			return;
		}
		if(tipoPagamentoInvalido){
			swal("Erro", "Defina o tipo de pagamento para todas linhas de pagamento.", "error")
			return;
		}
		if(dataInvalida){
			swal("Erro", "Defina o vencimento para todas linhas de pagamento.", "error")
			return;
		}

		if(tipo == 'fiscal'){
			emitirNfce = true
		}


		$("#form-comanda").submit()
	}, 20)
}

$("#form-comanda").on("submit", function (e) {

	e.preventDefault();
	const form = $(e.target);
	var json = $(this).serializeFormJSON();

	json.empresa_id = $('#empresa_id').val()
	json.usuario_id = $('#usuario_id').val()
	json.valor_total = convertMoedaToFloat($('.total').text())
	json.numero_comanda = $('#numero_comanda').val()

	// console.log(">>>>>>>> salvando ", json);
	// console.log(">>>>>>>> troco ", json.troco);
	// return

	$.post(path_url + 'api/frenteCaixa/store-comanda', json)
	.done((success) => {
		// console.log(success)
		if (emitirNfce == true) {

			gerarNfce(success)
		} else {
			swal({
				title: "Sucesso",
				text: "Venda finalizada com sucesso, deseja imprimir o comprovante?",
				icon: "success",
				buttons: true,
				buttons: ["Não", "Sim"],
				dangerMode: true,
			}).then((isConfirm) => {
				if (isConfirm) {
					imprimirNaoFiscal(success.id)
				} 
				location.href = '/frontbox-mesas';
			});
		}
	}).fail((err) => {
		swal("Erro", err.responseJSON, "error")
		console.log(err)
	})
})

function gerarNfce(venda) {

	let empresa_id = $("#empresa_id").val();

	$.post(path_url + "api/nfce_painel/emitir", {
		id: venda.id,
	})
	.done((success) => {
		swal("Sucesso", "NFCe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
		.then(() => {
			window.open(path_url + 'nfce/imprimir/' + venda.id, "_blank")
			setTimeout(() => {
				location.href = path_url+'frontbox-mesas'
			}, 100)
		})
	})
	.fail((err) => {
		console.log(err)

		swal("Algo deu errado", err.responseJSON, "error")

	})
}

function imprimirNaoFiscal(id){
	let impressao_sem_janela_cupom = $('#impressao_sem_janela_cupom').val()
	if(impressao_sem_janela_cupom == 0){
		var disp_setting="toolbar=yes,location=no,";
		disp_setting+="directories=yes,menubar=yes,";
		disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

		var docprint=window.open(path_url+"frontbox/imprimir-nao-fiscal/"+id,"",disp_setting);

		docprint.focus();
	}else{
		window.open(path_url+"frontbox/imprimir-nao-fiscal-html/"+id)
	}
}

$.fn.serializeFormJSON = function () {

	var o = {};
	var a = this.serializeArray();
	$.each(a, function () {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};

$(".btn-delete-comanda").on("click", function (e) {
	e.preventDefault();

	swal({
		title: "Você está certo?",
		text: "Uma vez deletado, você não poderá recuperar essa comanda novamente!",
		icon: "warning",
		buttons: true,
		buttons: ["Cancelar", "Excluir"],
		dangerMode: true,
	}).then((isConfirm) => {
		if (isConfirm) {
			$('#form-delete-comanda').submit();
		} else {
			swal("", "Este item está salvo!", "info");
		}
	});
});
