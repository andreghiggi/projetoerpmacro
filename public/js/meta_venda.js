$(function(){
	$("#inp-meta_id").val('')
	setTimeout(() => {
		$("#inp-periodo").change()
	}, 100)
})

$(document).on("change", "#inp-periodo", function () {
	let periodo = $(this).val()
	$.get(path_url + 'api/metas/vendas-periodo', {
		periodo : periodo,
		empresa_id: $('#empresa_id').val()
	})
	.done((res) => {
		// console.log(res)

		let metaMes = convertMoedaToFloat($('.meta-geral').text())
		$('.total-mes').text("R$ " + convertFloatToMoeda(res))
		if(res > metaMes){
			$('.valor-meta-superior').removeClass('d-none')
			$('.valor-meta-baixo').addClass('d-none')
			$('.total-diferenca').text("R$ " + convertFloatToMoeda(res-metaMes))
		}else{
			$('.valor-meta-superior').addClass('d-none')
			$('.valor-meta-baixo').removeClass('d-none')
			$('.total-diferenca').text("R$ " + convertFloatToMoeda(metaMes-res))
		}
		let perc = __calcPercentual(metaMes, res)
		$('.progress-value').text(perc.toFixed(1)+"%")
		if(perc >= 100){
			$('.progress-value').text("100%")
		}
		$('.progress-bar').css({
			'width': perc+'%'
		})

		$("#inp-meta_id").change()
	})
	.fail((err) => {
		console.log(err)
	})
});

function __calcPercentual(v1, v2){
	if(v1 > v2){
		return 100+((v2-v1)/v1*100);
	}else{
		return 100;
	}
}

$(document).on("change", "#inp-meta_id", function () {
	let meta_id = $(this).val()
	let periodo = $('#inp-periodo').val()

	if(meta_id){
		$.get(path_url + 'api/metas/vendas-funcionario', {meta_id : meta_id, periodo : periodo})
		.done((res) => {
			console.log(res)
			$('.metas').html(res)
			montaGrafico(meta_id, periodo)
		})
		.fail((err) => {
			console.log(err)
		})
	}else{
		$('.metas').html('')
	}
})

function montaGrafico(meta_id, periodo){
	$.get(path_url + 'api/metas/vendas-funcionario-grafico', {meta_id : meta_id, periodo: periodo})
	.done((res) => {
		console.log(res)
		var options = {
			chart: {
				type: 'bar'
			},
			series: [{
				name: 'Vendas',
				data: res.values
			}],
			xaxis: {
				categories: res.labels
			}
		}

		var chart = new ApexCharts(document.querySelector("#chart"), options);

		chart.render();
	})
	.fail((err) => {
		console.log(err)
	})
	
}
