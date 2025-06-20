<div class="row">
	<div class="col-12">
		{{ $pergunta->texto }}
	</div>

	<div class="col-md-2 col-6 mt-3">
		<a class="btn btn-dark" href="{{ route('mercado-livre-perguntas.show', [$pergunta->id]) }}">Responder pergunta</a>
	</div>
</div>