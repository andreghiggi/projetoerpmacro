$(function(){
	// $('#inp-data_agendamento').val('')
})

$(document).on("click", "#btn-buscar-horarios", function () {
	
	let data_agendamento = $("#inp-data_agendamento").val()
	if(data_agendamento){
		$('.modal-loading').modal('show')
		$.get(path_url + 'api/delivery-link/get-atendentes', {
			data_agendamento: data_agendamento, carrinho_id: $('#inp-carrinho_id').val() 
		})
		.done(function(success) {
			console.clear()
			console.log(success)
			$('.modal-loading').modal('hide')
			$('.atendentes').html(success)
			$('html, body').animate({
				scrollTop: parseInt($("#lbl-horarios").offset().top)
			}, 1000);
		})
		.fail(function(err) {
			console.log(err)
			$('.modal-loading').modal('hide')
			toastr.error('Algo deu errado!');
		})
	}else{
		toastr.error('Selecione uma data!');
	}
})

$(document).on("click", ".botao-agendamento", function () {
	$('#modal-escolhe-agendamento').modal('show')
	let elem = $(this).closest('.card')
	let funcionario_id = elem.find('.funcionario_id').val()
	let funcionario_nome = elem.find('.funcionario_nome').val()
	let inicio = elem.find('.inicio').val()
	let fim = elem.find('.fim').val()
	let data = elem.find('.data').val()
	let data_pt = elem.find('.data_pt').val()

	$('#modal-escolhe-agendamento .atendente').text(funcionario_nome)
	$('#modal-escolhe-agendamento .inicio').text(inicio)
	$('#modal-escolhe-agendamento .fim').text(fim)
	$('#modal-escolhe-agendamento .data').text(data_pt)

	$('#modal-escolhe-agendamento #funcionario_id').val(funcionario_id)
	$('#modal-escolhe-agendamento #inicio').val(inicio)
	$('#modal-escolhe-agendamento #fim').val(fim)
	$('#modal-escolhe-agendamento #data').val(data)
})

$(document).on("click", ".btn-atendente", function () {
	$('.card-horario').addClass('d-none')
	$('.btn-atendente').removeClass('active')
	$(this).addClass('active')
	let id = $(this).data('id')
	if(id > 0){
		$('.card_'+id).removeClass('d-none')
	}else{
		$('.card-horario').removeClass('d-none')
	}
})


