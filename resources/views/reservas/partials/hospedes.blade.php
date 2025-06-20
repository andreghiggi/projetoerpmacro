@foreach($item->hospedes as $key => $hospede)
<div class="row g-2 m-2">
	<div class="col-md-12">
		<h5 class="text-danger">Hóspede {{ $key+1 }}</h5>
	</div>
	<input type="hidden" name="hospede_id[]" value="{{ $hospede->id }}">
	<div class="col-md-4">
		{!!Form::text('nome_completo[]', 'Nome completo')
		->required()
		->value($hospede->nome_completo)
		!!}
	</div>
	<div class="col-md-2">
		{!!Form::tel('cpf[]', 'CPF')
		->required()
		->attrs(['class' => 'cpf'])
		->value($hospede->cpf)
		!!}
	</div>
	<hr>
	<div class="col-md-2 col">
		{!!Form::tel('cep[]', 'CEP')
		->required()
		->attrs(['class' => 'cep'])
		->value($hospede->cep)
		!!}
	</div>
	<div class="col-md-4 col">
		{!!Form::text('rua[]', 'Rua')
		->required()
		->value($hospede->rua)
		!!}
	</div>
	<div class="col-md-2 col">
		{!!Form::text('numero[]', 'Número')
		->required()
		->value($hospede->numero)
		!!}
	</div>
	<div class="col-md-3 col">
		{!!Form::text('bairro[]', 'Bairro')
		->required()
		->value($hospede->bairro)
		!!}
	</div>

	<div class="col-md-3 col">
		{!!Form::select('cidade_id[]', 'Cidade')
		->required()
		->id('cidade_'.$key)
		->attrs(['class' => 'cidade'])
		->options([$hospede->cidade_id => $hospede->cidade->info])
		!!}
	</div>

	<div class="col-md-2">
		{!!Form::tel('telefone[]', 'Telefone')
		->required()
		->attrs(['class' => 'fone'])
		->value($hospede->telefone)
		!!}
	</div>

	<div class="col-md-3">
		{!!Form::text('email[]', 'Email')
		->type('email')
		->value($hospede->email)
		!!}
	</div>
</div>
<hr>
@endforeach