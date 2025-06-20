@extends('food.default', ['title' => 'Pizza'])
@section('content')

@section('css')
<style type="text/css">
	.primary-btn{
		border: none;
	}
</style>
@endsection

<section class="product-details spad" style="margin-top: -100px">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-md-6">
				<div class="product__details__pic">
					<div class="product__details__pic__item">
						<img class="product__details__pic__item--large"
						src="{{ $pizzas[0]->img }}" alt="">
					</div>

				</div>
			</div>
			<div class="col-lg-6 col-md-6">
				<form class="product__details__text" action="{{ route('food.adicionar-carrinho') }}" method="post">
					@csrf
					<input type="hidden" value="{{ $config->loja_id }}" name="link">

					<h3>
						@foreach($pizzas as $pizza)
						1/{{ sizeof($pizzas) }} {{ $pizza->nome }} @if(!$loop->last) | @endif
						@endforeach
					</h3>

					<input type="hidden" id="valor_item" name="valor_item" value="">
					<input type="hidden" id="valor_unitario" value="{{ $valorPizza }}">
					@foreach($pizzas as $pizza)
					<input type="hidden" name="pizza_id[]" value="{{ $pizza->id }}">
					@endforeach
					<input type="hidden" name="tamanho_id" value="{{ $tamanho->id }}">

					<div class="product__details__price">R$ {{ __moeda($valorPizza) }}</div>
					<p>{{ $pizzas[0]->texto_delivery }}</p>

					@if(sizeof($pizzas[0]->adicionaisAtivos) > 0)
					<div class="row mb-3"> 
						<h5 class="col-12 text-main">Selecione os adicionais</h5>
						@foreach($pizzas[0]->adicionaisAtivos as $a)
						<div class="col-md-6">
							<input value="{{$a->adicional->id}}" type="checkbox" class="form-checkbox add-select" name="adicional[]" data-valor="{{$a->adicional->valor}}">
							{{ $a->adicional->nome }} R${{ __moeda($a->adicional->valor) }}
						</div>
						@endforeach
					</div>
					@endif

					<input type="text" class="form-control" name="observacao" placeholder="Alguma observação para o item?">

					<div class="product__details__quantity mt-2">
						<label>Quantidade</label>
						<div class="quantity">
							<div class="pro-qty">
								<input type="text" value="1" id="inp-quantidade" name="quantidade">
							</div>
						</div>
					</div>

					@if($funcionamento == null)
					<div class="card bg-danger">
						<div class="card-header" style="height: 50px;">
							@if(\App\Models\MarketPlaceConfig::getSegmentoServico($config))
							<p class="text-white">Estabelecimento está fechado hoje</p>
							@else
							<p class="text-white">Restaurante está fechado hoje</p>
							@endif
						</div>
					</div>
					@endif

					@if(!\App\Models\MarketPlaceConfig::getSegmentoServico($config))
					@if($funcionamento != null && !$funcionamento->aberto)
					<div class="card bg-danger">
						<div class="card-header" style="height: 50px;">
							<p class="text-white">Restaurante abrirá às {{ __hora_pt($funcionamento->inicio) }}</p>
						</div>
					</div>
					@endif
					@endif

					@if($funcionamento && $funcionamento->aberto)
					<button class="primary-btn btn-main">
						ADICIONAR AO CARRINHO
						<span id="valor-item">R$ {{ __moeda($valorPizza) }}</span>
					</button>
					@endif

					<ul>
						@if($pizzas[0]->categoria)
						<li><b>Categoria</b> <span>{{ $pizzas[0]->categoria->nome }}</span></li>
						@endif

						@if($pizzas[0]->destaque_delivery)
						<li><span>{{ $pizzas[0]->nome }}<samp> esta em destaque em nosso cardápio</samp></span></li>
						@endif
					</ul>
				</form>
			</div>
		</div>

		@foreach($pizzas as $pizza)
		@if(sizeof($pizza->ingredientes) > 0)
		<div class="col-lg-12">
			<div class="product__details__tab__desc">
				<h5 style="font-weight: bold">Ingredientes de <strong>{{ $pizza->nome }}</strong></h5>
				<p>@foreach($pizza->ingredientes as $i) {{ $i->ingrediente }}{{ !$loop->last ? ', ' : '' }} @endforeach</p>
			</div>
		</div>
		@endif
		@endforeach
	</div>
</section>
@endsection
@section('js')
<script type="text/javascript" src="/delivery/js/produto_detalhe.js"></script>
@endsection
