@extends('layouts.app', ['title' => 'Teste email'])
@section('content')
<div class="mt-3">
	<div class="row">
		<div class="card">
			<div class="card-body">
				{!!Form::open()
				->post()
				->route('teste-email-send')
				!!}
				<div class="pl-lg-4">

					<div class="row g-2">

						<div class="col-md-12">
							{!!Form::textarea('texto', 'Texto')
							->attrs(['rows' => '3'])
							->required()
							!!}
						</div>

						<div class="col-md-6">
							{!!Form::text('assunto', 'Assunto')
							->required()
							!!}
						</div>

						<div class="col-md-6">
							{!!Form::text('destinatario', 'Destinatário')
							->type('email')
							->required()
							!!}
						</div>

						<div class="col-12" style="text-align: right;">
							<button type="submit" class="btn btn-success px-5" id="btn-store">Enviar</button>
						</div>
					</div>
				</div>
				{!!Form::close()!!}
			</div>
		</div>
	</div>
</div>
@endsection

