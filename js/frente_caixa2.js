document.addEventListener("DOMContentLoaded", function(event) {
	setTimeout(() => {
		$('.div-main').removeClass('d-none')
	}, 100)
});
$(function(){

	if(senhaAcao != ''){
		$('.inp-modal-pass').attr('readonly', 1)
	}

	let finalizacao_pdv = $('#inp-finalizacao_pdv').val()
	if(finalizacao_pdv == 'nao_fiscal'){
		$('#btn_fiscal').addClass('d-none')
	}else if(finalizacao_pdv == 'fiscal'){
		$('#btn_nao_fiscal').addClass('d-none')
	}
	setTimeout(() => {
		$('#cat_todos').first().trigger('click')

		$("#inp-conta_empresa_sangria_id").select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar a conta",
			width: "100%",
			theme: "bootstrap4",
			dropdownParent: '#sangria_caixa',
			ajax: {
				cache: true,
				url: path_url + "api/contas-empresa",
				dataType: "json",
				data: function (params) {
					console.clear();
					let empresa_id = $('#empresa_id').val()
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

						o.text = v.nome;
						o.value = v.id;
						results.push(o);
					});
					return {
						results: results,
					};
				},
			},
		});

		$("#inp-conta_empresa_suprimento_id").select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar a conta",
			width: "100%",
			theme: "bootstrap4",
			dropdownParent: '#suprimento_caixa',
			ajax: {
				cache: true,
				url: path_url + "api/contas-empresa",
				dataType: "json",
				data: function (params) {
					console.clear();
					let empresa_id = $('#empresa_id').val()
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

						o.text = v.nome;
						o.value = v.id;
						results.push(o);
					});
					return {
						results: results,
					};
				},
			},
		});
	}, 100)
	$('#inp-pesquisa').val('')

	$(document).on('click', '.categorias-pagination a', function(event){
		event.preventDefault()
		var page = $(this).attr('href').split('page=')[1];
		$.get(path_url + 'api/frenteCaixa/categorias-page?page='+page, { 
			empresa_id: $('#empresa_id').val(), 
		})
		.done((res) => {
			$('.div-categorias').html(res)
		}).fail((err) => {
			console.log(err)
			
		})
	})

	$(document).on('click', '.marcas-pagination a', function(event){
		event.preventDefault()
		var page = $(this).attr('href').split('page=')[1];
		$.get(path_url + 'api/frenteCaixa/marcas-page?page='+page, { empresa_id: $('#empresa_id').val() })
		.done((res) => {
			$('.div-marcas').html(res)
		}).fail((err) => {
			console.log(err)
			
		})
	})

	$(document).on('click', '.produtos-pagination a', function(event){
		event.preventDefault()

		let data = {
			categoria_id : CATEGORIAID,
			marca_id : MARCAID,
			empresa_id: $('#empresa_id').val(),
			lista_id: $('#lista_id').val(),
			local_id: $('#local_id').val(),
		}
		var page = $(this).attr('href').split('page=')[1];
		$.get(path_url + 'api/frenteCaixa/produtos-page?page='+page, data)
		.done((res) => {
			$('.div-produtos').html(res)
		}).fail((err) => {
			console.log(err)
		})
	})

	if($('#pedido_valor_entrega').val()){
		$('#inp-acrescimo').val(convertFloatToMoeda($('#pedido_valor_entrega').val()))
		$('#inp-tipo_acrescimo').val('R$').change()
	}

	if($('#venda_id').val() == '0'){
		$('#inp-desconto').val('0')
		$('#inp-acrescimo').val('0')
		$('#inp-valor_total').val('0')
	}else{
		calculaTotal()
	}

	setTimeout(() => {
		$("#inp-pesquisa").focus();
	}, 500)
})

function modalFrete(){
	$('#modal_frete').modal('show')
	let vFrete = $('#inp-valor_frete').val()
	$('#valor_frete').val(vFrete)
}

$('.btn-save-frete').click(() => {
	let valorFrete = convertMoedaToFloat($('#valor_frete').val())
	if(valorFrete){
		VALORFRETE = valorFrete
		$('#inp-valor_frete').val(convertFloatToMoeda(valorFrete))
		calculaTotal()
	}

	$('#modal_frete').modal('hide')
})

