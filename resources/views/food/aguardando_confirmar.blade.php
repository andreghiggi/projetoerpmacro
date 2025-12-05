@extends('food.default', ['title' => 'Aguardando confirmação'])
@section('content')

@section('css')
<style type="text/css">
	.anime{
		margin-top: 120px;
		height: 350px;
	}
</style>
@endsection

<section class="featured spad" style="margin-top: -100px">
	<div class="container">
		<div class="col-12 text-center">
			<img class="anime" src="/delivery/animations/aguarde{{rand(1,3)}}.gif">
			<img style="display: none" class="anime anime-confirmado" src="/delivery/animations/confirmado{{rand(1,3)}}.gif">
			<img style="display: none" class="anime anime-cancelado" src="/delivery/animations/cancelado.gif">
		</div>
	</div>
</section>
@section('js')
<script type="text/javascript">
	$(function(){
		intervalVar = setInterval(() => {
			let pedido_id = '{{ $pedido->id }}';
			$.get(path_url+'api/delivery-link/consulta-pedido/', {pedido_id: pedido_id})
			.done((success) => {
				// console.log(success)
				if(success == "aprovado"){
					clearInterval(intervalVar)
					$('.anime').css('display', 'none')
					$('.anime-confirmado').css('display', 'inline-block')
					setTimeout(() => {
						swal("Sucesso", "Seu Pedido foi confirmado pelo estabelecimento!", "success")
						.then(() => {
							location.reload()
						})
					}, 6000)

					// setTimeout(() => {
					// 	location.reload()
					// }, 6000)
				}else if(success == "cancelado"){
					clearInterval(intervalVar)
					$('.anime').css('display', 'none')
					$('.anime-cancelado').css('display', 'inline-block')
					setTimeout(() => {
						location.reload()
					}, 6000)
				}
			})
			.fail((err) => {
				console.log(err)
			})
		}, 2000)

	})
</script>
@endsection
@endsection