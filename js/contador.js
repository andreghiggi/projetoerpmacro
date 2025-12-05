$(document).on("click", ".selecionar-empresa-contador", function () {
	$('#modal-empresas-contador').modal('show')
	let contador_id = $('#empresa_id').val()
	$.get(path_url + 'api/contador/empresas', {contador_id : contador_id})
	.done((success) => {
		console.log(success)
		$('#modal-empresas-contador tbody').html(success)

	})
	.fail((err) => {
		console.log(err)
	})
});



