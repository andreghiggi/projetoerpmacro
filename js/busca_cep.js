$('body').on('blur', '.cep', function () {

	let cep = $(this).val().replace(/[^0-9]/g,'')
	if(cep.length == 8){
		$.get('https://viacep.com.br/ws/'+cep+'/json')
		.done((res) => {
			console.log(res)
			$('#inp-rua').val(res.logradouro)
			$('#inp-bairro').val(res.bairro)
			$.get(path_url + "api/cidadePorCodigoIbge/" + res.ibge)
			.done((res) => {
				var newOption = new Option(res.info, res.id, false, false);
				$('#inp-cidade_id').append(newOption).trigger('change');
			})
			.fail((err) => {
				console.log(err)
			})
		})
		.fail((err) => {
			console.log(err)
		})
	}
})