$("#inp-transportadora_id").select2({
	minimumInputLength: 2,
	language: "pt-BR",
	placeholder: "Digite para buscar a transportadora",
	dropdownParent: '#modal_frete',
	ajax: {
		cache: true,
		url: path_url + "api/transportadoras/pesquisa",
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

$("body").on("click", ".btn-incrementa", function () {

	let inp = $(this).prev()[0]
	let prodRow = $(this).closest('.products').find('.produto_id')
	let produto_id = prodRow.val()
	if (inp.value) {
		let v = convertMoedaToFloat(inp.value)

		$.get(path_url + "api/produtos/valida-estoque", { qtd: v+1, product_id: produto_id, local_id: $('#local_id').val() })
		.done((res) => {
			console.log(res)
			v += 1
			inp.value = parseInt(v)
			$inpVlUnit = $(this).closest('.products').find('.valor_unitario')
			if(res.quantidade_atacado && res.quantidade_atacado >= v){
				$inpVlUnit.val(convertFloatToMoeda(res.valor_atacado))
			}
            // calculaTotal()
        })
		.fail((err) => {
            // console.log(err);
            inp.value = v
            swal("Alerta", err.responseJSON, "warning")
        });

	}
})

function registrarLog(data){
	// console.clear()
	// console.log("LOG", data)
	$.post(path_url + 'api/frenteCaixa/pdf-log', data)
	.done((res) => {
        // console.log(res)
    })
	.fail((err) => {
        // console.log(err)
    })
}

function resetProdutos() {
	CATEGORIAID = 0
	MARCAID = 0
	getProdutos()
}

$('.btn-gerar-fatura').click(() => {
	$('#modal_finalizar_pdv2').modal('hide')
	$('#modal_fatura_venda').modal('show')
	let desconto = convertMoedaToFloat($('#inp-valor_desconto').val())
	let acrescimo = convertMoedaToFloat($('#inp-valor_acrescimo').val())
	let total = $('#inp-valor_total').val()

	let total_venda = total
	$('.lbl-total_fatura').text("R$ " + convertFloatToMoeda(total_venda))
})

$('.btn-store-fatura').click(() => {
	console.clear()
	let desconto = convertMoedaToFloat($('#inp-valor_desconto').val())
	let acrescimo = convertMoedaToFloat($('#inp-valor_acrescimo').val())
	let total = $('#inp-valor_total').val()

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

var CATEGORIAID = 0
var MARCAID = 0
$(document).on('click', '.list-categoria', function(){
	CATEGORIAID = $(this).data('id')

	$('.list-categoria').removeClass('active')
	$('.categoria-'+CATEGORIAID).addClass('active')
	getProdutos()
})

$(document).on('blur', '#inp-desconto', function(e){
	calculaDesconto()
});
$(document).on('change', '#inp-tipo_desconto', function(e){
	calculaDesconto()
});

function calculaDesconto(){
	let desconto = convertMoedaToFloat($('#inp-desconto').val())
	let tipo_desconto = $('#inp-tipo_desconto').val()
	let total = $('#inp-valor_total').val()
	let valor_desconto = desconto
	if(tipo_desconto == '%'){
		valor_desconto = total*(desconto/100)
	}

	$('#inp-valor_desconto').val(convertFloatToMoeda(valor_desconto))
	calculaTotal()

	let data = {
		empresa_id: $("#empresa_id").val(),
		usuario_id: $('#usuario_id').val(),
		acao: 'Desconto',
		valor_desconto: valor_desconto,

	}
	registrarLog(data)
}

function removeItem(code){
	if(senhaAcao != ''){
		swal({
			title: "Senha para remover item",
			text: "Informe a senha para continuar",
			content: {
				element: "input",
				attributes: {
					type: "password",
					placeholder: "Digite a senha"
				}
			},
			button: {
				text: "Ok",
				closeModal: false,
				type: "error",
			},
		}).then((v) => {
			if(v == senhaAcao){
				removeItemLinha(code)
			}else{
				swal("Erro", "Senha incorreta!", "error")
			}
		})
	}else{
		swal({
			title: "Você está certo?",
			text: "Deseja realmente remover este item da venda?",
			icon: "warning",
			buttons: true,
			buttons: ["Cancelar", "Excluir"],
			dangerMode: true,
		}).then((isConfirm) => {
			if (isConfirm) {

				removeItemLinha(code)
				calculaTotal()
			} else {
				swal("", "Este item está salvo!", "info");
			}
		});
	}
}

function removeItemLinha(code){
	let data = {
		empresa_id: $("#empresa_id").val(),
		usuario_id: $('#usuario_id').val(),
		acao: 'Item removido',
		produto_id: $('.product-line-'+code).find('.produto_id').val()
	}

	$('.product-line-'+code).remove()
	swal("Sucesso", "Item removido!", "success")
	calculaTotal()
	registrarLog(data)
}

function buscarVariacoes(produto_id){
	$.get(path_url + "api/variacoes/find", { produto_id: produto_id })
	.done((res) => {
		$('#modal_variacao .modal-body').html(res)
		$('#modal_variacao').modal('show')
	})
	.fail((err) => {
		console.log(err)
		swal("Algo deu errado", "Erro ao buscar variações", "error")
	})
}

function editItem(code){
	if(senhaAcao == ''){
		let produto_id = $('.product-line-'+code).find('.produto_id').val()
		let quantidade = $('.product-line-'+code).find('.quantidade').val()
		let valor_unitario = $('.product-line-'+code).find('.valor_unitario').val()
		let data = {
			produto_id: produto_id,
			code: code,
			quantidade: quantidade,
			valor_unitario: valor_unitario
		}
		$.get(path_url + 'api/frenteCaixa/edit-item', data)
		.done((res) => {
			// console.log(res)
			$('#edit_item_pdv').modal('show')
			$('#edit_item_pdv .modal-body').html(res)

		}).fail((err) => {
			console.log(err)
		})
	}else{
		toastr.error('Você não tem permissão para editar o item da venda!');
	}
}

$(document).on('click', '#btn-edit-item', function(e){
	let id = $('#inp-id-item').val()
	let code = $('#inp-code-item').val()
	let qtd = $('#inp-qtd-item').val()
	let valor_unitario = convertMoedaToFloat($('#inp-valor-unitario-item').val())

	$.get(path_url + "api/orcamentos/valida-desconto", 
	{ 
		produto_id: id, valor: valor_unitario, empresa_id: $('#empresa_id').val(), pdv: 1
	}).done((res) => {
		$('.product-line-'+code).find('.quantidade').val(qtd)
		$('.product-line-'+code).find('.qtd-row').val(qtd)
		$('.product-line-'+code).find('.valor_unitario').val(convertFloatToMoeda(valor_unitario))
		$('.product-line-'+code).find('.subtotal_item').val(convertFloatToMoeda(valor_unitario*qtd))

		$('.product-line-'+code).find('.price').text("R$ " + convertFloatToMoeda(valor_unitario*qtd))
		$('.product-line-'+code).find('.qtd-row').val(qtd)
		$('#edit_item_pdv').modal('hide')
		calculaTotal()
	})
	.fail((err) => {
		console.log(err)
		let v = err.responseJSON
		$('#inp-valor-unitario-item').val(convertFloatToMoeda(v))
		swal("Erro", "Valor minímo para este item " + convertFloatToMoeda(v), "error")
	})	
})

$(document).on('blur keydown', '.qtd-row', function(e){

	let code = $(this).next().data('code')
	let qtd = convertMoedaToFloat($(this).val())
	setTimeout(() => {
		$('.product-line-'+code).find('.quantidade').val(qtd)

		let valor_unitario = convertMoedaToFloat($('.product-line-'+code).find('.valor_unitario').val())

		$('.product-line-'+code).find('.price').text("R$ " + convertFloatToMoeda(valor_unitario*qtd))
		$('.product-line-'+code).find('.subtotal_item').val(convertFloatToMoeda(valor_unitario*qtd))

		calculaTotal()
	}, 10)
})

$(document).on('click', '.increment-decrement', function(e){
	let bt = $(this)[0].innerText
	let code = $(this).data('code')
	let qtd = 0;
	if(bt == '+'){
		qtd = $(this).prev().val()
		qtd++;
	}else{
		qtd = $(this).next().val()
		qtd--;
		if(qtd <= 0){
			removeItem(code)
			return;
		}
	}

	setTimeout(() => {
		$('.product-line-'+code).find('.quantidade').val(qtd)

		$('.product-line-'+code).find('.qtd-row').val(qtd)
		let valor_unitario = convertMoedaToFloat($('.product-line-'+code).find('.valor_unitario').val())

		$('.product-line-'+code).find('.price').text("R$ " + convertFloatToMoeda(valor_unitario*qtd))
		$('.product-line-'+code).find('.subtotal_item').val(convertFloatToMoeda(valor_unitario*qtd))

		calculaTotal()

	}, 10)
});

function selecionaLista(){
	let tipo_pagamento_lista = $('#inp-tipo_pagamento_lista').val()
	let funcionario_lista_id = $('#inp-funcionario_lista_id').val()
	let lista_preco_id = $('#inp-lista_preco_id').val()

	if(!lista_preco_id){
		swal("Alerta", "Selecione a lista", "warning")
		return;
	}

	if(tipo_pagamento_lista){
		$('#inp-tipo_pagamento').val(tipo_pagamento_lista).change()
	}
	if(funcionario_lista_id){
		$.get(path_url + "api/funcionarios/find", {id: funcionario_lista_id})
		.done((res) => {
			var newOption = new Option(res.nome, res.id, true, false);
			$('#inp-funcionario_id').append(newOption);
			$('.funcionario_selecionado').text(res.nome)

		})
		.fail((err) => {
			console.log(err);
		});
	}

	$('#lista_id').val(lista_preco_id)
	setTimeout(() => {
		resetProdutos()
	}, 10)
	setTimeout(() => {
		$("#inp-pesquisa").focus();
	}, 500)
}

$("body").on("change", ".tipo_pagamento", function () {
	let cliente_id = $('#inp-cliente_id').val()
	if($(this).val() == '06' && !cliente_id){
		swal("Alerta", "Informe o cliente!", "warning")
		$(this).val('').change()
	}
})

$('#inp-valor_cashback').blur(() => {
	validaCashBack()
})

function validaCashBack(){

	let valor_setado = $('#inp-valor_cashback').val()
	valor_setado = valor_setado.replace(",", ".")
	valor_setado = parseFloat(valor_setado)
	let total = $('#inp-valor_total').val()

	if(total == 0){
		swal("Alerta", "Informe ao menos um produto para continuar", "warning")
		return;
	}
	if(CashBackConfig){
		let percentual_maximo_venda = CashBackConfig.percentual_maximo_venda
		let valor_maximo = total * (percentual_maximo_venda/100)

		if(valor_setado > valor_maximo){
			swal("Erro", "Valor máximo permitido R$ " + convertFloatToMoeda(valor_maximo), "warning")
			$('#inp-valor_cashback').val('')
		}else if(valor_setado > valorCashBack){
			swal("Erro", "Valor ultrapassou R$ " + convertFloatToMoeda(valorCashBack), "warning")
			$('#inp-valor_cashback').val('')
		}else{

		}
	}
}

$(document).on("change", "#inp-cliente_id", function () {
	clienteCNPJ = false
	let cliente_id = $(this).val()

	$.get(path_url + "api/clientes/find/" + $(this).val())
	.done((cliente) => {
		console.log(cliente)
		$('#_cashback').modal('show')

		$.get(path_url + "api/clientes/cashback/" + cliente_id)
		.done((e) => {
			if(e){
				CashBackConfig = e
				valorCashBack = e.valor_cashback

				$('.cashback-div').removeClass('d-none')
				$('.info_cash_back').text('*percentual de cashback para uso ' + e.percentual_maximo_venda + '%')

			}
			$('.valor-cashback-disponivel').text('R$ ' + convertFloatToMoeda(e.valor_cashback))
		})
		.fail((e) => {
			$('.cashback-div').addClass('d-none')
		});

		if(cliente.lista_preco){

			$('#lista_id').val(cliente.lista_preco.id)
			setTimeout(() => {
				resetProdutos()
			}, 10)
			setTimeout(() => {
				$("#inp-pesquisa").focus();
			}, 500)
		}
	})
	.fail((err) => {
		console.log(err);
	});

	$.get(path_url + "api/clientes/find/" + cliente_id)
	.done((cliente) => {
		$('.p-cliente').html("<label>Cliente: <strong>"+cliente.info+"</strong></label>");
		if(cliente.cpf_cnpj.replace(/[^0-9]/g,'').length == 14){
			$('.p-cliente').append("<br><strong class='text-danger'>Será emitida NFe cliente selecionado com CNPJ</strong>");
			clienteCNPJ = true
		}

	})
	.fail((err) => {
		console.log(err);
	});
})

$("body").on("change", "#inp-lista_preco_id", function () {
	$.get(path_url + "api/lista-preco/find", {id: $(this).val()})
	.done((res) => {
		$('#inp-tipo_pagamento_lista').val(res.tipo_pagamento).change()

		if(res.funcionario_id){
			$('#inp-funcionario_lista_id').val(res.funcionario_id).change();
		}
	})
	.fail((err) => {
		console.log(err);
	});
})

$("#lista_precos select").each(function () {

	let id = $(this).prop("id");

	if (id == "inp-lista_preco_id") {

		$(this).select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar a lista de preço",
			theme: "bootstrap4",
			dropdownParent: $(this).parent(),
			ajax: {
				cache: true,
				url: path_url + "api/lista-preco/pesquisa",
				dataType: "json",
				data: function (params) {
					var query = {
						pesquisa: params.term,
						empresa_id: $("#empresa_id").val(),
						usuario_id: $("#usuario_id").val(),
						tipo_pagamento_lista: $("#inp-tipo_pagamento_lista").val(),
						funcionario_lista_id: $("#inp-funcionario_lista_id").val(),
					};
					return query;
				},
				processResults: function (response) {
					var results = [];

					$.each(response, function (i, v) {
						var o = {};
						o.id = v.id;

						o.text = v.nome + " " + v.percentual_alteracao + "%";
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
});

$(document).on('blur', '#inp-acrescimo', function(e){
	calculaAcrescimo()
});
$(document).on('change', '#inp-tipo_acrescimo', function(e){
	calculaAcrescimo()
});

$(document).on('keyup', '#inp-valor_frete', function(e){
	calculaAcrescimo()
});

function calculaAcrescimo(){
	let acrescimo = convertMoedaToFloat($('#inp-acrescimo').val())
	let tipo_acrescimo = $('#inp-tipo_acrescimo').val()
	let total = $('#inp-valor_total').val()
	let valor_acrescimo = acrescimo

	if(tipo_acrescimo == '%'){
		valor_acrescimo = total*(acrescimo/100)
	}
	// console.log(valor_acrescimo)
	$('#inp-valor_acrescimo').val(convertFloatToMoeda(valor_acrescimo))
	calculaTotal()

	if(valor_acrescimo > 0){
		let data = {
			empresa_id: $("#empresa_id").val(),
			usuario_id: $('#usuario_id').val(),
			acao: 'Acréscimo',
			valor_acrescimo: valor_acrescimo,
		}
		registrarLog(data)
	}
}

$(document).on('keyup', '#inp-pesquisa', function(e){
	let pesquisa = $(this).val()
	let isCodigoBarras = pesquisa.substring(0,1) == "@" ? 1 : 0
	if(isCodigoBarras){
		pesquisa = pesquisa.substring(1, pesquisa.length)
	}
	if(!isNumeric(pesquisa) || isCodigoBarras){
		if(pesquisa.length > 1){
			let data = {
				pesquisa: pesquisa,
				empresa_id: $('#empresa_id').val(),
				local_id: $('#local_id').val(),
			}
			$.get(path_url + 'api/frenteCaixa/pesquisa-produto', data)
			.done((res) => {
				// console.log(res)
				$('.results-list').removeClass('d-none')
				$('.results-list').html(res)
			}).fail((err) => {
				console.log(err)
			})
		}
	}

})

function fecharAutocomplete(){
	$('.results-list').html('').addClass('d-none')
}

$(document).on('keypress', '.qtd-row', function(e){
	if(e.which == 13) {
		e.preventDefault();
	}
});

var naoAbriModalVariacao = 0
$(document).on('keypress', '#inp-pesquisa', function(e){

	let pesquisa = $(this).val()

	// if(pesquisa.length > 1){
	// 	let data = {
	// 		pesquisa: pesquisa,
	// 		empresa_id: $('#empresa_id').val(),
	// 		local_id: $('#local_id').val(),
	// 	}
	// 	$.get(path_url + 'api/frenteCaixa/pesquisa-produto', data)
	// 	.done((res) => {
	// 		console.log(res)
	// 		$('.results-list').removeClass('d-none')
	// 		$('.results-list').html(res)
	// 	}).fail((err) => {
	// 		console.log(err)
	// 	})
	// }

	if(e.which == 13) {

		$('.results-list').html('').addClass('d-none')
		e.preventDefault();

		if(isNumeric(pesquisa)){

			$.get(path_url + "api/produtos/findByBarcode",
			{
				barcode: pesquisa,
				empresa_id: $('#empresa_id').val(),
				lista_id: $('#lista_id').val(),
				usuario_id: $('#usuario_id').val()
			})
			.done((e) => {
				// console.log(e)
				if(e.status == 0){
					toastr.error('Produto inativo!');
					return
				}
				$('.results-list').html('').addClass('d-none')
				if(e.id){
					// console.log(e.codigo_variacao)
					naoAbriModalVariacao = 1
					addProduto(e.id, e.codigo_variacao)
				}else{
					buscarPorReferencia(pesquisa)
				}
			})
			.fail((err) => {
				console.log(err);
				buscarPorReferencia(pesquisa)
			});

		}else{
			swal("Alerta", "Informe um código válido", "error")
			return
		}
	}

})

function isNumeric(value) {
	return /^-?\d+$/.test(value);
}

function buscarPorReferencia(barcode) {

	$.get(path_url + "api/produtos/findByBarcodeReference2",
	{
		barcode: barcode,
		empresa_id: $('#empresa_id').val(),
		usuario_id: $('#usuario_id').val()
	})
	.done((e) => {
		// console.log(e)
		$('#inp-pesquisa').val('')
		$('.itens-cart').append(e)
		// $(".table-itens tbody").append(e);
		calculaTotal();
	})
	.fail((e) => {
		console.log(e);
		swal("Erro", "Produto não localizado!", "error")
	});
}

$(document).on('click', '.inp-modal-pass', function(){
	if(senhaAcao != ''){
		swal({
			title: "Senha para desconto",
			text: "Informe a senha para continuar",
			button: {
				text: "Ok",
				closeModal: false,
				type: "error",
			},
			content: {
				element: "input",
				attributes: {
					type: "password",
					placeholder: "Digite a senha"
				}
			},
		}).then((v) => {
			if(v == senhaAcao){
				senhaAcao = ''
				swal.close();
				$('.inp-modal-pass').removeAttr('readonly')
			}else{
				swal("Erro", "Senha incorreta!", "error")
			}
		})
	}
});

$(document).on('click', '.list-marca', function(){
	MARCAID = $(this).data('id')

	$('.list-marca').removeClass('active')
	$('.marca-'+MARCAID).addClass('active')
	getProdutos()
})

function getProdutos(){

	let data = {
		categoria_id : CATEGORIAID,
		marca_id : MARCAID,
		empresa_id: $('#empresa_id').val(),
		lista_id: $('#lista_id').val(),
		local_id: $('#local_id').val(),
	}

	$.get(path_url + 'api/frenteCaixa/produtos-page?page=1', data)
	.done((res) => {
		$('.div-produtos').html(res)
	}).fail((err) => {
		console.log(err)
	})
}

function infoProduto(id){
	$.get(path_url + 'api/produtos/info', { produto_id: id })
	.done((res) => {
		$('#modal_info_produto .modal-body').html(res)
		$('#modal_info_produto').modal('show')

	}).fail((err) => {
		console.log(err)
	})
}

function validaCaixa() {
	let abertura = $('#abertura').val()
	if (!abertura) {
		$('#modal-abrir_caixa').modal('show')
		return
	}
}


function selecionarVariacao(id, descricao, valor){
	$('#modal_variacao').modal('hide')

    // add
    let abertura = $('#abertura').val()
    if (abertura) {
    	$.post(path_url + 'api/frenteCaixa/add-produto', { 
    		produto_id: id, 
    		lista_id: $('#lista_id').val(), 
    		qtd: 1,
    		local_id: $('#local_id').val(),
    		variacao_id: id
    	})
    	.done((res) => {
    		// console.log(res)
    		$('.itens-cart').append(res)

    	}).fail((err) => {
    		console.log(err)
    	})
    }else{
    	swal("Atenção", "Abra o caixa para continuar!", "warning").then(() => {
    		validaCaixa()
    	})
    }
}

function addProduto(id, variacao_id = null){
	$('.results-list').html('').addClass('d-none')
	$('#inp-pesquisa').val('')
	let qtd = 0;

	if(variacao_id && naoAbriModalVariacao == 0){
		buscarVariacoes(id)
		return ;
	}

	naoAbriModalVariacao = 0

	let abertura = $('#abertura').val()
	let agrupar_itens = $('#agrupar_itens').val()

	$('.products').each(function () {
		if(id == $(this).find('.produto_id').val()){
			qtd += parseFloat($(this).find('.quantidade').val())
		}
	})

	if (abertura) {
		$.post(path_url + 'api/frenteCaixa/add-produto', { 
			produto_id: id, 
			lista_id: $('#lista_id').val(), 
			qtd: qtd,
			local_id: $('#local_id').val(),
			variacao_id: variacao_id
		})
		.done((res) => {
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
			$('#inp-pesquisa').focus()
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

function calculaTotal(){
	let desconto = convertMoedaToFloat($('#inp-valor_desconto').val())
	let acrescimo = convertMoedaToFloat($('#inp-valor_acrescimo').val())
	let valor_frete = convertMoedaToFloat($('#inp-valor_frete').val())
	setTimeout(() => {
		let total = 0
		$(".price").each(function () {
			total += convertMoedaToFloat($(this)[0].innerText);
		});

		total = total + acrescimo - desconto + valor_frete
		$('.total').text(convertFloatToMoeda(total))
		$('#inp-valor_total').val(total.toFixed(2))
		$('#inp-valor_total2').val(total.toFixed(2))
	}, 100)
}

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

$(document).on('click', '#btn_nao_fiscal', function(){
	finalizar('nao_fiscal')
})

$(document).on('click', '#btn_fiscal', function(){
	finalizar('fiscal')
})

$('.btn-vendas-suspensas').click(() => {
	$.get(path_url + "api/frenteCaixa/venda-suspensas",
	{
		empresa_id: $('#empresa_id').val(),
	})
	.done((data) => {
		$('.table-vendas-suspensas tbody').html(data)
	})
	.fail((e) => {
		console.log(e);
	});
})

$("body").on("click", ".btn-delete", function (e) {

	e.preventDefault();
	var form = $(this).parents("form").attr("id");

	swal({
		title: "Você está certo?",
		text: "Uma vez deletado, você não poderá recuperar esse item novamente!",
		icon: "warning",
		buttons: true,
		buttons: ["Cancelar", "Excluir"],
		dangerMode: true,
	}).then((isConfirm) => {
		if (isConfirm) {

			document.getElementById(form).submit();
		} else {
			swal("", "Este item está salvo!", "info");
		}
	});
});

var emitirNfce = false
var clienteCNPJ = false

function finalizar(tipo){
	let soma = 0

	let tipoPagamentoInvalido = 0
	let dataInvalida = 0
	let total = parseFloat($('#inp-valor_total').val())
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
		// if(total != soma){
		// 	swal("Erro", "Total divergente da soma da fatura.", "error")
		// 	return;
		// }
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

		if($("#form-pdv-update")){
			$("#form-pdv-update").submit()
		}
		if($("#form-pdv")){
			$("#form-pdv").submit()
		}
	}, 20)

}


$(document).on('keyup', '.valor_integral_row', function(){
	let soma = 0
	let total = parseFloat($('#inp-valor_total').val())
	$('.fatura .valor_integral_row').each(function () {
		soma += convertMoedaToFloat($(this).val());
	});
	let resultado = total-soma;
	if(resultado < 0){
		$('.total-restante').text("R$ 0,00")
		$('.d-troco').removeClass('d-none')
		$('.troco').val(convertFloatToMoeda(resultado*-1))

	}else{
		$('.total-restante').text("R$ " + convertFloatToMoeda(total-soma))
		$('.d-troco').addClass('d-none')
		$('.troco').val('0,00')
	}

});

$(document).on('click', '#btn-finalizar', function(){
	let total = $('#inp-valor_total').val()

	if(total == 0){
		swal("Erro", "Valor da venda deve ser maior que zero!", "error")
		return;
	}
	$('.valor_integral_row').val(convertFloatToMoeda(total))
	$('.total-venda-modal').text("R$ " + convertFloatToMoeda(total))
	$('.total-restante').text("R$ " + convertFloatToMoeda(total))
	$('#modal_finalizar_pdv2').modal('show')
});

$("#form-pdv").on("submit", function (e) {

	e.preventDefault();
	const form = $(e.target);
	var json = $(this).serializeFormJSON();

	json.empresa_id = $('#empresa_id').val()
	json.usuario_id = $('#usuario_id').val()

	json.desconto = convertMoedaToFloat($('#inp-valor_desconto').val())
	json.acrescimo = convertMoedaToFloat($('#inp-valor_acrescimo').val())
	json.valor_frete = convertMoedaToFloat($('#inp-valor_frete').val())
	json.valor_total = convertFloatToMoeda(json.valor_total)

	// console.log(">>>>>>>> salvando ", json);
	// return
	// alert('teste')

	let documentoPdv = $('#documento_pdv').val()
	let cliente = $("#inp-cliente_id").val();


	if((clienteCNPJ == true && emitirNfce == true) || (documentoPdv == 'nfe' && cliente && emitirNfce == true)){

		storeNfe(json)
	}else{

		$.post(path_url + 'api/frenteCaixa/store', json)
		.done((success) => {
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
					if($('#pedido_delivery_id').length){
						location.href = '/pedidos-delivery';
					}else if($('#pedido_id').length){
						location.href = '/pedidos-cardapio/'+$('#pedido_id').val();
					}else{

						if($('.fatura tr').length > 0 && $('#inp-cliente_id').val()){
							swal({
								title: "Sucesso",
								text: "Deseja imprimir as duplicatas",
								icon: "success",
								buttons: ["Não", 'Imprimir'],
								dangerMode: true,
							})
							.then((v) => {
								if (v) {
									window.open(path_url + 'frontbox/imprimir-carne/' + success.id, "_blank")

									location.href = '/frontbox/create';
								} else {
									location.href = '/frontbox/create';
								}
							});
						}else{
							location.href = '/frontbox/create';
						}
					}
				});
			}
		}).fail((err) => {
			swal("Erro", err.responseJSON, "error")
			console.log(err)
		})
	}
});

function storeNfe(json){
    // console.log(json)
    $.post(path_url + 'api/frenteCaixa/storeNfe', json)
    .done((success) => {
        // console.log(success)
        gerarNfe(success)
        
    }).fail((err) => {
    	swal("Erro", err.responseJSON, "error")
    	console.log(err)
    })
}

function gerarNfe(venda) {
	console.clear()

	$.post(path_url + "api/nfe_painel/emitir", {
		id: venda.id
	})
	.done((success) => {
		swal("Sucesso", "NFe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
		.then(() => {
			window.open(path_url + 'nfe/imprimir/' + venda.id, "_blank")
			setTimeout(() => {
				location.reload()
			}, 100)
		})
	})
	.fail((err) => {
        // console.log(err)
        try{
        	if(err.responseJSON.error){
        		let o = err.responseJSON.error.protNFe.infProt
        		swal("Algo deu errado", o.cStat + " - " + o.xMotivo, "error")
        		.then(() => {
        			location.reload()
        		})
        	}else{
        		swal("Algo deu errado", err[0], "error")
        	}
        }catch{
        	if(err.responseJSON.message){
        		swal("Algo deu errado", err.responseJSON.message, "error")
        		.then(() => {
        			location.reload()
        		})
        	}else{
        		try{
        			if(err.responseJSON.xMotivo){
        				swal("Algo deu errado", err.responseJSON.xMotivo, "error")
        				.then(() => {
        					location.reload()
        				})
        			}else{
        				if(err.responseJSON.error){
        					swal("Algo deu errado", err.responseJSON.error, "error")
        					.then(() => {
        						location.reload()
        					})
        				}else{
        					swal("Algo deu errado", err.responseJSON, "error")
        					.then(() => {
        						location.reload()
        					})
        				}
        			}
        		}catch{
        			swal("Algo deu errado", err.responseJSON[0], "error")
        			.then(() => {
        				location.reload()
        			})
        		}
        	}
        }
        
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

var update = false
$("#form-pdv-update").on("submit", function (e) {
	update = true
	e.preventDefault();
	const form = $(e.target);
	var json = $(this).serializeFormJSON();

	json.empresa_id = $('#empresa_id').val()
	json.usuario_id = $('#usuario_id').val()

	json.desconto = convertMoedaToFloat($('#valor_desconto').val())
	json.acrescimo = convertMoedaToFloat($('#valor_acrescimo').val())
	json.valor_frete = convertMoedaToFloat($('#inp-valor_frete').val())
	
	// console.log(">>>>>>>> salvando ", json);
	$.post(path_url + 'api/frenteCaixa/update/'+$('#venda_id').val(), json)
	.done((success) => {

		if (emitirNfce == true) {
			gerarNfce(success)
		} else {

			swal({
				title: "Sucesso",
				text: "Venda atualizada com sucesso, deseja imprimir o comprovante?",
				icon: "success",
				buttons: true,
				buttons: ["Não", "Sim"],
				dangerMode: true,
			}).then((isConfirm) => {
				if (isConfirm) {
					window.open(path_url + 'frontbox/imprimir-nao-fiscal/' + success.id, "_blank")
				} else {
                    // location.reload()
                }
                if($('#pedido_delivery_id').length){
                	location.href = '/pedidos-delivery';
                }else if($('#pedido_id').length){
                	location.href = '/pedidos-cardapio';
                }else{
                	if(update){
                		location.href = path_url+'frontbox'
                	}else{
                		location.reload()
                	}
                }
            });
		}
	}).fail((err) => {
		console.log(err)
	})
});

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
				if(!update){
					location.reload()
				}else{
					location.href = path_url+'frontbox'
				}
			}, 100)
		})
	})
	.fail((err) => {
		console.log(err)
		swal("Algo deu errado", err.responseJSON, "error")
		.then(() => {
			location.reload()
		})
	})
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

$("body").on("click", "#btn-suspender", function () {

	let total = $('#inp-valor_total').val()

	if(total == 0){
		swal("Erro", "Valor da venda deve ser maior que zero!", "error")
		return;
	}
	swal({
		title: "Você esta certo?",
		text: "Deseja suspender esta venda?",
		icon: "warning",
		buttons: true,
		buttons: ["Cancelar", "Suspender"],
	}).then(confirm => {
		if (confirm) {
			// console.clear()

			var json = $("#form-pdv").serializeFormJSON();
			json.empresa_id = $('#empresa_id').val()
			json.usuario_id = $('#usuario_id').val()

			$.post(path_url + 'api/frenteCaixa/suspender', json)
			.done((success) => {
				// console.log(success)
				swal("Sucesso", "Venda suspensa!", "success")
				.then(() => {
					location.reload()
				})
			})
			.fail((err) => {
				console.log(err)
				swal("Erro", "Algo deu errado", "error")
			})
		}
	});
})

