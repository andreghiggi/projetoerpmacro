@extends('layouts.app', ['title' => 'Sugestão #' . $item->id])
@section('content')

<div class="card mt-1">
	<div class="card-header">
		<h4>Sugestão <strong class="text-success">#{{ $item->id }}</strong></h4>
		<div style="text-align: right; margin-top: -35px;">
			<a href="{{ route('sugestao.index') }}" class="btn btn-danger btn-sm px-3">
				<i class="ri-arrow-left-double-fill"></i>Voltar
			</a>
		</div>
	</div>

	<div class="card-body">

		<div class="row">
			<h3 class="text-dark">{{ $item->titulo }}</h3>
			<h5 class="col-md-6">Criado por: <strong>{{ $item->empresa->nome }} - {{ __data_pt($item->created_at) }}</strong></h5>
			<h5 class="col-md-6">Data de atualização: <strong>{{ __data_pt($item->updated_at) }}</strong></h5>

			<div class="col-md-4">
				@if($item->estado == 'nova')
				<h4><span class="badge bg-primary">NOVO</span></h4>
				@elseif($item->estado == 'recusada')
				<h4><span class="badge bg-danger">RECUSADA</span></h4>
				@elseif($item->estado == 'aprovada')
				<h4><span class="badge bg-success">APROVADA</span></h4>
				@elseif($item->estado == 'em_desevolvimento')
				<h4><span class="badge bg-info">EM DESENVOLVIMENTO</span></h4>
				@else
				<h4><span class="badge bg-dark">CONCLUÍDA</span></h4>
				@endif
			</div>

			<div class="col-md-4">
				@if($item->empresa_id == $item->empresa_logada)
				<i class="ri-thumb-up-line"></i> {{ $item->total_votacoes }} Curtidas
				@else
				@if($item->curtida)
				<i class="ri-thumb-up-line"></i> {{ $item->total_votacoes }} Curtidas
				@else
				<a href="{{ route('sugestao.like', [$item->id]) }}"> <i class="ri-thumb-up-line"></i> {{ $item->total_votacoes }} Curtir</a>
				@endif
				@endif
			</div>
			<br>
			<hr>

			{!! $item->texto !!}
		</div>

		<form class="col-md-12" method="post" action="{{ route('sugestao.response', [$item->id]) }}">
			@csrf
			<div class="col-md-12">
				{!!Form::textarea('texto', 'Resposta ou complemento da ideia')
				->attrs(['rows' => '10', 'class' => 'tiny'])
				!!}
			</div>
			<br>
			<div class="col-12" style="text-align: right;">
				<button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
			</div>
		</form>

		@foreach($item->comentarios as $c)
		<div class="row">
			<div class="card">
				<div class="card-header">
					<h5 class="text-primary">{{ $c->empresa ? $c->empresa->nome : 'Slym' }}</h5>
					<p class="text-muted">{{ __data_pt($c->created_at) }}</p>
				</div>
				<div class="card-body">
					{!! $c->texto !!}
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>
@endsection

@section('js')
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	$(function(){
		tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

		setTimeout(() => {
			$('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
		}, 1000)
	})

	function openModal(){
		$('#modal-info').modal('show')
	}

</script>
@endsection
