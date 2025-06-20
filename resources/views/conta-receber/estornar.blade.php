@extends('layouts.app', ['title' => 'Estornar Conta Receber'])

@section('content')

<div class="card mt-1">
	<div class="card-header">
		<h3>Estornar Conta Receber</h3>
		<div style="text-align: right;" class="">
			<a href="{{ route('conta-receber.index') }}" class="btn btn-danger btn-sm px-3">
				<i class="ri-arrow-left-double-fill"></i>Voltar
			</a>
		</div>
	</div>
	<div class="card-body">
		{!!Form::open()->fill($item)
		->put()
		->route('conta-receber.estornar-update', [$item->id])
		->multipart()
		!!}
		<div class="pl-lg-4">

			<div class="row g-2">
				<div class="col-md-4">
					{!!Form::text('cliente', 'Cliente')->attrs(['class' => 'moeda'])->value(isset($item->cliente) ? $item->cliente->info : '')->readonly()
					!!}
				</div>
				<div class="col-md-2">
					{!!Form::text('valor_integral', 'Valor Integral')->attrs(['class' => 'moeda'])->value(isset($item) ? __moeda($item->valor_integral) : '')->readonly()
					!!}
				</div>
				<div class="col-md-2">
					{!!Form::text('data_vencimento', 'Data Vencimento')->readonly()
					->value(__data_pt($item->data_vencimento, 0))
					!!}
				</div>
				<div class="col-md-2">
					{!!Form::text('data_recebimento', 'Data Recebimento')->readonly()
					->value(__data_pt($item->data_recebimento, 0))
					!!}
				</div>

				<div class="col-md-12">
					{!!Form::text('motivo_estorno', 'Motivo do estorno')->required()
					!!}
				</div>

				<hr class="mt-4">
				<div class="col-12" style="text-align: right;">
					<button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
				</div>
			</div>

		</div>
		{!!Form::close()!!}
	</div>
</div>
@endsection
