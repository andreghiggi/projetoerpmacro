@extends('loja.default', ['title' => 'Produtos'])
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
	<div class="container">
		<div class="row">

			<div class="col-md-12">
				<div class="section-title">
					<h3 class="title">Produtos</h3>

				</div>
			</div>

			<div class="col-md-12">
				<div class="row">
					<div class="products-tabs">
						<!-- tab -->

							<div class="row" data-nav="#slick-nav-">
								
								@foreach($produtos as $p)
								<div class="product col-md-4 col-12">
									<div class="product-img">
										<img src="{{ $p->img }}" alt="" style="height: 250px">
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
										<h4 class="product-price">R${{ __moeda($p->valor_ecommerce) }}
											@if($p->percentual_desconto > 0)
											<del class="product-old-price">
												R$ {{ __moeda($p->valor_ecommerce + ($p->valor_ecommerce*$p->percentual_desconto/100)) }}
											</del>
											@endif
										</h4>
										
									</div>
									<div class="add-to-car">
										<a href="{{ route('loja.produto-detalhe', [$p->hash_ecommerce, 'link='.$config->loja_id])}}" class="btn w-100 btn-success"><i class="fa fa-shopping-cart"></i> 
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