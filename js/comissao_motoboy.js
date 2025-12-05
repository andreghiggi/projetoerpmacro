$(function(){
	setTimeout(() => {
		validaBtn()
	}, 10)
})

$(document).on("change", "#inp-gerar_conta", function () {
	if($(this).val()){
		$('#modal_conta_pagar select, #modal_conta_pagar input').each(function(){
			console.log($(this))
			$(this).attr('required')
		})
	}else{
		$('#modal_conta_pagar select, #modal_conta_pagar input').each(function(){
			console.log($(this))
			$(this).removeAttr('required')
		})
	}
});

$(document).on("click", ".btn-store", function () {
	let gerarConta = $("#inp-gerar_conta").val()
	if(gerarConta){
		let valor_integral = $('#inp-valor_integral').val()
		let data_vencimento = $('#inp-data_vencimento').val()

		if(!valor_integral){
			swal("Alerta", "Informe o valor", "warning")
			return;
		}

		if(!data_vencimento){
			swal("Alerta", "Informe o vencimento", "warning")
			return;
		}

		$('#form-comissao').submit()
	}
});

$(document).on("click", ".select-all", function () {
	let isChecked = $(this).is(':checked')
	$('.select-check').each(function(){
		$(this).prop('checked', isChecked)
	})
	setTimeout(() => {
		validaBtn()
	}, 10)
});

$(document).on("click", ".select-check", function () {
	validaBtn()
});

function validaBtn(){
	$('.btn-pay').attr('disabled', 1)
	let total = 0
	$('.select-check').each(function(){
		if($(this).is(':checked')){
			let v = $(this).closest('td').next().next().next().next()[0].innerText
			if(v){
				total += convertMoedaToFloat(v)
			}
			$('.btn-pay').removeAttr('disabled')
		}
	})

	setTimeout(() => {
		$('.total-pay').text(convertFloatToMoeda(total))
	}, 50)
}

$(document).on("click", ".btn-pay", function () {
	$('#modal_conta_pagar').modal('show')
	$('#inp-valor_integral').val($('.total-pay').text())
});


