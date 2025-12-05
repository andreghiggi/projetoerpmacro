$(function(){
	$.get(path_url+'api/contas-empresa-count', {empresa_id: $('#empresa_id').val()})
	.done((res) => {
		if(res == 0){
			$('.div-conta-empresa').remove()
		}
	}).fail((err) => {
		console.log(err)
		$('.div-conta-empresa').remove()
	})
})