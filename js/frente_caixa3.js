var produtos = []
var itens = []
var fatura = []
var tipoPagamento = null
var cliente = null
var emitirNfce = false

$(".main").on("keydown", function(e) {
	if (e.key === "Enter") {
		e.preventDefault();
		return false;
	}
});

$(document).on("keydown", function(e) {
	switch (e.key.toLowerCase()) {
		case "f1":
		e.preventDefault();
		$('#inp-codigo-barras').focus()
		break;

		case "f2":
		e.preventDefault();
		openModalCartao('03')
		break;

		case "f3":
		e.preventDefault();
		openModalCartao('04')
		break;

		case "f4":
		e.preventDefault();
		openModalDinheiro()
		break;

		case "f7":
		e.preventDefault();
		openModalMultiploPagamento()
		break;

		case "f9":
		e.preventDefault();
		setPagamento('17')
		break;

		case "a":
		if (e.ctrlKey) {
			e.preventDefault();
			$('.modal-acoes').modal('show');
		}
		break;

		case "f":
		if (e.shiftKey) {
			e.preventDefault();
			$('#inp-pesquisa').focus()
		}
		break;
	}
});

$(function(){

	atualizarBotaoOffline()
	produtos = JSON.parse($('#produtos-hidden').val())
	setTimeout(() => {
		$('#inp-codigo-barras').focus()
	}, 100)

	$('#inp-cliente_cpf_cnpj').val('')

	$('.select-cliente').select2({

		width: $(this).data("width")
		? $(this).data("width")
		: $(this).hasClass("w-100")
		? "100%"
		: "style",
		placeholder: $(this).data("placeholder"),
		allowClear: Boolean($(this).data("allow-clear")),
		dropdownParent: $('.modal-cliente')
	});

	if($("#definir_vendedor_pdv").val() == 1){
		openModalVendedor()
	}

	let $tbody = $(".modal-fatura .table-dynamic tbody");
	let $primeiraLinha = $tbody.find("tr:first").clone();
	$primeiraLinha.show();

	$primeiraLinha.find("input").val("");
	$primeiraLinha.find(".tipo_pagamento").select2("destroy");
	$primeiraLinha.find(".select2-container").remove();

	$primeiraLinha.find("select").val("").trigger("change");
	$tbody.html($primeiraLinha);

	$('.tipo_pagamento').select2({

		width: $(this).data("width")
		? $(this).data("width")
		: $(this).hasClass("w-100")
		? "100%"
		: "style",
		placeholder: $(this).data("placeholder"),
		allowClear: Boolean($(this).data("allow-clear")),
		dropdownParent: $('.modal-fatura')
	});

	$('#inp-lista_preco_id').val('').change()

	if($('#venda_suspensa_id').val() > 0){
		let itenSuspensa = JSON.parse($('#itens_venda_suspensa').val())
		itens = itenSuspensa
		montaHtml()
	}else{
		if($('#cliente_padrao').val() == 0){
			$('#cliente').val('').change()
		}
		$('#vendedor').val('').change()
	}

	if($('#venda_id').val() > 0){
		let itenVenda = JSON.parse($('#itens_venda').val())
		itens = itenVenda
		montaHtml()
	}else{
		if($('#cliente_padrao').val() == 0){
			$('#cliente').val('').change()
		}
		$('#vendedor').val('').change()
	}

	verificaVendasOff()
	limpaFormularioProduto()
})

if($('#venda_id').val() == 0){
	// $(window).on("beforeunload", function () {
	// 	return "Tem certeza que deseja atualizar ou sair desta página?";
	// });
}

$("#btnVendasOffline").on("click", function() {
	verificaVendasOff()
});

function atualizarBotaoOffline() {
	let vendas = JSON.parse(localStorage.getItem("vendas-off-slym")) || [];

	if (vendas.length > 0) {
		$("#btnVendasOffline").fadeIn();
		$("#btnVendasOffline .badge").text(vendas.length);
	} else {
		$("#btnVendasOffline").fadeOut();
	}
}

