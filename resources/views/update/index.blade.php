@extends('layouts.app', ['title' => 'Update'])
@section('content')
<style type="text/css">
	.btn-file {
		position: relative;
		overflow: hidden;
	}

	.btn-file input[type=file] {
		position: absolute;
		top: 0;
		right: 0;
		min-width: 100%;
		min-height: 100%;
		font-size: 100px;
		text-align: right;
		filter: alpha(opacity=0);
		opacity: 0;
		outline: none;
		background: white;
		cursor: inherit;
		display: block;
	}
</style>

<div class="mt-1">
	<div class="card card-custom gutter-b example example-compact">
		<div class="container @if(env('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-lg-12">
				<div class="col-lg-12">
					<p class="text-danger ml-4 mt-4">Atenção com os comandos executados nesta tela, pode afetar todo o banco de dados!</p>
				</div>
				
				<form class="container" method="post" action="/update-sql/sql" enctype="multipart/form-data">
					@csrf
					<br>
					<div class="row">
						<div class="form-group validated col-lg-4">
							<div class="">
								<span style="width: 100%" class="btn btn-primary btn-file">
									<i class="ri-search-line"></i>
									Procurar arquivo SQL<input required accept=".sql" name="file" type="file">
								</span>
								<label class="text-info" id="filename"></label>
							</div>
						</div>
						<div class="form-group validated col-lg-2">
							<div class="">
								<button class="btn btn-danger">Executar</button>
							</div>
						</div>
					</div>
				</form>
				<hr class="mt-5">
				<form class="container" method="post" action="/update-sql/run-sql">
					@csrf
					<h5 class="ml-4">Comando Sql</h5>
					<div class="col-12 form-group">
						<textarea name="sql" class="form-control"></textarea>
						<label class="mt-3">separe os comandos com ";"</label>
					</div>
					<button class="btn btn-dark float-right mt-3" type="submit">Executar SQL</button>
					<br>
				</form>
				<br><br>
			</div>
		</div>
	</div>
</div>

@endsection
