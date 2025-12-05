@extends('food.default', ['title' => 'Home'])

@section('content')

<div class="container">
	<div class="row visible-xs visible-sm">
		<div class="col-md-12">
			<div class="clearline"></div>
		</div>
	</div>
	<div class="row" id="menu_mobile">
		<div class="col-md-12">
			<div class="search-bar-mobile visible-xs visible-sm">
				<form class="align-middle" action="{{ route('food.pesquisa') }}" method="GET">
					<input type="text" name="pesquisa" placeholder="Digite sua busca..." value="{{ isset($pesquisa) ? $pesquisa : '' }}" />
					<input type="hidden" name="link" value="{{ $config->loja_id }}" />
					<button>
						<i class="lni lni-search-alt"></i>
					</button>
					<div class="clear"></div>
				</form>
			</div>
		</div>


		@if(sizeof($banners) > 0)
		<div class="slideshow-container">

			@foreach($banners as $key => $b)

			<div class="mySlides">
				<img src="{{ $b->img }}" style="width:100%; border-radius: 5px;">
				@if($b->descricao)
				<div class="text">{{ $b->descricao }}</div>
				@endif

				@if($b->produto)
				<a class="btn-comprar-agora" href="#!" onclick="carregaPagina('{{ route('produto-delivery.modal', [$b->produto->hash_delivery]) }}')">
					comprar agora
				</a>

				@endif
			</div>
			@endforeach

			<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
			<a class="next" onclick="plusSlides(1)">&#10095;</a>
		</div>
		<br>
		<div style="text-align:center">
			@foreach($banners as $key => $b)
			<span class="dot" onclick="currentSlide({{$key+1}})"></span>
			@endforeach
		</div>
		@endif

		<div class="row">
			<div class="col-md-12">
				<div class="search-bar-mobile visible-xs visible-sm">
					<div id="menu_topo" class="tv-infinite tv-infinite-menu" style="background-color:white">
						@foreach($categorias as $c)
						@if($c->produtosDelivery && sizeof($c->produtosDelivery) > 0)
						<a @if($loop->first) class="active" @endif id="link_cat_{{ $c->id }}" onclick="showCategoria('cat_'+{{ $c->id }}, this)">{{ $c->nome }}</a>
						@else
						@if($c->servicos && sizeof($c->servicos) > 0)
						<a @if($loop->first) class="active" @endif id="link_cat_{{ $c->id }}" onclick="showCategoria('cat_'+{{ $c->id }}, this)">{{ $c->nome }}</a>
						@endif

						@endif
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="categorias">
		<div class="categoria" id="cat_000">

			@if(__isSegmentoServico($config->empresa_id) && sizeof($servicosEmDestaque) > 0)
			<div class="row">
				<div class="col-md-10 col-sm-10 col-xs-10">
					<span class="title">Serviços em destaque</span>
				</div>
			</div>
			<div class="produtos">
				<div class="row">
					<div class="tv-infinite">
						@foreach($servicosEmDestaque as $s)
						<div class="col-md-3 col-infinite">
							<div class="produto">
								<a href="#!" onclick="carregaPagina('{{ route('servico-delivery.modal', [$s->hash_delivery]) }}')" title="{{ $s->nome }}">
									<div class="capa" style="background: url('{{ $s->img  }}') no-repeat center center;">
										<span class="nome"></span>
									</div>
									
									
									<span class="nome">{{ $s->nome }}</span>
									<span class="valor">R$ {{ __moeda($s->valor) }}</span>
									
								</a>
							</div>
						</div>
						@endforeach
					</div>
				</div>
			</div>
			@endif

			@if(__isSegmentoProduto($config->empresa_id) && sizeof($produtosEmDestaque) > 0)
			<div class="row">
				<div class="col-md-10 col-sm-10 col-xs-10">
					<span class="title">Produtos em destaque</span>
				</div>
			</div>
			<div class="produtos">
				<div class="row">
					<div class="tv-infinite">
						@foreach($produtosEmDestaque as $p)
						<div class="col-md-3 col-infinite">
							<div class="produto" style="height: 270px">
								<a href="#!" onclick="carregaPagina('{{ route('produto-delivery.modal', [$p->hash_delivery]) }}')" title="{{ $p->nome }}">
									<div class="capa" style="background: url('{{ $p->img  }}') no-repeat center center;">
										<span class="nome"></span>
									</div>
									@if($p->oferta_delivery)
									<div class="em-oferta">OFERTA</div>
									@endif
									
									<span class="nome">{{ $p->nome }}</span>

									@if($p->categoria && $p->categoria->tipo_pizza)
									<span class="valor">
										{{ $p->valorPizzaApresentacao() }}
									</span>
									@else
									<span class="valor">R$ {{ __moeda($p->valor_delivery) }}</span>
									@endif

									@if(sizeof($p->adicionais) > 0)
									<span class="apenas">Este item possui</span>
									<span class="apenas">opcionais</span>
									@endif
								</a>
							</div>
						</div>
						@endforeach
					</div>
				</div>
			</div>
			@endif
		</div>

		@if(__isSegmentoProduto($config->empresa_id))
		@foreach($categorias as $c)
		@if($c->produtosDelivery && sizeof($c->produtosDelivery) > 0)
		<div class="categoria" id="cat_{{ $c->id }}">
			<div class="row">
				<div class="col-md-10 col-sm-10 col-xs-10">
					<span class="title">{{ $c->nome }}</span>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-2">
					<a class="vertudo" href="{{ route('food.produtos-categoria', [$c->hash_delivery, 'link='.$config->loja_id]) }}"><i class="lni lni-arrow-right"></i></a>
				</div>
			</div>
			<div class="produtos">
				<div class="row">
					<div class="novalistagem">

						@foreach($c->produtos as $p)
						@if($p->delivery && $p->validaEstoqueDelivery($c->empresa_id))
						<div class="col-md-6 col-sm-12 col-xs-12">
							<div class="novoproduto" categoria="cat_{{ $c->hash_delivery }}" style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
								<button style="color:black;background-color:white; text-aligh:Left;padding:0px;" onclick="carregaPagina('{{ route('produto-delivery.modal', [$p->hash_delivery]) }}')" title="{{ $p->nome }}">

									<div class="row" style="text-align:left;">
										<div class="col-md-9 col-sm-7 col-xs-7 npr">
											<span class="nome" style="color:black">{{ $p->nome }}</span>
											@if($p->texto_delivery)
											<span class="descricao text-dark">
												{{ substr($p->texto_delivery, 0, 200) }}
											</span>
											@endif
											<div class="preco text-dark">
												<span class="blank_valor_anterior"></span>
												@if($p->categoria && $p->categoria->tipo_pizza)
												<span class="valor" style="color:black">
													{{ $p->valorPizzaApresentacao() }}
												</span>
												@else
												<span class="valor" style="color:black">R$ {{ __moeda($p->valor_delivery) }}</span>
												@endif
											</div>
										</div>
										<div class="col-md-3 col-sm-5 col-xs-5">
											<div class="capa">
												<img src="{{ $p->img }}" />
											</div>
										</div>
									</div>
								</button>
							</div>
						</div>

						@endif
						@endforeach
						
					</div>
				</div>
			</div>
		</div>
		@endif
		@endforeach
		@endif

		@if(__isSegmentoServico($config->empresa_id))
		@foreach($categorias as $c)
		@if($c->servicosMarketplace && sizeof($c->servicosMarketplace) > 0)
		<div class="categoria" id="cat_{{ $c->id }}">
			<div class="row">
				<div class="col-md-10 col-sm-10 col-xs-10">
					<span class="title">{{ $c->nome }}</span>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-2">
					<a class="vertudo" href="{{ route('food.servicos-categoria', [$c->hash_delivery, 'link='.$config->loja_id]) }}"><i class="lni lni-arrow-right"></i></a>
				</div>
			</div>
			<div class="produtos">
				<div class="row">
					<div class="novalistagem">

						@foreach($c->servicosMarketplace as $p)

						<div class="col-md-6 col-sm-12 col-xs-12">
							<div class="novoproduto" categoria="cat_{{ $c->hash_delivery }}" style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
								<button style="color:black;background-color:white; text-aligh:Left;padding:0px;" onclick="carregaPagina('{{ route('servico-delivery.modal', [$p->hash_delivery]) }}')" title="{{ $p->nome }}">

									<div class="row" style="text-align:left;">
										<div class="col-md-9 col-sm-7 col-xs-7 npr">
											<span class="nome" style="color:black">{{ $p->nome }}</span>
											@if($p->texto_delivery)
											<span class="descricao text-dark">
												{{ substr($p->texto_delivery, 0, 200) }}
											</span>
											@endif
											<div class="preco text-dark">
												<span class="blank_valor_anterior"></span>
												
												<span class="valor" style="color:black">R$ {{ __moeda($p->valor) }}</span>

											</div>
										</div>
										<div class="col-md-3 col-sm-5 col-xs-5">
											<div class="capa">
												<img src="{{ $p->img }}" />
											</div>
										</div>
									</div>
								</button>
							</div>
						</div>

						@endforeach
						
					</div>
				</div>
			</div>
		</div>
		@endif
		@endforeach
		@endif
		
	</div>