function verificaVendasOff(){
	// localStorage.setItem("vendas-off-slym", "[]")
	let vendasOff = JSON.parse(localStorage.getItem("vendas-off-slym")) || [];
	// console.log(vendasOff)
	if(vendasOff.length > 0){
		$('.modal-vendas-off').modal('show')

		vendasOff.map((x) => {
			let tr = "<tr class='trid-"+x._id+"'>"

			tr += "<td>"
			tr += x._id
			tr += "</td>"
			tr += "<td>"
			tr += x.cliente_nome
			tr += "</td>"

			tr += "<td>"
			tr += convertFloatToMoeda(x.valor_total)
			tr += "</td>"

			tr += "<td>"
			tr += x.itens.length
			tr += "</td>"

			tr += "<td>"
			tr += x.data_atual
			tr += "</td>"


			tr += "<td>"
			tr += "<button data-id='"+x._id+"' class='btn btn-sm btn-danger btn-remove-venda' type='button'><i class='ri-delete-bin-line'></i></button>"
			tr += "</td>"

			tr += "</tr>"
			$('.vendas-off tbody').append(tr)
		})
	}
}
$(document).on("click", ".btn-remove-venda", function () {

	swal({
		title: "Você está certo?",
		text: "Uma vez deletado, você não poderá recuperar esse item novamente!",
		icon: "warning",
		buttons: true,
		buttons: ["Cancelar", "Excluir"],
		dangerMode: true,
	}).then((isConfirm) => {
		if (isConfirm) {
			let vendasOff = JSON.parse(localStorage.getItem("vendas-off-slym")) || [];
			$(this).closest('tr').remove()
			let temp = vendasOff.filter((x) => {
				return x._id != $(this).attr('data-id')
			})

			vendasOff = temp
			localStorage.setItem("vendas-off-slym", JSON.stringify(vendasOff))
		} else {
			swal("", "Este item está salvo!", "info");
		}
	});
})

$(document).on("click", ".btn-salvar-vendas", function () {
	let vendasOff = JSON.parse(localStorage.getItem("vendas-off-slym")) || [];
	falhas = []
	let promessas = vendasOff.map((x) => {
		let url = path_url + 'api/frenteCaixa/storepdv3';

		return $.post(url, x)
		.done(() => {
			toastr.success("Venda " + x._id + " salva no servidor!");
		})
		.fail(() => {
			toastr.error("Falha ao salvar venda " + x._id);
			falhas.push(x)
		});
	});

	Promise.all(promessas).then((resultados) => {
		// console.log(falhas)
		if(falhas.length > 0){
			localStorage.setItem("vendas-off-slym", JSON.stringify(falhas));
		}else{
			localStorage.setItem("vendas-off-slym", "[]")
		}
		$('.modal-vendas-off').modal('hide');
	});
})

$('.btn-seleciona-cliente').click(() => {
	let cliente = $('#cliente option:selected').text()
	$('.txt-cliente').text(cliente)
})

$('.btn-seleciona-vendedor').click(() => {
	let vendedor = $('#vendedor option:selected').text()
	$('.txt-vendedor').text(vendedor)
})

function atualizarRelogio() {
	const agora = new Date();

	const dia = String(agora.getDate()).padStart(2, '0');
	const mes = String(agora.getMonth() + 1).padStart(2, '0');
	const ano = agora.getFullYear();

	const horas = String(agora.getHours()).padStart(2, '0');
	const minutos = String(agora.getMinutes()).padStart(2, '0');
	const segundos = String(agora.getSeconds()).padStart(2, '0');

	const dataHora = `${dia}/${mes}/${ano} ${horas}:${minutos}:${segundos}`;
	$('.timer').text(dataHora);
}

$(document).ready(function() {
	setInterval(atualizarRelogio, 1000);
	setInterval(testeConexao, 5000);
});

function testeConexao(){

	$.ajax({
		url: path_url + 'api/frenteCaixa/teste-conexao',
		method: "GET",
		global: false
	}).done((success) => {
		if(!$('.d-offline').hasClass('d-none')){
			toastr.success("Servidor conectado!");
		}
		$('.d-offline').addClass('d-none')
	}).fail((err) => {
		if($('.d-offline').hasClass('d-none')){
			toastr.error("Servidor off-line!");
		}
		$('.btn-fiscal').attr('disabled', 'disabled')
		$('.d-offline').removeClass('d-none');
	});
}

$(document).on("change", "#produto_id", function () {
	let valor = $(this).find('option:selected').data('valor')
	$('#inp-valor_unitario').val(convertFloatToMoeda(valor))
	$('#inp-sub_total').val(convertFloatToMoeda(valor))
	$('#inp-quantidade').val('1')

	if(valor){
		setTimeout(() => {
			$('#inp-valor_unitario').focus()
		}, 500)
	}
});

$("#inp-valor_unitario").on("keydown", function(e) {
	if (e.key === "Enter") {
		$('#inp-quantidade').focus()
	}
});

$("#inp-quantidade").on("keydown", function(e) {
	if (e.key === "Enter") {
		$('#inp-sub_total').focus()
	}
});

$("#inp-sub_total").on("keydown", function(e) {
	if (e.key === "Enter") {
		$('#btn-adicionar').trigger('click')
	}
});

$(document).on("click", ".btn-suprimento", function () {
	$('.modal-acoes').modal('hide')
	$('.modal-suprimento').modal('show')
});

$(document).on("click", ".btn-sangria", function () {
	$('.modal-acoes').modal('hide')
	$('.modal-sangria').modal('show')
});

