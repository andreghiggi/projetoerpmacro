var adicionais = []
var sabores_selecionados = []
var adicionais_id = []
var valor_adicional = 0
$(document).on("click", ".opcao", function () {
	$('.opcao').removeClass('active')
	$('.opcoes-input').html('')
	var valor = $(this).attr('adicional-valor');
	var id = $(this).attr('adicional-id');

	if(adicionais_id.includes(id)){
		adicionais_id = adicionais.filter((x) => {
			return x != id
		})
		adicionais = adicionais.filter((x) => {
			return x.id != id
		})
	}else{
		adicionais.push({id: id, valor: valor})
		adicionais_id.push(id)
	}

	setTimeout(() => {
		percorreAdicionais(adicionais)
	}, 10)
});

$(".btn-delete").on("click", function (e) {
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

function percorreAdicionais(adicionais){
	valor_adicional = 0
	adicionais.map((x) => {
		$('.op_'+x.id).addClass('active')
		$('.opcoes-input').append('<input type="hidden" value="'+x.id+'" name="adicional[]"/>')
		valor_adicional += parseFloat(x.valor)
	})
	setTimeout(() => {
		atualizaSubTotal()
	}, 10)
}

$(document).on("click", ".campo-numero .decrementar", function () {
	var valor = parseInt( $(this).siblings('input').val() );
	var newvalor = valor-1;
	if( newvalor >= 1 ) {
		$(this).siblings('input').val(newvalor);
	} else {
		$(this).siblings('input').val(1);
	}
	atualizaSubTotal()
	$(this).siblings('input').trigger("change");
});

$(document).on("click", ".campo-numero .incrementar", function () {

	var valor = parseInt( $(this).siblings('input').val() );
	var newvalor = valor+1;
	$(this).siblings('input').val(newvalor);
	$(this).siblings('input').trigger("change");
	atualizaSubTotal()

});

function atualizaSubTotal(){

	let qtd = $('#quantidade').val()
	let valor_unitario = $('#valor_unitario_produto').val()
	let total = qtd * (parseFloat(valor_unitario) + parseFloat(valor_adicional))
	$('.subtotal').html('<strong>Total:</strong> R$ ' + convertFloatToMoeda(total));
	$('#inp-sub_total').val(total);
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

$(document).on("keyup", "#inp-pesquisa_sabor", function () {
	let pesquisa = $(this).val()
	let tamanho_id = $('#tamanho_id').val()
	let produto_id = $('#produto_id').val()
	if(!tamanho_id){
		swal("Atenção", "Selecione o tamanho primeiro", "warning")
		return;
	}

	if(pesquisa.length > 1){
		$.get(path_url + 'api/delivery-link/pesquisa-pizza', {
			pesquisa: pesquisa,
			tamanho_id: tamanho_id,
			produto_id: produto_id,
			empresa_id: $('#inp-empresa_id').val(),

		}).done((res) => {
			console.clear()
			$('.div-sabores').html(res)
		}).fail((err) => {
			console.log(err)
		})
	}
});

$(document).on("change", "#tamanho_id", function () {
	let pesquisa = $("#inp-pesquisa_sabor").val()
	let tamanho_id = $('#tamanho_id').val()
	let produto_id = $('#produto_id').val()

	if(pesquisa.length > 1){
		$.get(path_url + 'api/delivery-link/pesquisa-pizza', {
			pesquisa: pesquisa,
			tamanho_id: tamanho_id,
			produto_id: produto_id,
			empresa_id: $('#inp-empresa_id').val(),

		}).done((res) => {
			console.clear()
			$('.div-sabores').html(res)
			montaSabores()
		}).fail((err) => {
			console.log(err)
		})
	}else{
		$.get(path_url + 'api/delivery-link/monta-pizza', {
			sabores_selecionados: sabores_selecionados,
			produto_id: produto_id,
			tamanho_id: tamanho_id
		}).done((res) => {
			console.clear()
			$('.total-pizza').text('R$ ' + convertFloatToMoeda(res.valor_pizza))
		}).fail((err) => {
			console.log(err)
		})

		setTimeout(() => {
			validaValorPizza()
		}, 500)
	}

});

function seleciona_tamanho(produto_id){
	if(!sabores_selecionados.includes(produto_id)){
		sabores_selecionados.push(produto_id)
	}
	setTimeout(() => {
		console.log(sabores_selecionados)
		montaSabores()
	}, 10)
}

function remove_sabor(produto_id){
	let pizza_princial = $('#produto_id').val()
	if(pizza_princial == produto_id){
		toastr.warning("Não é possível remover o sabor principal!")
		return;
	}
	sabores_selecionados = sabores_selecionados.filter((x) => {
		return x != produto_id
	})
	setTimeout(() => {
		console.log(sabores_selecionados)
		montaSabores()
	}, 10)
}

function validaValorPizza(){
	let total_pizza = convertMoedaToFloat($('.total-pizza').text())
	if(total_pizza > 0){
		$('.sacola-adicionar').removeAttr('disabled')
	}else{
		$('.sacola-adicionar').attr('disabled', 1)
	}
}

function montaSabores(){
	$('.sabores-input').html('')
	let produto_id = $('#produto_id').val()
	let tamanho_id = $('#tamanho_id').val()

	$.get(path_url + 'api/delivery-link/monta-pizza', {
		sabores_selecionados: sabores_selecionados,
		produto_id: produto_id,
		tamanho_id: tamanho_id
	}).done((res) => {
		console.clear()
		$('.div-sabores-selecionados').html(res.view)
		console.log(res)
		res.sabores.map((x) => {
			$('.sabores-input').append('<input type="hidden" value="'+x.id+'" name="pizza_id[]"/>')
		})
		$('.total-pizza').text('R$ ' + convertFloatToMoeda(res.valor_pizza))
		$('#inp-sub_total').val(res.valor_pizza);

	}).fail((err) => {
		console.log(err)
		toastr.warning(err.responseText)
	})
	
	setTimeout(() => {
		validaValorPizza()
	}, 100)
}

