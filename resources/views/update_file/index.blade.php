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
	<div class="card">
		<div class="m-3">

			<form method="post" action="{{ route('update-file.store') }}" enctype="multipart/form-data">
				@csrf

				<div class="row">
					<div style="text-align: right; margin-top: 15px;">
						<a href="{{ route('update-file.log') }}" class="btn btn-dark btn-sm px-3">
							<i class="ri-list-settings-fill"></i> Histórico de Atualizações
						</a>
						<a href="{{ route('clear') }}" class="btn btn-danger btn-sm px-3">
							<i class="ri-refresh-fill"></i> Limpar cache servidor
						</a>
					</div>
					@if($update)
					<h5>Versão atual: <strong>{{ $update->versao }} - {{ __data_pt($update->updated_at) }}</strong></h5>
					@else
					Versão atual: --
					@endif

					@if(!function_exists('shell_exec'))
					<p class="text-warning">
						<i class="ri-error-warning-line"></i> Ative a extensão shell_exec no seu servidor
					</p>
					@else
					<p class="text-danger ">Atenção realize o backup antes de atualizar os diretórios!</p>
					<div class="form-group validated col-lg-4">
						<div class="">
							<span style="width: 100%" class="btn btn-primary btn-file">
								<i class="ri-search-line"></i>
								Procurar arquivo ZIP<input required accept=".zip" name="file" type="file">
							</span>
							<label class="text-info" id="filename"></label>
						</div>
					</div>
					<div class="form-group validated col-lg-2">
						<div class="">
							<button class="btn btn-danger btn-execute">Executar</button>
						</div>
					</div>
					@endif

				</div>
			</form>

		</div>
	</div>
</div>

@endsection

@section('js')
<script type="text/javascript">
	$('.btn-execute').click(() => {
		$body = $("body");
		$body.addClass("loading");
	})
</script>
@endsection