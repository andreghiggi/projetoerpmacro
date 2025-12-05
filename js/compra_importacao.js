var line = null

$(document).on("click", ".adiciona-di", function () {
	line = $(this)
	$inpProduto = $(this).closest('td').find('select')
	if($inpProduto.val()){

		let produtoTexto = $inpProduto.find('option:selected').text();
		$('#modal_importacao_di').modal('show')
		$('#modal_importacao_di .nome').text(produtoTexto)

		let dados_importacao = $(this).closest('td').find('.dados_importacao').val()
		if(dados_importacao){
			dados_importacao = JSON.parse(dados_importacao)

			let $inputs = $('#modal_importacao_di').find('input, select, textarea');
			$inputs.each(function() {
				let name = $(this).attr('name');
				$('#modal_importacao_di #inp-'+name).val(dados_importacao[name]).change()
			})
		}
	}else{
		toastr.error("Selecione o produto")
	}
});

$('#inp-cidade_desembarque_id').select2({
    dropdownParent: $('#modal_importacao_di')
});
$('#inp-tpViaTransp').select2({
    dropdownParent: $('#modal_importacao_di')
});
$('#inp-tpIntermedio').select2({
    dropdownParent: $('#modal_importacao_di')
});
$('#inp-UFTerceiro').select2({
    dropdownParent: $('#modal_importacao_di')
});

$(document).on("click", ".salvar-dados-importacao", function () {
	let html = line.closest('td')

	// console.log(html)
	let $inputs = $('#modal_importacao_di').find('input, select, textarea');
	let dadosImportacao = {};

	$inputs.each(function() {
		let name = $(this).attr('name');
		let value = $(this).val(); 
		dadosImportacao[name] = value; 
	});
	// console.log("dadosImportacao", dadosImportacao)
	html.find('.dados_importacao').val(JSON.stringify(dadosImportacao))

});
