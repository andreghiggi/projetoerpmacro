@extends('food.default', ['title' => 'Identificação'])
@section('content')


@section('css')
<style type="text/css">
	.btn-main{
		border: none;
	}
</style>
@endsection

<div class="container">

	<div id="dvUsuario" hidden="" class="elemento-usuario" style="display: block;">
		<div class="titler">
			<div class="row">
				<div class="col-md-12">
					<div class="title-line mt-0 pd-0">
						<i class="lni lni-user"></i>
						<span>Dados do cliente</span>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" id="inp-empresa_id" value="{{ $config->empresa_id }}">
		<input type="hidden" value="{{ $config->loja_id }}" id="inp-link">
		<input type="hidden" value="{{ $carrinho->id }}" id="inp-carrinho_id">

		<div class="row">
			<div class="col-md-12">
				<div class="form-field-default">
					<label>Whatsapp:</label>
					<input class="maskcel inptWhatsapp" type="text" id="inp-fone" placeholder="Whatsapp:" value="">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-field-default">
					<label>Nome completo:</label>
					<input type="text" class="inptNome" id="inp-nome" placeholder="Nome:" value="">
				</div>
			</div>
		</div>
		
		
		<button class="botao-acao pt-3 btn-main" style="display: none;">PRÓXIMO</button>

	</div>
</div>

<!-- <section class="featured spad" style="margin-top: -100px">
	<div class="container">
		<br>
		<input type="hidden" value="{{ $config->loja_id }}" id="inp-link">
		<input type="hidden" value="{{ $carrinho->id }}" id="inp-carrinho_id">
		<div class="row featured__filter">
			<input type="hidden" id="inp-empresa_id" value="{{ $config->empresa_id }}">
			<p class="col-12 text-main">Digite seu telefone para identificar o cadastro</p>
			<div class="col-md-3 col-12 mt-1">
				<input type="tel" id="inp-fone" name="fone" placeholder="(43) 99999-9999" class="fone form-control">
			</div>
			<div class="col-md-6 col-12 mt-1">
				<input type="text" id="inp-nome" class="form-control" placeholder="Informe seu nome completo" name="">
			</div>
			<div class="col-md-3 col-12 mt-1">
				<button type="button" class="primary-btn btn-main w-100 d-none">Continuar</button>
			</div>
		</div>
	</div>
</section> -->
@section('js')
<script type="text/javascript" src="/delivery/js/auth.js"></script>
@endsection
@endsection