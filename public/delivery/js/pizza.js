var _sabores = []
var _tamanho = null
var _valor_pizza = 0
var _maximo_sabores = null
function escolherTamanho(tamanho, key){
	_sabores = []
	percorreArraySaboresAdicionados()
	_tamanho = JSON.parse(tamanho)
	_maximo_sabores = _tamanho.maximo_sabores
	$('.img-size').removeClass('active-border')
	$('.active-'+key).addClass('active-border')

	_tamanho.produtos.map((x) => {
		$('.valor-pizza-'+x.produto_id).text("R$ " + convertFloatToMoeda(x.valor))
	})

	$('.btn-seleciona').removeClass('d-none')
}

function selecionaPizza(id){

	let s = _sabores.find((x) => {
		return x.id == id
	})

	setTimeout(() => {
		if(!s){
			if(_sabores.length >= _maximo_sabores){
				swal("Alerta", _tamanho.nome + " permitido até " + _maximo_sabores + "sabor(es)", "warning")
				return;
			}
			_sabores.push({
				id: id
			})
		}else{
			_sabores = _sabores.filter((x) => {
				return x.id != id
			})
		}
	}, 10)

	setTimeout(() => {
		percorreArraySaboresAdicionados()
		calculaValorPizza()
		if(_sabores.length > 0){
			$('.fixedbutton').removeClass('d-none')
		}else{
			$('.fixedbutton').addClass('d-none')
		}
	}, 20)
}

function calculaValorPizza(){
	if(_sabores.length > 0){
		$.get(path_url + 'api/delivery-link/valor-pizza', 
		{
			sabores: _sabores,
			tamanho_id: _tamanho.id
		})
		.done((res) => {
			console.log(res)
			_valor_pizza = res
			$('.valor-pizza').text(convertFloatToMoeda(res))
		})
		.fail((err) => {
			console.log(err)
		})
	}
}

function percorreArraySaboresAdicionados(){
	$('.fresh-meat').removeClass('bg-active')
	_sabores.map((x) => {
		$('.produto-'+x.id).addClass('bg-active')
	})
}

$('.fixedbutton').click(() => {
	
	$.get(path_url + 'api/delivery-link/hash-pizzas', {sabores: _sabores})
	.done((res) => {
		console.log(res)
		$('#form-pizza .appends').html(res)
		$('#form-pizza #tamanho_id').val(_tamanho.id)
		$('#form-pizza #valor_pizza').val(_valor_pizza)
		setTimeout(() => {
			$('#form-pizza').submit()
		}, 50)
	})
	.fail((err) => {
		console.log(err)
		swal("Erro", "Erro ao adicionar pizza", "error")
	})

})
