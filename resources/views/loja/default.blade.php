<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>{{$title}} - {{ $config->nome }}</title>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
	<link href="/assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
	
	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="/ecommerce/css/bootstrap.min.css"/>

	<!-- Slick -->
	<link type="text/css" rel="stylesheet" href="/ecommerce/css/slick.css"/>
	<link type="text/css" rel="stylesheet" href="/ecommerce/css/slick-theme.css"/>
	
	<!-- nouislider -->
	<link type="text/css" rel="stylesheet" href="/ecommerce/css/nouislider.min.css"/>

	<!-- Font Awesome Icon -->
	<link rel="stylesheet" href="/ecommerce/css/font-awesome.min.css">

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="/ecommerce/css/style.css"/>
	<link rel="stylesheet" type="text/css" href="/assets/css/toastr.min.css">

	<link type="text/css" rel="stylesheet" href="/css/ecommerce_extend2.css"/>

	@yield('css')

	<style type="text/css">
		body.loading {
			overflow: hidden;
		}

		body.loading .modal-loading {
			display: block;
		}

		.modal-loading {
			display: none;
			position: fixed;
			z-index: 10000;
			top: 0;
			left: 0;
			height: 100%;
			width: 100%;
			background: rgba(255, 255, 255, 0.8)
			url("/loading.gif") 50% 50% no-repeat;
		}

		.dropdown-toggle:hover{
			cursor: pointer;
		}

		.inp-pesquisa{
			width: 40% !important;
		}

		@media only screen and (max-device-width: 640px) {
			.inp-categorias{
				display: none;
			}
			.inp-pesquisa{
				width: 60% !important;
			}
		}
	</style>

	@if($config->cor_principal)
	<style type="text/css">
		:root {
			--color-main: {{ $config->cor_principal }}
		}
	</style>
	<link type="text/css" rel="stylesheet" href="/css/ecommerce_extend.css"/>

	@endif