$(document).on("click", ".btn-salvar-suprimento", function () {
	let data = {
		usuario_id: $('#usuario_id').val(),
		valor: $('.modal-suprimento #inp-valor').val(),
		observacao: $('.modal-suprimento #inp-observacao').val(),
		tipo_pagamento: $('.modal-suprimento #inp-tipo_pagamento').val()
	}
	$.post(path_url + 'api/frenteCaixa/store-suprimento', data)
	.done((success) => {
		// console.log(success)
		toastr.success("Suprimento realizado!")
	})
	.fail((err) => {
		// console.log(err)
		toastr.error("Erro ao realizar suprimento!")
	})
});

$(document).on("click", ".btn-salvar-sangria", function () {
	let data = {
		usuario_id: $('#usuario_id').val(),
		valor: $('.modal-sangria #inp-valor').val(),
		observacao: $('.modal-sangria #inp-observacao').val(),
	}
	$.post(path_url + 'api/frenteCaixa/store-sangria', data)
	.done((success) => {
		// console.log(success)
		toastr.success("Sangria realizada!")
	})
	.fail((err) => {
		// console.log(err)
		toastr.error("Erro ao realizar sangria!")
	})
});

$(document).on("click", "#btn-adicionar", function () {
	let produto_id = $('#produto_id').val()
	let valor_unitario = convertMoedaToFloat($('#inp-valor_unitario').val())
	let sub_total = convertMoedaToFloat($('#inp-sub_total').val())
	let quantidade = $('#inp-quantidade').val()

	let p = produtos.find((x) => {
		return x.id == produto_id
	})

	if(p.quantidade_atacado > 0 && quantidade >= p.quantidade_atacado){
		valor_unitario = p.valor_atacado
	}

	let nome = p.nome
	if(p.promocao){
		nome += p.promocao
	}

	let item = {
		_id: Math.floor(Math.random() * (10000000000 - 1 + 1)) + 1,
		produto_id: produto_id,
		produto_nome: nome,
		valor_unitario: valor_unitario,
		sub_total: sub_total,
		quantidade: quantidade,
		disabled_qtd: p.quantidade_atacado >= quantidade
	}

	itens.push(item)
	montaHtml()
	// toastr.success('Produto adicionado!');
	$('#inp-codigo-barras').focus()
	
});

function adicionarItemCard(id){
	let p = produtos.find((x) => {
		return x.id == id
	})

	let nome = p.nome
	if(p.promocao){
		nome += p.promocao
	}

	let item = {
		_id: Math.floor(Math.random() * (10000000000 - 1 + 1)) + 1,
		produto_id: id,
		produto_nome: nome,
		valor_unitario: p.valor_unitario,
		sub_total: p.valor_unitario,
		quantidade: 1,
		disabled_qtd: 0
	}

	let qtdExistente = itens
	.filter(x => x.produto_id === id)
	.reduce((acc, x) => acc + parseFloat(x.quantidade || 1), 0);

	let qtdTotal = qtdExistente + 1;

	// console.log("qtdTotal", qtdTotal)

	itens.push(item)

	if (p.quantidade_atacado > 0 && qtdTotal >= p.quantidade_atacado) {
		let novoValor = p.valor_atacado || p.valor_unitario;

		itens = itens.map(x => {
			if (x.produto_id === id) {
				let novaQtd = parseFloat(x.quantidade || 1);
				return {
					...x,
					valor_unitario: novoValor,
					sub_total: parseFloat((novoValor * novaQtd).toFixed(2))
				};
			}
			return x;
		});

		toastr.info(`${p.nome} atingiu o valor de atacado (${p.quantidade_atacado})`);
	}
	// console.log(itens)
	naoAlteraQtd = 1
	montaHtml();
	$("#inp-codigo-barras").val('');
	setTimeout(() => {
		naoAlteraQtd = 0
	}, 3000)

}

$('.btn-add-tr').click(() => {
	let hoje = new Date().toISOString().split('T')[0];
	$('.data_vencimento').last().val(hoje)
})

$(document).on("input", "#inp-valor_recebido", function () {
	let total = convertMoedaToFloat($('.total-venda').text())
	let valor_recebido = convertMoedaToFloat($(this).val())
	$('.valor-troco').text("R$ " + convertFloatToMoeda(valor_recebido-total))

})

$(document).on("input", ".valor_integral_row", function () {
	let soma = 0
	$('.salvar-fatura').attr('disabled', 'disabled')

	$('.valor_integral_row').each(function () {
		soma += convertMoedaToFloat($(this).val())
	})

	let total = convertMoedaToFloat($('.total-venda').text())
	$('.soma-fatura').text("R$ " + convertFloatToMoeda(soma))
	$('.total-faltante').text("R$ " + convertFloatToMoeda(total - soma))

	if(total - soma == 0){
		$('.salvar-fatura').removeAttr('disabled')
	}
})

$('.salvar-fatura').click(() => {
	fatura = []
	$('.valor_integral_row').each(function () {
		let valor = convertMoedaToFloat($(this).val())
		let data = $(this).closest('td').prev().find('input').val()
		let tipo_pagamento = $(this).closest('td').prev().prev().find('select').val()
		fatura.push({
			valor: valor,
			data: data,
			tipo_pagamento: tipo_pagamento
		})
	})

	setTimeout(() => {
		$('.modal-finalizar').modal('show')
	}, 10)
})

