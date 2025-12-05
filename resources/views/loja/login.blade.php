@extends('loja.default', ['title' => 'Cadastro'])
@section('css')

<link rel="stylesheet" type="text/css" href="/css/ecommerce_login.css">
@endsection
@section('content')

<div class="section">
	<!-- container -->
	<div class="container">
		<!-- row -->
		<form class="row" method="post" action="{{ route('loja.login-auth') }}">
			@csrf
			<input type="hidden" name="link" value="{{ $config->loja_id }}">

			<div class="col-md-3"></div>
			<div class="col-md-6">
				<!-- Billing Details -->
				<div class="billing-details row">
					<div class="section-title">
						<h3 class="title">Acesso</h3>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<input required class="input" type="text" name="email" placeholder="Email">
						</div>
					</div>
					
					<div class="col-md-12">
						<div class="form-group">
							<input required class="input" type="password" name="senha" placeholder="Senha">
						</div>
					</div>
				</div>
				
				<button type="submit" class="primary-btn order-submit">Login</button>
				<label style="margin-left: 5px; margin-top: 5px;">
					Ainda não tem cadastro? <a style="color: red" href="{{ route('loja.cadastro', ['link='.$config->loja_id])}}">quero me cadastrar</a>
				</label>
			</div>
		</form>
	</div>
</div>

@endsection
@section('js')

<script type="text/javascript"></script>
@endsection
