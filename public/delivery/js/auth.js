$(function(){

})

$('body').on('blur', '#inp-fone', function () {
	let fone = $(this).val()
	if(fone.length >= 14){
		$.get(path_url + 'api/delivery-link/valida-fone', {
			fone: fone,
			empresa_id: $('#inp-empresa_id').val(),
			carrinho_id: $('#inp-carrinho_id').val()
		}).done((res) => {
			console.log(res)
			$('#inp-nome').val(res.razao_social)
			setTimeout(() => {
				swal("Sucesso", "Cadastro localizado", "success")
				.then(() => {
					location.href = '/food-pagamento?link='+$('#inp-link').val()+'&uid='+res.uid
				})
			}, 300)
		}).fail((err) => {
			console.log(err)
			if(err.status == 404){
				swal("Alerta", "Não foi localizado este telefone cadastrado, informe seu nome para continuar", "warning")
				.then(() => {
					$('.btn-main').css('display', 'block')					
				})
			}else{
				swal("Erro", err.responseJSON, "error")
			}
		})
	}else{
		swal("Alerta", "Informe um telefo válido", "warning")
	}
})

$('body').on('click', '.btn-main', function () {
	let fone = $('#inp-fone').val()
	let nome = $('#inp-nome').val()
	let empresa_id = $('#inp-empresa_id').val()

	$.post(path_url + 'api/delivery-link/cliente-store', {
		fone: fone,
		nome: nome,
		empresa_id: $('#inp-empresa_id').val(),
		carrinho_id: $('#inp-carrinho_id').val()
	}).done((res) => {
		swal("Sucesso", "Obrigado por se registrar", "success")
		.then(() => {
			location.href = '/food-pagamento?link='+$('#inp-link').val()+'&uid='+res.uid
		})

	}).fail((err) => {
		console.log(err)
		swal("Erro", err.responseJSON, "error")
	})
})