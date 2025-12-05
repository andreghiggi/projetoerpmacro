@extends('loja.default', ['title' => 'Home'])
@section('content')

<nav id="navigation">
	<div class="container">
		<div id="responsive-nav">
			<!-- NAV -->
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
					<h3 class="title">Produtos em Destaque</h3>
				</div>
			</div>

			<div class="col-md-12">
				<div class="row">
					<div class="products-tabs">
						<!-- tab -->
						<div id="tab1" class="tab-pane active">
							<div class="products-slick" data-nav="#slick-nav-1">
								
								@foreach($produtosEmDestaque as $p)
								<a href="{{ route('loja.produto-detalhe', [$p->hash_ecommerce, 'link='.$config->loja_id])}}">
									<div class="product">
										<div class="product-img">
											<img src="{{ $p->img }}" alt="" style="height: 250px;">
											<div class="product-label">
												@if($p->percentual_desconto > 0)
												<span class="sale">-{{ $p->percentual_desconto }}%</span>
												@endif
												<span class="new">Destaque</span>
											</div>
										</div>
										<div class="product-body">
											<p class="product-category">{{ $p->categoria ? $p->categoria->nome : 'Geral' }}</p>
											<h3 class="product-name"><a href="#">{{ $p->nome }}</a></h3>
											@if(sizeof($p->variacoes) > 0)
											<h4 class="product-price">{{ $p->valorPrimeiraVariacao() }}</h4>
											@else
											@if($p->valor_ecommerce > 0)
											<h4 class="product-price">R${{ __moeda($p->valor_ecommerce) }}
												@if($p->percentual_desconto > 0)
												<del class="product-old-price">
													R$ {{ __moeda($p->valor_ecommerce + ($p->valor_ecommerce*$p->percentual_desconto/100)) }}
												</del>
												@endif
											</h4>
											@endif
											@endif
											
										</div>
									</div>
								</a>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection