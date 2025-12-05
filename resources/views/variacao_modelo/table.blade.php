@if(isset($sub) && $sub != null)

@foreach($item->itens as $key1 => $i)
@foreach($sub->itens as $key2 => $s)
<tr class="dynamic-form">
	<input type="hidden" class="form-control" name="variacao_modelo_item_id[]" value="{{ $i->id }}" required readonly>
	<td>

		<input style="width: 150px;" type="text" class="form-control" name="descricao_variacao[]" value="{{ $i->nome }} {{ $s->nome }}" required readonly>
	</td>
	<td>
		<input style="width: 100px;" type="tel" class="form-control moeda" name="valor_venda_variacao[]" value="" required>
	</td>

	<td>
		<div class="input-group input-group-merge" style="width: 200px;">
			<input type="tel" name="codigo_barras_variacao[]" class="form-control ignore">
			<div class="input-group-text gerar-codigo">
				<span class="ri-barcode-box-line"></span>
			</div>
		</div>
	</td>
	<td>
		<input style="width: 100px;" type="text" class="form-control ignore" name="referencia_variacao[]" value="">
	</td>
	<td>
		<input style="width: 100px;" type="text" class="form-control ignore qtd" name="estoque_variacao[]" value="">
	</td>
	<td>
		<input style="width: 250px;" class="ignore" accept="image/*" type="file" class="form-control" name="imagem_variacao[]" value="">
	</td>
	<td>
		<button type="button" class="btn btn-sm btn-danger btn-remove-tr-variacao">
			<i class="ri-subtract-line"></i>
		</button>
	</td>
</tr>
@endforeach
@endforeach

@else
@foreach($item->itens as $i)
<tr class="dynamic-form">
	<td>
		<input type="hidden" class="form-control" name="variacao_modelo_item_id[]" value="{{ $i->id }}" required readonly>
		<input style="width: 150px;" type="text" class="form-control" name="descricao_variacao[]" value="{{ $i->nome }}" required readonly>
	</td>
	<td>
		<input style="width: 100px;" type="tel" class="form-control moeda" name="valor_venda_variacao[]" value="">
	</td>

	<td>

		<div class="input-group input-group-merge" style="width: 200px;">
			<input type="tel" name="codigo_barras_variacao[]" class="form-control ignore">
			<div class="input-group-text gerar-codigo">
				<span class="ri-barcode-box-line"></span>
			</div>
		</div>
	</td>
	<td>
		<input style="width: 100px;" type="text" class="form-control ignore" name="referencia_variacao[]" value="">
	</td>
	<td>
		<input style="width: 100px;" type="text" class="form-control ignore qtd" name="estoque_variacao[]" value="">
	</td>
	<td>
		<input style="width: 250px;" class="ignore" accept="image/*" type="file" class="form-control" name="imagem_variacao[]" value="">
	</td>
	<td>
		<button type="button" class="btn btn-sm btn-danger btn-remove-tr-variacao">
			<i class="ri-subtract-line"></i>
		</button>
	</td>
</tr>
@endforeach
@endif