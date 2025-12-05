@extends('food.default', ['title' => 'Ofertas'])

@section('content')

<div class="middle">
	<div class="container">
		<div class="row rowtitle hidden-xs hidden-sm">
			<div class="col-md-12">
				<div class="title-icon">
					<span>PRODUTOS EM OFERTA</span>
				</div>
				
			</div>
			<div class="col-md-12 hidden-xs hidden-sm">
				<div class="clearline"></div>
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
								<a href="#!" onclick="carregaPagina('{{ route('produto-delivery.modal', [$p->hash_delivery]) }}')" title="lasanha media">
									<div class="capa" style="background: url('{{ $p->img  }}') no-repeat center center;">
										<span class="nome">{{ $p->nome }}</span>
									</div>
									@if($p->categoria && $p->categoria->tipo_pizza)
									<span class="apenas apenas-single">
										Tipo <br> pizza 
									</span>
									<span class="valor" style="font-size: 15px; margin-bottom: 9px">
										{{ $p->valorPizzaApresentacao() }}
									</span>
									@else
									<span class="apenas apenas-single">
										Por <br> apenas 
									</span>
									<span class="valor">R$ {{ __moeda($p->valor_delivery) }}</span>
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


@endsection
@section('js')
<script src="/food-files/js/main.js"></script>
@endsection