</head>
<body>
	<!-- HEADER -->
	<header>
		<!-- TOP HEADER -->
		<div id="top-header">
			<div class="container">
				<!-- <ul class="header-links pull-left">
					<li><a href="tel:{{ $config->telefone }}"><i class="fa fa-phone"></i> {{ $config->telefone }}</a></li>
					<li><a href="mailto:{{ $config->email }}"><i class="fa fa-envelope-o"></i> {{ $config->email }}</a></li>
					<li><a href="#"><i class="fa fa-map-marker"></i> {{ $config->endereco }}</a></li>
				</ul> -->
				<ul class="header-links pull-right">

					<li><a href="{{ route('loja.minha-conta', ['link='.$config->loja_id]) }}"><i class="fa fa-user-o"></i> Minha conta</a></li>
				</ul>
			</div>
		</div>
		<!-- /TOP HEADER -->

		<!-- MAIN HEADER -->
		<div id="header">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<!-- LOGO -->
					<div class="col-md-3">
						<div class="header-logo">
							<a href="{{ route('loja.index', ['link='.$config->loja_id]) }}" class="logo">
								<img src="{{ $config->logo_img }}" alt="Logo" width="60">
							</a>
						</div>
					</div>
					<!-- /LOGO -->

					<!-- SEARCH BAR -->
					<div class="col-md-6">
						<div class="header-search">
							<form method="get" action="{{ route('loja.pesquisa') }}">
								<input type="hidden" value="{{ $config->loja_id }}" name="link">
								<select name="categoria" class="input-select inp-categorias">
									<option value="">Categorias</option>
									@foreach($categorias as $c)
									@if($c->hash_ecommerce)
									<option @isset($categoria_pesquisa) @if($categoria_pesquisa == $c->hash_ecommerce) selected @endif @endif value="{{ $c->hash_ecommerce }}">{{ $c->nome }}</option>
									@endif
									@endforeach
								</select>
								<input class="input inp-pesquisa" placeholder="Digite aqui" name="pesquisa" @isset($pesquisa) value="{{ $pesquisa }}"  @endif>
								<button class="search-btn">Procurar</button>
							</form>
						</div>
					</div>
					<!-- /SEARCH BAR -->

					<!-- ACCOUNT -->
					<div class="col-md-3 clearfix">
						<div class="header-ctn">

							<!-- Cart -->
							@isset($carrinho)
							<div class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									<i class="fa fa-shopping-cart"></i>
									<span>Carrinho</span>
									<div class="qty">{{ $carrinho != [] ? sizeof($carrinho->itens) : 0 }}</div>
								</a>
								@if($carrinho != [])
								<div class="cart-dropdown">
									<div class="cart-list">
										@foreach($carrinho->itens as $i)
										<div class="product-widget">
											<div class="product-img">
												<img src="{{ $i->produto->img }}" alt="">
											</div>
											<div class="product-body">
												<h3 class="product-name"><a href="#">{{ $i->produto->nome }} {{ $i->variacao ? $i->variacao->descricao : '' }}</a></h3>
												<h4 class="product-price"><span class="qty">
													{{ number_format($i->quantidade, 0) }}x</span>R${{ __moeda($i->valor_unitario) }}
												</h4>
												<h3 class="product-price">subtotal<span class="qty">
													R${{ __moeda($i->sub_total) }}</span>
												</h3>
											</div>

										</div>
										@endforeach

									</div>
									<div class="cart-summary">
										<small>{{ sizeof($carrinho->itens) }} Item(s) selecionados</small>
										<h5>SUBTOTAL: R$ {{ __moeda($carrinho->valor_total) }}</h5>
									</div>
									<div class="cart-btns">
										<a href="{{ route('loja.carrinho', ['link='.$config->loja_id]) }}">Visualizar</a>
										<a class="finalizar" href="{{ route('loja.pagamento', ['link='.$config->loja_id]) }}">Finalizar  <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
								@endif

							</div>
							@endif
							<!-- /Cart -->

							<!-- Menu Toogle -->
							<div class="menu-toggle">
								<a href="#">
									<i class="fa fa-bars"></i>
									<span>Menu</span>
								</a>
							</div>
							<!-- /Menu Toogle -->
						</div>
					</div>
					<!-- /ACCOUNT -->
				</div>
				<!-- row -->
			</div>
			<!-- container -->
		</div>
		<!-- /MAIN HEADER -->
	</header>
	<!-- /HEADER -->

	@yield('content')


	<!-- NEWSLETTER -->
	<div id="newsletter" class="section">
		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">
				<div class="col-md-12">
					<div class="newsletter">

						<ul class="newsletter-follow">
							@if($config->link_facebook)
							<li>
								<a target="_blank" href="{{ $config->link_facebook }}"><i class="fa fa-facebook"></i></a>
							</li>
							@endif
							@if($config->link_instagram)
							<li>
								<a target="_blank" href="{{ $config->link_instagram }}"><i class="fa fa-instagram"></i></a>
							</li>
							@endif
							@if($config->link_whatsapp)
							<li>
								<a target="_blank" href="{{ $config->link_whatsapp }}"><i class="fa fa-whatsapp"></i></a>
							</li>
							@endif
						</ul>
					</div>
				</div>
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</div>
	<!-- /NEWSLETTER -->

	<!-- FOOTER -->
	<footer id="footer">
		<!-- top footer -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<div class="col-md-4 col-xs-6">
						<div class="footer">
							<h3 class="footer-title">Sobre nós</h3>
							@if($config->descricao_breve)
							<p>{{ $config->descricao_breve }}</p>
							@endif
							<ul class="footer-links">
								<li><a href="#"><i class="fa fa-map-marker"></i>{{ $config->endereco }}</a></li>
								<li><a href="tel:{{ $config->telefone }}"><i class="fa fa-phone"></i>{{ $config->telefone }}</a></li>
								<li><a href="mailto:{{ $config->email }}"><i class="fa fa-envelope-o"></i>{{ $config->email }}</a></li>
							</ul>
						</div>
					</div>

					<div class="col-md-4 col-xs-6">
						<div class="footer">
							<h3 class="footer-title">Categorias</h3>
							<ul class="footer-links">
								@foreach($categorias as $c)
								@if($c->hash_ecommerce)
								<li><a href="{{ route('loja.produtos-categoria', [$c->hash_ecommerce, 'link='.$config->loja_id]) }}">{{ $c->nome }}</a></li>
								@endif
								@endforeach
							</ul>
						</div>
					</div>

					<div class="clearfix visible-xs"></div>

					<div class="col-md-4 col-xs-6">
						<div class="footer">
							<h3 class="footer-title">Informação</h3>
							<ul class="footer-links">
								<li><a href="{{ route('loja.minha-conta', ['link='.$config->loja_id]) }}">Minha conta</a></li>
								@if($config->politica_privacidade)
								<li><a href="{{ route('loja.politica-privacidade', ['link='.$config->loja_id]) }}">Politica de privacidade</a></li>
								@endif
							</ul>
						</div>
					</div>


				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /top footer -->

		<!-- bottom footer -->

		<div class="control-loading">
			<div class="modal-loading loading-class"></div>
		</div>
		<div id="bottom-footer" class="section">
			<div class="container">
				<!-- row -->
				<div class="row">
					<div class="col-md-12 text-center">
						<ul class="footer-payments">
							<li><a><i class="fa fa-cc-visa"></i></a></li>
							<li><a><i class="fa fa-credit-card"></i></a></li>
							<li><a><i class="fa fa-cc-paypal"></i></a></li>
							<li><a><i class="fa fa-cc-mastercard"></i></a></li>
							<li><a><i class="fa fa-cc-discover"></i></a></li>
							<li><a><i class="fa fa-cc-amex"></i></a></li>
						</ul>
						<span class="copyright">
							<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
							Copyright &copy;<script>document.write(new Date().getFullYear());</script> by <a>{{ $config->nome }}</a>
							<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
						</span>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /bottom footer -->
	</footer>
	<!-- /FOOTER -->

	<!-- jQuery Plugins -->
	<script src="/ecommerce/js/jquery.min.js"></script>
	<script src="/ecommerce/js/bootstrap.min.js"></script>
	<script src="/ecommerce/js/slick.min.js"></script>
	<script src="/ecommerce/js/nouislider.min.js"></script>
	<script src="/assets/js/toastr.min.js"></script>
	<script src="/ecommerce/js/jquery.zoom.min.js"></script>
	<script src="/ecommerce/js/main.js"></script>
	<script src="/assets/vendor/jquery-mask-plugin/jquery.mask.min.js"></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>
	

	<script type="text/javascript">
		@if(session()->has('flash_success'))
		toastr.success('{{ session()->get('flash_success') }}');
		@endif

		@if(session()->has('flash_error'))
		toastr.error('{{ session()->get('flash_error') }}');
		@endif

		let prot = window.location.protocol;
		let host = window.location.host;
		const path_url = prot + "//" + host + "/";
		$body = $("body");
		$(document).on({
			ajaxStart: function () {
				$body.addClass("loading");
			},
			ajaxStop: function () {
				$body.removeClass("loading");
			}
		});

		var cpfMascara = function (val) {
			return val.replace(/\D/g, "").length > 11
			? "00.000.000/0000-00"
			: "000.000.000-009";
		},
		cpfOptions = {
			onKeyPress: function (val, e, field, options) {
				field.mask(cpfMascara.apply({}, arguments), options);
			},
		};
		$(document).on("focus", ".cpf_cnpj", function () {
			$(this).mask(cpfMascara, cpfOptions);
		});
		function convertMoedaToFloat(value) {
			if (!value) {
				return 0;
			}

			var number_without_mask = value.replaceAll(".", "").replaceAll(",", ".");
			return parseFloat(number_without_mask.replace(/[^0-9\.]+/g, ""));
		}

		function convertFloatToMoeda(value) {
			value = parseFloat(value)
			return value.toLocaleString("pt-BR", {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});
		}
	</script>
	@yield('js')

</body>
</html>