var timerLeitura = null;
var bufferCodigo = "";

$(document).on("keydown", "#inp-codigo-barras", function (e) {
	let tecla = e.key;

	// ignora teclas de controle
	if (tecla.length !== 1) return;

	// adiciona caractere ao buffer
	bufferCodigo += tecla;

	// limpa timer anterior
	clearTimeout(timerLeitura);

	timerLeitura = setTimeout(() => {
		if (bufferCodigo.length >= 8) {
			processarCodigo(bufferCodigo);
		}
		bufferCodigo = "";
	}, 80);
});

function processarCodigo(codigo) {
	let quantidade = 1;
	let sub_total = 0;
	let p = produtos.find((x) => x.codigo_barras == codigo);

	if (!p) {
		let balanca_digito_verificador = $('#balanca_digito_verificador').val();
		let referencia = parseInt(codigo.substring(1, balanca_digito_verificador));
		p = produtos.find((x) => x.referencia_balanca == referencia);

		if (p) {
			let valorStr = codigo.substring(7, 12);
			let valor = parseFloat(valorStr) / 100;
			sub_total = valor;
			quantidade = (valor / p.valor_unitario).toFixed(3);
		}
	}

	if (!p) {
		toastr.error('Produto não encontrado!');
		beepErro();
		return;
	}

	let qtdExistente = itens
	.filter(x => x.produto_id === p.id)
	.reduce((acc, x) => acc + parseFloat(x.quantidade || 1), 0);

	let qtdTotal = qtdExistente + parseFloat(quantidade || 1);

	let item = {
		_id: Math.floor(Math.random() * (10000000000 - 1 + 1)) + 1,
		produto_id: p.id,
		produto_nome: p.nome,
		valor_unitario: p.valor_unitario,
		sub_total: sub_total || p.valor_unitario,
		quantidade: quantidade,
	};

	itens.push(item);

	if (p.quantidade_atacado > 0 && qtdTotal >= p.quantidade_atacado) {
		let novoValor = p.valor_atacado || p.valor_unitario;

		itens = itens.map(x => {
			if (x.produto_id === p.id) {
				let novaQtd = parseFloat(x.quantidade || 1);
				return {
					...x,
					valor_unitario: novoValor,
					sub_total: parseFloat((novoValor * novaQtd).toFixed(2))
				};
			}
			return x;
		});

		toastr.info(`${p.nome} atingiu o valor de atacado (${p.quantidade_atacado})`);
	}
	naoAlteraQtd = 1
	montaHtml();
	$("#inp-codigo-barras").val('');
	setTimeout(() => {
		naoAlteraQtd = 0
	}, 3000)
}


function beepErro(){
	var audio = new Audio('/audio/beep_error.mp3');
	audio.addEventListener('canplaythrough', function() {
		audio.play();
	});
}

function limpaFormularioProduto(){
	$('#produto_id').val('').change()
	$('#inp-valor_unitario').val('')
	$('#inp-sub_total').val('')
	$('#inp-quantidade').val('1')
}

function montaHtml(){
	let tbody = ""

	$('.itens tbody').html('')
	itens.map((item) => {

		if(item.quantidade+"".includes('.')){
			item.quantidade = (item.quantidade + "").replace(/\./g, ",");
		}

		let tr = "<tr>"
		tr += "<td>"
		tr += item.produto_nome
		tr += "</td>"

		tr += "<td>"
		tr += convertFloatToMoeda(item.valor_unitario)
		tr += "</td>"

		tr += "<td>"
		tr += "<input style='width: 100px' "+ (item.disabled_qtd ? 'readonly' : '') +" class='form-control qtd-custom' value='"+item.quantidade+"'>"
		tr += "<input type='hidden' class='form-control produto_id-custom' value='"+item.produto_id+"'>"
		tr += "</td>"

		tr += "<td class='sub_total_linha'>"
		tr += convertFloatToMoeda(item.sub_total)
		tr += "</td>"

		tr += "<td>"
		tr += "<button onclick='editItem("+item._id+")' class='btn btn-sm btn-warning' style='margin-right: 3px;' type='button'><i class='ri-edit-line'></i></button>"
		tr += "<button onclick='removeItem("+item._id+")' class='btn btn-sm btn-danger' type='button'><i class='ri-delete-bin-line'></i></button>"
		tr += "</td>"

		tr += "</tr>"

		$('.itens tbody').append(tr)
	})
	calculaTotal()
	limpaFormularioProduto()
	let ultimo = $(".qtd-custom:last");
	ultimo.focus();

	setTimeout(() => {
		$('#inp-codigo-barras').focus()
	}, 100)

}

$(document).on('input', '.mask-num-up', function () {
	this.value = this.value
	.replace(/[^0-9,%]/g, '')
	.replace(/,+/g, ',')
	.replace(/%+/g, '%')
});