</div>

@endsection
@section('js')
<script src="/food-files/js/main.js"></script>


<script type="text/javascript">

	let slideIndex = 1;
	showSlides(slideIndex);

	function plusSlides(n) {
		showSlides(slideIndex += n);
	}

	function currentSlide(n) {
		showSlides(slideIndex = n);
	}

	function showSlides(n) {
		let i;
		let slides = document.getElementsByClassName("mySlides");
		let dots = document.getElementsByClassName("dot");
		if (n > slides.length) {
			slideIndex = 1
		}
		if (n < 1) {
			slideIndex = slides.length
		}
		for (i = 0; i < slides.length; i++) {
			slides[i].style.display = "none";
		}
		for (i = 0; i < dots.length; i++) {
			dots[i].className = dots[i].className.replace(" active-dot", "");
		}
		slides[slideIndex-1].style.display = "block";
		dots[slideIndex-1].className += " active-dot";
	}

	setInterval(() => {
		$('.mySlides').addClass('fade')
		plusSlides(1)
		setTimeout(() => {
			$('.mySlides').removeClass('fade')
		}, 1500)
	}, 6000)
</script>

@isset($produto)
<script type="text/javascript">
	$(function(){
		setTimeout(() => {
			carregaPagina('{{ route('produto-delivery.modal', [$produto->hash_delivery]) }}')
		}, 200)
	})
</script>
@endif

@isset($servico)
<script type="text/javascript">
	$(function(){
		setTimeout(() => {
			carregaPagina('{{ route('servico-delivery.modal', [$servico->hash_delivery]) }}')
		}, 200)
	})


</script>
@endif
@endsection