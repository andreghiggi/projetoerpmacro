@extends('qr_code_cardapio.default', ['title' => 'Home'])

@section('css')
<style type="text/css">
	.em-oferta{
		background: var(--main);
		position: absolute;
		color: #fff;
		padding: 6px;
		margin-top: -45px;
		margin-left: 0px;
		width: 80px;
		border-top-right-radius: 10px;
		border-bottom-right-radius: 10px;
	}
</style>
@endsection
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
				<form class="align-middle" action="{{ route('cardapio.pesquisa') }}" method="GET">
					<input type="text" name="pesquisa" placeholder="Digite sua busca..." value="{{ isset($pesquisa) ? $pesquisa : '' }}" />
					<input type="hidden" name="link" value="{{ $link }}" />
					<button>
						<i class="lni lni-search-alt"></i>
					</button>
					<div class="clear"></div>
				</form>
			</div>
		</div>

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
			@if(isset($mesa) && $mesa != null)
			<h5>{{ $mesa->nome }}</h5>
			@endif
			@if(isset($produtosEmDestaque) && sizeof($produtosEmDestaque) > 0)
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
								<a href="#!" onclick="carregaPagina('{{ route('produto-cardapio.modal', [$p->hash_delivery, 'link='.$link]) }}')" title="{{ $p->nome }}">
									<div class="capa" style="background: url('{{ $p->img  }}') no-repeat center center;">
										<span class="nome"></span>
									</div>
									@if($p->oferta_delivery)
									<div class="em-oferta">OFERTA</div>
									@endif
									
									<span class="nome">{{ $p->nome }}</span>
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


		@foreach($categorias as $c)
		@if($c->produtosCardapio && sizeof($c->produtosCardapio) > 0)
		<div class="categoria" id="cat_{{ $c->id }}">
			<div class="row">
				<div class="col-md-10 col-sm-10 col-xs-10">
					<span class="title">{{ $c->nome }}</span>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-2">
					<a class="vertudo" href="{{ route('cardapio.produtos-categoria', [$c->hash_delivery, 'link='.$link]) }}"><i class="lni lni-arrow-right"></i></a>
				</div>
			</div>
			<div class="produtos">
				<div class="row">
					<div class="novalistagem">

						@foreach($c->produtos as $p)
						@if($p->cardapio && $p->validaEstoqueDelivery($p->empresa_id))
						<div class="col-md-6 col-sm-12 col-xs-12">
							<div class="novoproduto" categoria="cat_{{ $c->hash_delivery }}" style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">

								<button style="color:black;background-color:white; text-aligh:Left;padding:0px;" onclick="carregaPagina('{{ route('produto-cardapio.modal', [$p->hash_delivery, 'link='.$link]) }}')" title="{{ $p->nome }}">

									<div class="row" style="text-align:left;">
										<div class="col-md-9 col-sm-7 col-xs-7 npr">
											<span class="nome" style="color:black">{{ $p->nome }}</span>
											<span class="descricao text-dark">
												@if($p->descricao)
												<span class="descricao text-dark">
													{{ substr($p->descricao, 0, 200) }}
												</span>
												@endif
											</span>
											<div class="preco text-dark">
												<span class="blank_valor_anterior"></span>
												@if($p->categoria && $p->categoria->tipo_pizza)
												<span class="valor" style="color:black">
													{{ $p->valorPizzaApresentacao() }}
												</span>
												@else
												<span class="valor" style="color:black">R$ {{ __moeda($p->valor_cardapio) }}</span>
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

		
	</div>
</div>

@endsection
@section('js')
<script src="/food-files/js/main.js"></script>

@isset($produto)
<script type="text/javascript">
	$(function(){
		setTimeout(() => {
			carregaPagina('{{ route('produto-cardapio.modal', [$produto->hash_delivery, 'link='.$link]) }}')
		}, 200)
	})
</script>
@endif
@endsection