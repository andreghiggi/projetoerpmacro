@extends('loja.default', ['title' => 'Produtos'])
@section('css')
<link rel="stylesheet" type="text/css" href="/css/ecommerce_produtos_categoria.css">
@endsection
@section('content')
<nav id="navigation">
	<div class="container">
		<div id="responsive-nav">
			<ul class="main-nav nav navbar-nav">
				<li class="active"><a href="{{ route('loja.index', ['link='.$config->loja_id]) }}">Home</a></li>
				@foreach($categorias as $c)
				@if($c->hash_ecommerce)
				<li><a href="{{ route('loja.produtos-categoria', [$c->hash_ecommerce, 'link='.$config->loja_id]) }}">{{ $c->nome }}</a></li>
				@endif
				@endforeach
			</ul>
		</div>
	</div>
</nav>
<div class="section">
	<div class="container">
		<div class="row">

			<div class="col-md-12">
				<div class="section-title">
					<h3 class="title">Produtos em <strong class="text-danger">{{ $categoria->nome }}</strong></h3>

				</div>
			</div>

			<div class="col-md-12">
				<div class="row">
					<div class="products-tabs">

						<div class="row" data-nav="#slick-nav-">
							
							@foreach($produtos as $p)
							<div class="product col-md-4 col-12" style="height: 480px;">
								<div class="product-img">
									<img src="{{ $p->img }}" alt="" style="height: 320px">
									<div class="product-label">
										@if($p->percentual_desconto > 0)
										<span class="sale">-{{ $p->percentual_desconto }}%</span>
										@endif
										<span class="new">Destaque</span>
									</div>
								</div>
								<div class="product-body">
									<p class="product-category">{{ $p->categoria ? $p->categoria->nome : '' }}</p>
									<h3 class="product-name"><a href="#">{{ $p->nome }}</a></h3>
									@if($p->valor_ecommerce > 0)
									<h4 class="product-price">R${{ __moeda($p->valor_ecommerce) }}
										@if($p->percentual_desconto > 0)
										<del class="product-old-price">
											R$ {{ __moeda($p->valor_ecommerce + ($p->valor_ecommerce*$p->percentual_desconto/100)) }}
										</del>
										@endif
									</h4>
									@else
									<h4 style="height: 20px;"></h4>
									@endif
									
								</div>
								<div class="add-to-car">
									<a href="{{ route('loja.produto-detalhe', [$p->hash_ecommerce, 'link='.$config->loja_id])}}" class="btn w-100 btn-success">
										Adicionar ao carrinho
									</a>
								</div>
							</div>
							@endforeach

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection