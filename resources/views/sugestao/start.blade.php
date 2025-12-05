@extends('layouts.app', ['title' => 'Autenticação'])
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

<div class="mt-3">
	<div class="card card-custom gutter-b example example-compact">
		<div class="m-2">
			<div class="col-lg-12">
				<div class="col-lg-12">
					<p class="text-muted">Solicite o token com o desenvolvedor (43) 9200004769</p>
				</div>
				
				<form class="" method="post" action="{{ route('sugestao.auth') }}">
					@csrf
					<div class="row">
						<div class="col-md-6">
							{!!Form::text('token', 'TOKEN')->attrs(['class' => ''])->required()
							!!}
						</div>
						<div class="col-md-2">
							<br>
							<button class="btn btn-dark">Gravar</button>
						</div>
					</div>
				</form>
				
			</div>
		</div>
	</div>
</div>

@endsection
