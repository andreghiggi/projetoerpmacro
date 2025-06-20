@extends('food.default', ['title' => $produto->nome])
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
						src="{{ $produto->img }}" alt="">
					</div>

				</div>
			</div>
			<div class="col-lg-6 col-md-6">
				<form class="product__details__text" action="{{ route('food.adicionar-carrinho') }}" method="post">
					@csrf
					<input type="hidden" value="{{ $config->loja_id }}" name="link">
					<h3>{{ $produto->nome }}</h3>
					<input type="hidden" id="produto_id" name="produto_id" value="{{ $produto->id }}">
					<input type="hidden" id="valor_unitario" value="{{ $produto->valor_delivery }}">
					<input type="hidden" id="valor_item" name="valor_item" value="">
					<div class="product__details__price">R$ {{ __moeda($produto->valor_delivery) }}</div>
					<p>{{ $produto->texto_delivery }}</p>

					@if(sizeof($produto->adicionaisAtivos) > 0)
					<div class="row mb-3"> 
						<h5 class="col-12 text-main">Selecione os adicionais</h5>
						@foreach($produto->adicionaisAtivos as $a)
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
						<span id="valor-item">R$ {{ __moeda($produto->valor_delivery) }}</span>
					</button>
					@endif

					<ul>
						@if($produto->categoria)
						<li><b>Categoria</b> <span>{{ $produto->categoria->nome }}</span></li>
						@endif

						@if($produto->destaque_delivery)
						<li><span>{{ $produto->nome }}<samp> esta em destaque em nosso cardápio</samp></span></li>
						@endif
					</ul>
				</form>
			</div>
		</div>
		@if(sizeof($produto->ingredientes) > 0)
		<div class="col-lg-12">
			<div class="product__details__tab__desc">
				<h5 style="font-weight: bold">Ingredientes</h5>
				<p>@foreach($produto->ingredientes as $i) {{ $i->ingrediente }}{{ !$loop->last ? ', ' : '' }} @endforeach</p>
			</div>
		</div>
		@endif
	</div>
</section>
@endsection
@section('js')
<script type="text/javascript" src="/delivery/js/produto_detalhe.js"></script>
@endsection
