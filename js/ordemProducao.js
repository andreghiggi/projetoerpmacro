$('.btn-add').on("click", function () {
	let empresa_id = $('#empresa_id').val()

	$.get(path_url + "api/ordemProducao/linha", {empresa_id: empresa_id})
	.done((success) => {
		$('.tbody-produtos').append(success)

		$selectCliente = $('.tbody-produtos').last().find('.cliente_id')
		$selectCliente.select2({
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

						o.text = "["+v.numero_sequencial+"] " + v.razao_social + " - " + v.cpf_cnpj;
						o.value = v.id;
						results.push(o);
					});
					return {
						results: results,
					};
				},
			},
		});

		$selectProduto = $('.tbody-produtos').last().find('.produto_id')
		$selectProduto.select2({
			minimumInputLength: 2,
			language: "pt-BR",
			placeholder: "Digite para buscar o produto",
			width: "100%",
			ajax: {
				cache: true,
				url: path_url + "api/produtos/tipo-producao",
				dataType: "json",
				data: function (params) {
					let empresa_id = $('#empresa_id').val()
					console.clear();
					var query = {
						pesquisa: params.term,
						empresa_id: empresa_id,
						usuario_id: $('#usuario_id').val()
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
						
						o.text = v.nome

						if(parseFloat(v.valor_unitario) > 0){
							o.text += ' R$ ' + convertFloatToMoeda(v.valor_unitario);
						}
						
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
	})
	.fail((err) => {
		console.log(err);
	});
});

$(function(){
	if($('#_edit').length > 0){

		$('.tbody-produtos').find(".produto_id").each(function () {

			$(this).select2({
				minimumInputLength: 2,
				language: "pt-BR",
				placeholder: "Digite para buscar o produto",
				width: "100%",
				ajax: {
					cache: true,
					url: path_url + "api/produtos/tipo-producao",
					dataType: "json",
					data: function (params) {
						let empresa_id = $('#empresa_id').val()
						console.clear();
						var query = {
							pesquisa: params.term,
							empresa_id: empresa_id,
							usuario_id: $('#usuario_id').val()
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
							
							o.text = v.nome

							if(parseFloat(v.valor_unitario) > 0){
								o.text += ' R$ ' + convertFloatToMoeda(v.valor_unitario);
							}
							
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
		})
	}
})