var naoAlteraQtd = 0
$(document).on("blur", ".qtd-custom", function () {

	if(naoAlteraQtd == 1){
		return;
	}
	let qtd = convertMoedaToFloat($(this).val())
	if(!qtd){
		qtd = 1
		$(this).val('1')
	}
	let id = $(this).closest('td').find('.produto_id-custom').val()

	let p = produtos.find((x) => {
		return x.id == id
	})

	let vlUnit = convertMoedaToFloat($(this).closest('td').prev().text())

	for(let i=0; i<itens.length; i++){

		if(p && p.quantidade_atacado > 0 && qtd >= p.quantidade_atacado){
			itens[i].disabled_qtd = 1
			itens[i].valor_unitario = p.valor_atacado
			$(this).closest('td').prev().text(convertFloatToMoeda(p.valor_atacado))
			vlUnit = p.valor_atacado
		}else{
			// itens[i].disabled_qtd = 0
			// $(this).closest('td').prev().text(convertFloatToMoeda(p.valor_unitario))
			// vlUnit = p.valor_unitario
		}
		if(id == itens[i].produto_id){
			itens[i].quantidade = qtd
			itens[i].sub_total = qtd * itens[i].valor_unitario
		}
	}
	$(this).closest('td').next().text(convertFloatToMoeda(qtd*vlUnit))
	calculaTotal()
});

$(document).on("input", ".qtd-custom", function () {
	let valor = $(this).val().replace(/[^0-9,]/g, '');

	const partes = valor.split(',');
	if (partes.length > 2) {
		valor = partes[0] + ',' + partes[1];
	}

	$(this).val(valor);
});

function openModalCliente(){
	$('.modal-cliente').modal('show')
}

function openModalListaPreco(){
	$('.modal-lista-preco').modal('show')
}

$(document).on("click", ".btn-selecionar-lista", function () {
	let lista_id = $('#inp-lista_preco_id').val()

	if(lista_id){
		produtos.map((x) => {
			x.valor_unitario = x['valor_lista_'+lista_id]
			$('.card-prod-'+x.id).find('.valor-produto').text("R$ " + convertFloatToMoeda(x.valor_unitario))

			$('#produto_id option[value="'+x.id+'"]').text("#"+ x.numero_sequencial + " " + x.nome + "[R$" + convertFloatToMoeda(x['valor_lista_'+lista_id]) + "]");
			$('#produto_id option[value="'+x.id+'"]').attr('data-valor', x['valor_lista_'+lista_id]);
		})
	}else{
		produtos.map((x) => {
			x.valor_unitario = x['valor_original']
			$('.card-prod-'+x.id).find('.valor-produto').text("R$ " + convertFloatToMoeda(x.valor_unitario))
		})
	}
})

$(document).on("click", ".acoes-pdv", function () {
	$('.modal-acoes').modal('show')
})

function openModalMultiploPagamento(){
	let total = convertMoedaToFloat($('.total-venda').text())
	if(total <= 0){
		toastr.warning('O valor da venda precisa ser maior que zero!');
		return
	}

	$('.total-fatura').text("R$ " + $('.total-venda').text())
	$('.total-faltante').text("R$ " + $('.total-venda').text())

	let hoje = new Date().toISOString().split('T')[0];
	$('.data_vencimento').val(hoje)
	$('.modal-fatura').modal('show')
}

function setPagamento(tipo){
	let total = convertMoedaToFloat($('.total-venda').text())
	if(total <= 0){
		toastr.warning('O valor da venda precisa ser maior que zero!');
		return
	}
	tipoPagamento = tipo

	$('.modal-finalizar').modal('show')

}

function openModalVendedor(){
	$('.modal-vendedor').modal('show')
}

function openModalDesconto(){
	$('.modal-desconto').modal('show')
	setTimeout(() => {
		$('#inp-valor_desconto').focus()
	}, 500)
}

function openModalDinheiro(){
	tipoPagamento = '01'
	let total = convertMoedaToFloat($('.total-venda').text())
	if(total <= 0){
		toastr.warning('O valor da venda precisa ser maior que zero!');
		return
	}

	$('.total-fatura').text("R$ " + $('.total-venda').text())
	$('.modal-dinheiro').modal('show')
}

$('.btn-modal-finalizar').click(() => {
	$('.modal-finalizar').modal('show')
})

$("#inp-valor_desconto").on("keydown", function(e) {
	if (e.key === "Enter") {
		$('.salvar-desconto').trigger('click')
	}
});

$('.salvar-desconto').click(() => {
	let valorDigitado = $('#inp-valor_desconto').val()
	let valorDesconto = convertMoedaToFloat($('#inp-valor_desconto').val())
	let totalProdutos = convertMoedaToFloat($('.soma-produtos').text())
	if(totalProdutos <= 0){
		toastr.error('Valor da venda deve ser maior que Zero!');
		return
	}
	if (valorDigitado.includes('%')) {
		valorDesconto = totalProdutos*(valorDesconto/100)
	}

	$('.desconto').text("R$ " + convertFloatToMoeda(valorDesconto))
	calculaTotal()
})

