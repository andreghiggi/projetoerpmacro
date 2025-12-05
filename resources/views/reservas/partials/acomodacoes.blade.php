<div class="row g-1">

	<div class="col-md-4">
		{!!Form::select('acomodacao_id', 'Acomodação', ['' => 'Selecione'] + $data->pluck('info', 'id')->all())
		->required()
		->attrs(['class' => 'select2'])
		!!}
	</div>

	<div class="col-md-5">
		<label>Cliente</label>
		<div class="input-group flex-nowrap">
			<select required id="inp-cliente_id" name="cliente_id" class="cliente_id">

			</select>

			<button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modal_novo_cliente" type="button">
				<i class="ri-add-circle-fill"></i>
			</button>

		</div>
	</div>

	<div class="col-md-2">
		{!!Form::tel('valor_estadia', 'Valor da estadia')
		->attrs(['class' => 'moeda'])
		->readonly(1)
		->required()
		!!}
	</div>

	<div class="col-md-12">
		{!!Form::textarea('observacao', 'Observação')
		->attrs(['rows' => '5'])
		!!}
	</div>
</div>