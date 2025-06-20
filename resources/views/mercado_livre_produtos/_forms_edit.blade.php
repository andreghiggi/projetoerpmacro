<div class="row g-2">

	<div class="col-md-4">
		{!!Form::text('nome', 'Nome')
		->required()
		->value($item->nome)
		!!}
	</div>

	<div class="col-md-4">
		{!!Form::select('mercado_livre_categoria', 'Categoria do anúcio')
		->attrs(['class' => 'form-select select2 input-ml'])
		->options((isset($item) && $item->mercado_livre_categoria) ? 
		[$item->mercado_livre_categoria => $item->categoriaMercadoLivre->nome] : [])
		!!}
	</div>

	@if(sizeof($prodML->variations) == 0)
	<div class="col-md-2">
		{!!Form::tel('mercado_livre_valor', 'Valor do anúncio')
		->value((isset($item) && $item->mercado_livre_valor > 0) ? __moeda($item->mercado_livre_valor) : '')
		->attrs(['class' => 'moeda input-ml'])
		!!}
	</div>
	@endif

	<input type="hidden" id="tipo_publicacao_hidden" value="{{ isset($item) ? $item->mercado_livre_tipo_publicacao : '' }}">

	<div class="col-md-6">
		{!!Form::text('mercado_livre_youtube', 'Link do youtube')
		->attrs(['class' => ''])
		!!}
	</div>

	<div class="col-md-12">
		{!!Form::textarea('mercado_livre_descricao', 'Descrição')
		->attrs(['rows' => '12'])
		->value($descricaoML ? $descricaoML->plain_text : '')
		!!}
	</div>

	@if(sizeof($prodML->variations) > 0)
	<h4>Variações do produto</h4>
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>Tipo</th>
					<th>Variação</th>
					<th>Valor</th>
					<th>Quantidade</th>
				</tr>
			</thead>
			<tbody>
				@foreach($prodML->variations as $v)
				<tr>
					<td>
						<input readonly class="form-control" type="" value="{{ $v->attribute_combinations[0]->name }}" name="variacao_nome[]">
					</td>
					<td>
						<input readonly class="form-control" type="" value="{{ $v->attribute_combinations[0]->value_name }}" name="variacao_valor_nome[]">
					</td>
					<td>
						<input class="form-control moeda" type="tel" value="{{ __moeda($v->price) }}" name="variacao_valor[]">
					</td>

					<td>
						<input class="form-control " type="tel" value="{{ ($v->available_quantity) }}" name="variacao_quantidade[]">
					</td>

					<input type="hidden" value="{{ $v->id }}" name="variacao_id[]">

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif

	<div class="col-12" style="text-align: right;">
		<button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
	</div>
</div>