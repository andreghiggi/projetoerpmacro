@extends('qr_code_cardapio.default', ['title' => 'Produtos'])

@section('content')

<div class="middle">
	<div class="container">
		<div class="row rowtitle hidden-xs hidden-sm">
			<div class="col-md-12">
				<div class="title-icon">
					<span>PRODUTOS DA CATEGORIA</span>
				</div>
				
			</div>
			<div class="col-md-12 hidden-xs hidden-sm">
				<div class="clearline"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="search-bar-mobile visible-xs visible-sm">
					<form class="align-middle" method="GET" action="">
						<input type="text" name="busca" placeholder="Digite sua busca..." value="">
						<button>
							<i class="lni lni-search-alt"></i>
						</button>
						<div class="clear"></div>
					</form>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="tv-infinite tv-infinite-menu">
					<a class="" href="{{ route('cardapio.index', ['link='.$link]) }}">Início</a>
					@foreach($categorias as $c)
					@if($c->produtos && sizeof($c->produtos) > 0)
					<a href="{{ route('cardapio.produtos-categoria', [$c->hash_delivery, 'link='.$link]) }}">{{ $c->nome }}</a>
					@endif
					@endforeach
				</div>
			</div>
		</div>
		<div class="categorias no-bottom-mobile">
			<div class="categoria no-bottom-mobile">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-6">
						<strong class="counter">{{ sizeof($produtos) }}</strong>
						<span class="title">Itens:</span>
					</div>
					
				</div>
				<div class="produtos">
					<div class="row tv-grid">
						@foreach($produtos as $p)
						<div class="col-md-3 col-sm-6 col-xs-6">
							<div class="produto">
								<a href="#!" onclick="carregaPagina('{{ route('produto-cardapio.modal', [$p->hash_delivery, 'link='.$link]) }}')" title="lasanha media">
									<div class="capa" style="background: url('{{ $p->img  }}') no-repeat center center;">
										<span class="nome">{{ $p->nome }}</span>
									</div>
									
									@if($p->categoria && $p->categoria->tipo_pizza)
									<span class="valor" style="color:black">
										{{ $p->valorPizzaApresentacao() }}
									</span>
									@else
									<span class="apenas apenas-single">
										Por <br> apenas 
									</span>
									<span class="valor" style="color:black">R$ {{ __moeda($p->valor_cardapio) }}</span>
									@endif
									<div class="detalhes"><i class="icone icone-sacola"></i> <span>Comprar</span></div>
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

@section('js')
<script src="/food-files/js/main.js"></script>

@if($categoria->tipo_pizza)
<script type="text/javascript" src="/delivery/js/pizza.js"></script>
@endif
@endsection

@endsection