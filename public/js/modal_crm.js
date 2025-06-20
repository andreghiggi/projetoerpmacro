function montaSelect2(){
	$('#inp-tipo_registro').val('').change()
	$('#inp-status').val('').change()
	$('#inp-conclusao').val('').change()
	$('#inp-alerta').val('1').change()
	$('#inp-assunto').val('')
	$('#inp-data_retorno').val('')
	$('#inp-data_entrega').val('')
	
	$("#inp-funcionario_id").select2({
		minimumInputLength: 2,
		language: "pt-BR",
		dropdownParent: $('#modal_crm'),
		placeholder: "Digite para buscar o funcionário",

		ajax: {
			cache: true,
			url: path_url + "api/funcionarios/pesquisa",
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

	$("#inp-registro_id").select2({
		minimumInputLength: 2,
		language: "pt-BR",
		placeholder: "Digite para buscar o registro pelo código",
		dropdownParent: $('#modal_crm'),
		width: "100%",
		ajax: {
			cache: true,
			url: path_url + "api/crm/referenciar-registro",
			dataType: "json",
			data: function (params) {
				let empresa_id = $('#empresa_id').val()
				let tipo_registro = $('#inp-tipo_registro').val()
				var query = {
					pesquisa: params.term,
					empresa_id: empresa_id,
					tipo_registro: tipo_registro
				};
				return query;
			},
			processResults: function (response) {
				var results = [];

				$.each(response, function (i, v) {
					var o = {};
					o.id = v.id;

					o.text = v.descricao;
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

$(document).on("change", "#inp-tipo_registro", function () {
	referenciar()
});

function referenciar(){
	if($("#inp-tipo_registro").val()){
		$('.d-registro').removeClass('d-none')
	}else{
		$('.d-registro').addClass('d-none')
	}
}