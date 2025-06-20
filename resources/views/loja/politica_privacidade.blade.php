@extends('loja.default', ['title' => 'Politica e privacidade'])

@section('content')

<div class="section">
	<div class="container">
		<div class="row">

			{!! $config->politica_privacidade !!}
		</div>
	</div>
</div>
@endsection