function openModalAcrescimo(){
	$('.modal-acrescimo').modal('show')
	setTimeout(() => {
		$('#inp-valor_acrescimo').focus()
	}, 500)
}

function openModalValorFrete(){
	$('.modal-frete').modal('show')
	setTimeout(() => {
		$('#inp-valor_frete').focus()
	}, 500)
}

function openModalObservacao(){
	$('.modal-observacao').modal('show')
	setTimeout(() => {
		$('.modal-observacao #inp-observacao').focus()
	}, 500)
}

$("#inp-valor_acrescimo").on("keydown", function(e) {
	if (e.key === "Enter") {
		$('.salvar-acrescimo').trigger('click')
	}
});

$('.salvar-frete').click(() => {
	let valor = convertMoedaToFloat($('#inp-valor_frete').val())
	$('.valor_frete').text("R$ " + convertFloatToMoeda(valor))
	calculaTotal()
})

// $('.salvar-acrescimo').click(() => {
// 	let valor = convertMoedaToFloat($('#inp-valor_acrescimo').val())
// 	$('.acrescimo').text("R$ " + convertFloatToMoeda(valor))
// 	calculaTotal()
// })

$('.salvar-acrescimo').click(() => {
	let valorDigitado = $('#inp-valor_acrescimo').val()
	let valorAcrescimo = convertMoedaToFloat($('#inp-valor_acrescimo').val())
	let totalProdutos = convertMoedaToFloat($('.soma-produtos').text())
	if(totalProdutos <= 0){
		toastr.error('Valor da venda deve ser maior que Zero!');
		return
	}
	if (valorDigitado.includes('%')) {
		valorAcrescimo = totalProdutos*(valorAcrescimo/100)
	}

	$('.acrescimo').text("R$ " + convertFloatToMoeda(valorAcrescimo))
	calculaTotal()
})

$('.salvar-observacao').click(() => {
	$('.totalizador-observacao').removeClass('text-primary')
	let observacao = $('.modal-observacao #inp-observacao').val()
	if(observacao.length > 1){
		$('.totalizador-observacao').addClass('text-primary')
	}
})

function openModalCartao(tipo){

	let total = convertMoedaToFloat($('.total-venda').text())
	if(total <= 0){
		toastr.warning('O valor da venda precisa ser maior que zero!');
		return
	}
	tipoPagamento = tipo
	$('.modal-cartao').modal('show')
}

function calculaTotal(){
	let total = 0
	let qtdItens = 0

	$('.sub_total_linha').each(function () {
		total += convertMoedaToFloat($(this).text())
		// qtdItens += convertMoedaToFloat($(this).prev().text())
	})

	setTimeout(() => {
		$('.soma-produtos').text("R$ " + convertFloatToMoeda(total))
		$('.total-itens').text($('.itens tbody tr').length)
	}, 10)

	let desconto = convertMoedaToFloat($('.desconto').text())
	let acrescimo = convertMoedaToFloat($('.acrescimo').text())
	let valor_frete = convertMoedaToFloat($('.valor_frete').text())

	$('.total-venda').text("R$ " + convertFloatToMoeda(total+acrescimo-desconto+valor_frete))
}

function removeItem(id){
	let temp = itens.filter((x) => {
		return x._id != id
	})
	itens = temp
	montaHtml()
}

var __editId = null
function editItem(id){
	let temp = itens.find((x) => {
		return x._id == id
	})
	if(temp){

		__editId = id
		//abrir modal editar
		console.log(temp)
		$('.modal-edit-item').modal('show')
		$('.modal-edit-item .modal-title').text(temp.produto_nome)
		$('.modal-edit-item #inp-valor_unitario_edit').val(convertFloatToMoeda(temp.valor_unitario))
		$('.modal-edit-item #inp-quantidade_edit').val((temp.quantidade))
	}
}

$("body").on("click", ".editar-item", function () {
	if(__editId){
		let valor = convertMoedaToFloat($('.modal-edit-item #inp-valor_unitario_edit').val())
		let quantidade = convertMoedaToFloat($('.modal-edit-item #inp-quantidade_edit').val())

		for(let i=0; i<itens.length; i++){
			if(itens[i]._id == __editId){
				itens[i].quantidade = quantidade
				itens[i].valor_unitario = valor
				itens[i].sub_total = valor * quantidade
			}
		}

		naoAlteraQtd = 1

		setTimeout(() => {
			montaHtml()
		}, 100)
		$("#inp-codigo-barras").val('');
		setTimeout(() => {
			naoAlteraQtd = 0
		}, 3000)
	}
})

