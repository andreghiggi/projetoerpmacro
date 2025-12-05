$(function(){
	$("#inp-meta_id").val('')
})

$(document).on("change", "#inp-meta_id", function () {
	let meta_id = $(this).val()

	if(meta_id){
		$.get(path_url + 'api/metas/os-funcionario', {meta_id : meta_id})
		.done((res) => {
			// console.log(res)
			$('.metas').html(res)
			montaGrafico(meta_id)
		})
		.fail((err) => {
			console.log(err)
		})
	}else{
		$('.metas').html('')
	}
})

function montaGrafico(meta_id){
	$.get(path_url + 'api/metas/os-funcionario-grafico', {meta_id : meta_id})
	.done((res) => {
		console.log(res)
		var options = {
			chart: {
				type: 'bar'
			},
			series: [{
				name: 'Ordens de Serviço',
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
