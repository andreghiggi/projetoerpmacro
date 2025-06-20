@extends('food.default', ['title' => $servico->nome])
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
						src="{{ $servico->img }}" alt="">
					</div>

				</div>
			</div>
			<div class="col-lg-6 col-md-6">
				<form class="product__details__text" action="{{ route('food.adicionar-carrinho-service') }}" method="post">
					@csrf
					<input type="hidden" value="{{ $config->loja_id }}" name="link">
					<h3>{{ $servico->nome }}</h3>
					<input type="hidden" id="servico_id" name="servico_id" value="{{ $servico->id }}">
					<input type="hidden" id="valor_unitario" value="{{ $servico->valor }}">
					<input type="hidden" id="valor_item" name="valor_item" value="">
					<div class="product__details__price">R$ {{ __moeda($servico->valor) }}</div>
					<p>{{ $servico->descricao }}</p>

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

					<button class="primary-btn btn-main">
						ADICIONAR
						<span id="valor-item">R$ {{ __moeda($servico->valor) }}</span>
					</button>

					<ul>
						@if($servico->categoria)
						<li><b>Categoria</b> <span>{{ $servico->categoria->nome }}</span></li>
						@endif


					</ul>
				</form>
			</div>
		</div>
		
	</div>
</section>
@endsection
@section('js')
<script type="text/javascript" src="/delivery/js/servico_detalhe.js"></script>
@endsection