$("body").on("blur", "#inp-valor_unitario", function () {
	let qtd = $("#inp-quantidade").val();
	let value_unit = $(this).val();

	let valor_minimo_venda = parseFloat($('#produto_id').find('option:selected').data('valor_minimo_venda'))
	value_unit = convertMoedaToFloat(value_unit);

	if(value_unit < valor_minimo_venda){
		$(this).val(convertFloatToMoeda(valor_minimo_venda))
		toastr.info(`Valor minímo para este produto é R$ ${convertFloatToMoeda(valor_minimo_venda)}`);
		value_unit = valor_minimo_venda
	}

	qtd = convertMoedaToFloat(qtd);
	$("#inp-sub_total").val(convertFloatToMoeda(qtd * value_unit));
})

$("body").on("blur", "#inp-quantidade", function () {
	let qtd = $(this).val();
	let value_unit = $("#inp-valor_unitario").val()
	value_unit = convertMoedaToFloat(value_unit);
	qtd = convertMoedaToFloat(qtd);
	$("#inp-sub_total").val(convertFloatToMoeda(qtd * value_unit));
})

// $('#inp-pesquisa').on('keyup', function () {
// 	const termo = $(this).val().toLowerCase();
// 	$('.btn-categoria').removeClass('btn-primary')
// 	$('.categoria-0').addClass('btn-primary')
// 	$('.prod').each(function () {
// 		const texto = $(this).text().toLowerCase();
// 		if (texto.indexOf(termo) > -1) {
// 			$(this).show();
// 		} else {
// 			$(this).hide();
// 		}
// 	});
// });

let timer;
$('#inp-pesquisa').on('keyup', function() {
	clearTimeout(timer);
	const termo = $(this).val().toLowerCase().trim();

	timer = setTimeout(() => {
		$('.btn-categoria').removeClass('btn-primary');
		$('.categoria-0').addClass('btn-primary');

		if (termo === "") {
            // Se o campo estiver vazio, mostra tudo de forma rápida
            $('.prod').show();
            return;
        }
        
        $('.prod').each(function () {
        	const texto = $(this).text().toLowerCase();
        	$(this).toggle(texto.includes(termo));
        });
    }, 300); // espera 300ms depois da digitação
});

function selecionaCategoria(categoria_id){
	$('.btn-categoria').removeClass('btn-primary')
	$('.categoria-'+categoria_id).addClass('btn-primary')

	$('.prod').each(function () {
		if(categoria_id == 0){
			$(this).show()
		}else{
			const categoria = $(this).find('.categoria').val();
			if (categoria_id == categoria) {
				$(this).show();
			} else {
				$(this).hide();
			}
		}
	});
}

$('#btn_nao_fiscal').click(() => {
	emitirNfce = false
	salvarVenda()
})

$(document).on('click', '.btn-fiscal', function(){
	$('.modal-finalizar').modal('hide')
})

$(document).on('click', '#btn_fiscal', function(){
	emitirNfce = true
	salvarVenda()
})

function vendaSuspensa(){
	$.get(path_url + "api/frenteCaixa/venda-suspensas",
	{
		empresa_id: $('#empresa_id').val(),
	})
	.done((data) => {
        // console.log(data)
        $('#vendas_suspensas').modal('show')
        $('.table-vendas-suspensas tbody').html(data)
    })
	.fail((e) => {
		console.log(e);
		toastr.error("Não foi possivel buscar as vendas suspensas!")
	});
}

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

$("body").on("click", "#btn-suspender", function () {
	swal({
		title: "Você esta certo?",
		text: "Deseja suspender esta venda?",
		icon: "warning",
		buttons: true,
		buttons: ["Cancelar", "Suspender"],
	}).then(confirm => {
		if (confirm) {
			console.clear()
			let suspender = 1
			salvarVenda(suspender)
		}
	});
})

