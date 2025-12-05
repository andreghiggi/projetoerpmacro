@extends('loja.default', ['title' => 'Carrinho'])
@section('css')
<link rel="stylesheet" type="text/css" href="/css/ecommerce_carrinho.css">
@endsection
@section('content')

<div class="section">
	<div class="container">
		<div class="row">

			<div class="col-md-12">
				<div class="section-title">
					<div class="container">
						<h3 class="title">Carrinho</h3><br>
						<input type="hidden" id="carrinho_id" value="{{ $item->id }}">
						<label>total de itens <strong style="color: #D10024">{{ sizeof($item->itens) }}</strong></label>

						@forelse($item->itens as $i)
						<div class="row cart-item">
							<div class="col-md-3 col-8">
								<img src="{{ $i->produto->img }}"><br><br>
							</div>
							<div class="col-md-2 col-4">
								<div class="qty-label">
									<form action="{{ route('loja.atualiza-quantidade', [$i->id, 'link='.$config->loja_id]) }}" method="post" id="form-update-{{$i->id}}">
										@csrf
										@method('put')
										Quantidade<br>
										<input type="hidden" name="link" value="{{ $config->loja_id }}">
										<input type="hidden" name="produto_variacao_id" value="{{ $i->variacao_id }}">
										<div class="input-number">
											<input class="qtd" name="quantidade" type="number" value="{{ number_format($i->quantidade, 0) }}">
											<span class="qty-up">+</span>
											<span class="qty-down">-</span>
										</div>
									</form>
								</div>
							</div>
							<div class="col-md-6 col-12" style="text-align:right;">
								<label class="nome-produto">{{ $i->produto->nome }} {{ $i->variacao ? $i->variacao->descricao : '' }}</label><br>
								<label>Valor unitário R$ {{ __moeda($i->valor_unitario) }}</label><br>
								<label class="sub_total">R$ {{ __moeda($i->sub_total) }}</label>
							</div>

							<div class="col-md-1 col-12">
								<form action="{{ route('loja.remove-item', [$i->id, 'link='.$config->loja_id]) }}" method="post" id="form-{{$i->id}}">
									@csrf
									@method('delete')
									<button class="btn btn-danger btn-delete" title="Remover Item">
										<i class="fa fa-trash"></i>
									</button>
								</form>
							</div>
						</div>
						@empty
						<div class="col-12">
							<p class="p-cart">Seu carrinho ainda está vazio</p>
						</div>
						@endforelse
					</div>

					@if(sizeof($item->itens) > 0)
					<h4>Subtotal: R$ {{ __moeda($item->valor_total) }}</h4>
					<form method="post" action="{{ route('loja.carrinho-setar-frete', ['link='.$config->loja_id]) }}">
						@csrf
						<div class="row">
							<div class="col-8 col-md-3">
								<div class="input-cep">
									<label>CEP</label>
									<input data-mask="00000-000" class="form-control" name="cep" id="cep" type="tel" value="">
								</div>
							</div>
							<div class="col-4 col-md-2">
								<button type="button" class="btn btn-primary btn-frete">
									<i class="fa fa-truck"></i>
									calcular frete
								</button>
							</div>
						</div>

						<div class="row data-frete">
							@if($dataFrete != null)
							<h5 class="col-md-12" style="margin-top: 20px">Seus endereços cadastrados</h5>
							{!! $dataFrete !!}
							@else

							@if($config->habilitar_retirada)
							<div class="container" style="margin-top: 15px">
								<div class="col-12">
									<input class="radio-frete" type="radio" name="tipo_frete" id="radio" value="0" data-valor="0">
									Retirar na loja
								</div>
							</div>
							@endif

							@if($config->frete_gratis_valor > 0 && $config->frete_gratis_valor <= $item->valor_total)
							<div class="container" style="margin-top: 15px">
								<div class="col-12">
									<input class="radio-frete" type="radio" name="tipo_frete" id="radio" value="gratis" data-valor="0">
									Frete grátis
								</div>
							</div>
							@endif
							@endif
						</div>

						<input type="hidden" name="valor_frete" id="valor_frete">
						<input type="hidden" name="endereco_id" id="endereco_id">

						<div class="row">
							
							<button class="btn btn-success btn-lg btn-pagamento btn-ir-pagamento" disabled>
								<i class="fa fa-money"></i>
								IR PARA PAGAMENTO
							</button>
							<a style="margin-right: 3px" href="{{ route('loja.index', ['link='.$config->loja_id]) }}" class="btn btn-primary btn-lg btn-pagamento btn-continuar-comprando">
								<i class="fa fa-shopping-cart"></i>
								CONTINUAR COMPRANDO
							</a>
						</div>
					</form>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript">

	$(function(){
		$('#valor_frete').val('')
	})
	$(".btn-delete").on("click", function (e) {
		e.preventDefault();
		var form = $(this).parents("form").attr("id");
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

	$('.qtd').on("blur", function (e) {
		e.preventDefault();
		var form = $(this).parents("form").attr("id");
		document.getElementById(form).submit();
	});

	$('.qty-up').on("click", function (e) {
		var form = $(this).parents("form").attr("id");
		document.getElementById(form).submit();
	})

	$('.qty-down').on("click", function (e) {
		var form = $(this).parents("form").attr("id");
		document.getElementById(form).submit();
	})

	$('.btn-frete').on("click", function (e) {
		let carrinho_id = $('#carrinho_id').val()
		let cep = $('#cep').val()
		if(cep.length != 9){
			swal("Alerta", "CEP inválido", "error")
		}else{
			$.get(path_url + 'api/ecommerce/calcular-frete', {
				carrinho_id: carrinho_id,
				cep: cep
			})
			.done((res) => {
				$('.data-frete').html(res)
			})
			.fail((err) => {
				console.log(err)
				$('.data-frete').html('')
				swal("Erro", "Algo deu errado ao calcular o frete", "error")
			})
		}
	})

	$(document).on("click", ".radio-frete", function () {
		$('#endereco_id').val('')
		let valorFrete = $(this).data('valor')
		let enderecoId = $(this).data('endereco-id')
		$('#valor_frete').val(valorFrete)
		$('#endereco_id').val(enderecoId)

		$('.btn-pagamento').removeAttr('disabled')
	})

</script>
@endsection