$(document).on("change", "#inp-empresa_boleto", function () {
	let empresa_id = $(this).val()
	if(empresa_id){
		$.get(path_url + "api/empresas/find", {empresa_id: empresa_id})
		.done((e) => {
			console.log(e)
			$('#inp-razao_social').val(e.nome)
			$('#inp-cpf_cnpj').val(e.cpf_cnpj)
			$('#inp-rua').val(e.rua)
			$('#inp-numero').val(e.numero)
			$('#inp-bairro').val(e.bairro)
			$('#inp-cep').val(e.cep)
			$('#inp-email').val(e.email)
			$('#inp-telefone').val(e.celular)
			$('#inp-vencimento').val(e.vencimento)
			if(e.plano){
				$('#inp-plano').val(e.plano.plano.nome)
				$('#inp-plano_id').val(e.plano.plano.id)
				$('#inp-valor').val(convertFloatToMoeda(e.plano.valor))
			}else{
				swal("Alerta", "Empresa sem plano atribuído", "warning")
				clearForm()
			}
		})
		.fail((err) => {
			console.log(err)

		})
	}
})

$('#form-financeiro').on('submit', function(e) {
	$body = $("body");
	$body.addClass("loading");
})

$("#inp-empresa_boleto").select2({
	minimumInputLength: 2,
	language: "pt-BR",
	placeholder: "Digite para buscar a empresa",
	width: "100%",
	ajax: {
		cache: true,
		url: path_url + "api/empresas/find-boleto",
		dataType: "json",
		data: function (params) {

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

function clearForm(){
	$("input").each(function (e, v) {
		$(this).val('')
		$("#inp-empresa").val('').change()
	})

}

$('#btn-gerar').click(() => {
	let contSucesso = 0;
	let contErro = 0;
	let contTotal = parseInt($('.total-boletos').text());
	cont = 0;

	$(".empresa_id").each(function (e, v) {
		let empresa_id = $(this).val()
		$valor = $(this).closest('td').next().next().next().next().find('input');
		$vencimento = $(this).closest('td').next().next().next().next().next().find('input');

		let data = {
			empresa_id: empresa_id,
			valor: $valor.val(),
			vencimento: $vencimento.val(),
		}
		// console.log(data)

		$.post(path_url + "api/financeiro-boleto/store", data)
		.done((e) => {
			console.log(e)
			contSucesso++
			cont++
			$('.tr_'+empresa_id).addClass('bg-success')
			if(cont == contTotal){
				swal("Processo finalizado", "Total de boletos com sucesso: " + contSucesso + "\nTotal de boletos com erro: " + contErro, "success")
			}
		})
		.fail((err) => {
			console.log(err)
			contErro++
			cont++
			$('.tr_'+empresa_id).addClass('bg-danger')
			if(cont == contTotal){
				swal("Processo finalizado", "Total de boletos com sucesso: " + contSucesso + "\nTotal de boletos com erro: " + contErro, "success")
			}
		})
	})
})

