$(".quantity").on("click", function (e) {
	console.log($(this).data("item"))
	let i = $(this).data("item")
	setTimeout(() => {
		$('#form-cart-'+i).submit()
	}, 200)
})

$(".btn-delete").on("click", function (e) {
	e.preventDefault();
	var form = $(this).parents("form").attr("id");
	console.log(form)
	swal({
		title: "Você está certo?",
		text: "deseja realmente remover este item do seu carrinho?",
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

$(".btn-cupom").on("click", function (e) {
	let codigo = $('#inp-cupom').val()

	if(codigo.length == 6){
		$.get(path_url + 'api/delivery-link/cupom', {
			cupom: codigo,
			empresa_id: $('#inp-empresa_id').val(),
			total: $('#inp-total').val(),
			uid: $('#inp-cliente_uid').val(),
			carrinho_id: $('#inp-carrinho_id').val()

		}).done((res) => {
			console.log(res)
			$('.vl-desconto').text("R$ " + convertFloatToMoeda(res))
			let total = $('#inp-total').val()
			$('.total-cart').text("R$ " + convertFloatToMoeda(total-res))
		}).fail((err) => {
			console.log(err)
			swal("Erro", err.responseJSON, "error")
			$('#inp-cupom').val('')
		})
	}else{
		swal("Erro", "O código precisa ser de 6 digitos", "error")
	}
});

function editEndereco(e){
	e = JSON.parse(e)
	console.log('._bairro_id option[value='+e.bairro_id+']')
	$('#modal-edit-endereco').modal('show')
	$('#endereco_id').val(e.id)
	$('#rua').val(e.rua)
	$('#numero').val(e.numero)
	$('#referencia').val(e.referencia)
	$('#cep').val(e.cep)

	$('._bairro_id').val(e.bairro_id).change();
	$('#tipo').val(e.tipo).change()
	$('#padrao').val(e.padrao).change()
	
}


