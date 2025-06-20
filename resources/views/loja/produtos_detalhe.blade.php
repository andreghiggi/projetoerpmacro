@extends('loja.default', ['title' => $produto->nome])
@section('css')
<style type="text/css">
	.w-100{
		width: 100%;
		margin-bottom: 10px;
	}
</style>

@endsection
@section('content')

<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<div class="row">
			<!-- Product main img -->
			<div class="col-md-5 col-md-push-2">
				<div id="product-main-img">
					<div class="product-preview">
						<img src="{{ $produto->img }}">
					</div>
					@foreach($produto->galeria as $g)
					<div class="product-preview">
						<img src="{{ $g->img }}">
					</div>
					@endforeach
				</div>
			</div>
			<!-- /Product main img -->

			<!-- Product thumb imgs -->
			<div class="col-md-2  col-md-pull-5">
				<div id="product-imgs">
					<div class="product-preview">
						<img src="{{ $produto->img }}">
					</div>
					@foreach($produto->galeria as $g)
					<div class="product-preview">
						<img src="{{ $g->img }}">
					</div>
					@endforeach

				</div>
			</div>
			<!-- /Product thumb imgs -->

			<!-- Product details -->
			<div class="col-md-5">
				<div class="product-details">
					<h2 class="product-name">{{ $produto->nome }}</h2>
					
					<div>
						<h3 class="product-price">R${{ __moeda($produto->valor_ecommerce) }}
							@if($produto->percentual_desconto > 0)
							<del class="product-old-price">
								R$ {{ __moeda($produto->valor_ecommerce + ($produto->valor_ecommerce*$produto->percentual_desconto/100)) }}
							</del>
							@endif
						</h3>
						<span class="product-available">Em estoque</span>
					</div>
					<p>{{ $produto->descricao_ecommerce }}</p>

					<form method="post" action="{{ route('loja.adicionar-carrinho')}}">
						<input type="hidden" name="link" value="{{ $config->loja_id }}">
						<input type="hidden" name="produto_id" value="{{ $produto->id }}">
						@csrf
						@if(sizeof($produto->variacoes) > 0)
						<div class="product-options">
							<label>
								Selecione
								<select id="variacao_id" name="variacao_id" class="input-select" style="width: 100%">
									@foreach($produto->variacoes as $v)
									@if($v->valor > 0)
									<option value="{{ $v->id }}">{{ $v->descricao }}</option>
									@endif
									@endforeach
								</select>
							</label>
						</div>
						@endif

						<div class="add-to-cart">
							<div class="qty-label">
								Quantidade
								<div class="input-number">
									<input name="quantidade" type="number" value="1">
									<span class="qty-up">+</span>
									<span class="qty-down">-</span>
								</div>
							</div><br>
							<button class="add-to-cart-btn" style="margin-top: 20px"><i class="fa fa-shopping-cart"></i>Adicionar ao carrinho</button>
						</div>
					</form>

					@if($produto->categoria)
					<ul class="product-links">
						<li>Categoria:</li>
						<li><a href="#">{{ $produto->categoria->nome }}</a></li>
					</ul>
					@endif

				</div>
			</div>
			<!-- /Product details -->

			<!-- Product tab -->
			<div class="col-md-12">
				<div id="product-tab">
					<!-- product tab nav -->

					<!-- /product tab nav -->

					<!-- product tab content -->
					<div class="tab-content">
						<!-- descrição  -->
						<div id="tab1" class="tab-pane fade in active">
							<div class="row">
								<div class="col-md-12">
									{!! $produto->texto_ecommerce !!}
								</div>
							</div>
						</div>


					</div>
					<!-- /product tab content  -->
				</div>
			</div>
			<!-- /product tab -->
		</div>
		<!-- /row -->
	</div>
	<!-- /container -->
</div>

@endsection

@section('js')
<script type="text/javascript">
	$(function(){
		getVariacao()
	})

	$(document).on("change", "#variacao_id", function () {
		getVariacao()
	})

	function getVariacao(){
		let variacao_id = $('#variacao_id').val()
		$.get(path_url+'api/ecommerce/variacao/', {variacao_id: variacao_id})
		.done((success) => {
			$('.product-price').html('R$'+convertFloatToMoeda(success.valor))
		})
		.fail((err) => {
			console.log(err)
		})
	}
</script>
@endsection