function salvarVenda(suspender = 0){

	if($("#definir_vendedor_pdv").val() == 1 && !$('#vendedor').val()){
		openModalVendedor()
		toastr.warning("Selecione o vendedor para finalizar!")
		return
	}

	let json = {
		_id: Math.floor(Math.random() * (10000000000 - 1 + 1)) + 1,
		itens: itens,
		fatura: fatura,
		tipo_pagamento: tipoPagamento,
		cliente_id: $('#cliente').val(),
		cliente_nome: $('#cliente').val() ? $('#cliente  option:selected').text() : 'Consumidor final',
		empresa_id: $('#empresa_id').val(),
		usuario_id: $('#usuario_id').val(),
		funcionario_id: $('#vendedor').val(),
		cliente_cpf_cnpj: $('#inp-cliente_cpf_cnpj').val(),
		cliente_nome: $('#inp-cliente_nome').val(),
		desconto: convertMoedaToFloat($('.desconto').text()),
		acrescimo: convertMoedaToFloat($('.acrescimo').text()),
		valor_frete: convertMoedaToFloat($('.valor_frete').text()),
		observacao: $('.modal-observacao #inp-observacao').val(),
		troco: convertMoedaToFloat($('.valor-troco').text()),
		valor_recebido: convertMoedaToFloat($('#inp-valor_recebido').val()),
		lista_id: null,
		valor_total: convertMoedaToFloat($('.total-venda').text()),
		valor_produtos: convertMoedaToFloat($('.soma-produtos').text()),
		venda_suspensa_id: $('#venda_suspensa_id').val(),
		data_atual: new Date().toLocaleString("pt-BR")
	}
	console.clear()

	// console.log("salvando", json)
	// return;

	let url = path_url + 'api/frenteCaixa/storepdv3'
	if(suspender == 1){
		url = path_url + 'api/frenteCaixa/suspender3'
	}

	if($('#venda_id').val() > 0){
		json.venda_id = $('#venda_id').val()
		url = path_url + 'api/frenteCaixa/updatepdv3'
	}

	$.post(url, json)
	.done((success) => {
		// console.log(success)

		if (emitirNfce == true) {
			gerarNfce(success)
		} else {
			if(suspender == 0){
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
						limpaFormulario()
					} else {
						limpaFormulario()
					}

					if($('#venda_id').val() > 0){
						location.href = '/frontbox/create';
					}

				});
			}
			else if(suspender == 1){
				toastr.success("Venda suspensa!")
				limpaFormulario()
			}
		}
	}).fail((err) => {
		if(!$('.d-offline').hasClass('d-none')){
			try {
				let vendasOff = JSON.parse(localStorage.getItem("vendas-off-slym")) || [];

				vendasOff = vendasOff.filter(v => v._id !== json._id);
				vendasOff.push(json)
				localStorage.setItem("vendas-off-slym", JSON.stringify(vendasOff))
				toastr.warning("Não foi possível salvar esta venda no servidor, armazenado local para enviar depois!")

				limpaFormulario()
			} catch (e) {
				console.error("Erro ao salvar venda offline", e);
				toastr.err("Falha ao salvar venda offline. Verifique o armazenamento!")
			}

		}else{
			swal("Erro", "Algo deu errado ao salvar venda!", "error")
		}
		console.log(err)
	})

	setTimeout(() => {
		atualizarBotaoOffline()
	}, 2000)
}

function gerarNfce(venda) {
	$.post(path_url + "api/nfce_painel/emitir", {
		id: venda.id,
	})
	.done((success) => {
		swal("Sucesso", "NFCe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
		.then(() => {
			window.open(path_url + 'nfce/imprimir/' + venda.id, "_blank")
			setTimeout(() => {
				limpaFormulario()
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

function limpaFormulario(){
	console.clear()
	$('#inp-valor_desconto').val("")
	$('#inp-valor_acrescimo').val("")
	$('.desconto').text("R$ 0,00")
	$('.acrescimo').text("R$ 0,00")
	$('.valor_frete').text("R$ 0,00")
	$('.valor-troco').text("R$ 0,00")
	$('#inp-valor_recebido').val('')
	$('#inp-pesquisa').val('')
	itens = []
	fatura = []
	emitirNfce = false
	cliente = null
	$('.itens tbody').html('')

	if($('#cliente_padrao').val() == 0){
		$('#cliente').val('').change()
		$('.txt-cliente').text('Cliente')
	}
	$('#inp-cliente_cpf_cnpj').val('')
	$('#vendedor').val('').change()
	$('.modal-observacao #inp-observacao').val('')
	$('.totalizador-observacao').removeClass('text-primary')

	if($('#lista_preco_id').val()){
		produtos.map((x) => {
			x.valor_unitario = x['valor_original']
			$('.card-prod-'+x.id).find('.valor-produto').text("R$ " + convertFloatToMoeda(x.valor_unitario))
		})
	}
	$('#lista_preco_id').val('').change()

	$('.txt-vendedor').text('Vendedor')

	let $tbody = $(".modal-fatura .table-dynamic tbody");
	let $primeiraLinha = $tbody.find("tr:first").clone();
	$primeiraLinha.show();

	$primeiraLinha.find("input").val("");
	$primeiraLinha.find(".tipo_pagamento").select2("destroy");
	$primeiraLinha.find(".select2-container").remove();

	$primeiraLinha.find("select").val("").trigger("change");
	$tbody.html($primeiraLinha);

	$('.tipo_pagamento').select2({

		width: $(this).data("width")
		? $(this).data("width")
		: $(this).hasClass("w-100")
		? "100%"
		: "style",
		placeholder: $(this).data("placeholder"),
		allowClear: Boolean($(this).data("allow-clear")),
		dropdownParent: $('.modal-fatura')
	});

	calculaTotal()
	$('.modal-finalizar').modal('hide')

	if($("#definir_vendedor_pdv").val() == 1){
		openModalVendedor()
	}
}

function reiniciarVenda(){
	swal({
		title: "Alerta",
		text: "Deseja reiniciar a venda?",
		icon: "warning",
		buttons: true,
		buttons: ["Não", "Sim"],
		dangerMode: true,
	}).then((isConfirm) => {
		if (isConfirm) {
			limpaFormulario()
		} 

	});
}

