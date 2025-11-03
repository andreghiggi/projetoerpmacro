@if(isset($sub) && $sub != null)

@foreach($item->itens as $index => $i)
@foreach($sub->itens as $s)
<tr class="dynamic-form">
	<td>
		<input type="text" class="form-control" name="descricao_variacao[]" value="{{ $i->nome }} {{ $s->nome }}" required readonly>
	</td>
	<td>
		<input type="tel" class="form-control moeda" name="valor_venda_variacao[]" value="" required>
	</td>

	<td>
		<input type="tel" class="form-control ignore" name="codigo_barras_variacao[]" value="">
	</td>
	<td>
		<input type="text" class="form-control ignore" name="referencia_variacao[]" value="">
	</td>
	<td>
        <input type="number" step="1" min="0" class="form-control ignore" name="estoque_variacao[]" value="">

{{--        <input type="text" class="form-control ignore qtd" name="estoque_variacao[]" value="">--}}
	</td>
	<td>

	@if( $item->imagens() )
	@foreach($item->imagens() as $index => $imagem) 

	<div id="image_variacao_frame_{{ $index }}_0" class="card form-input" style="max-width: 100%; width: 150px; margin: 0;">
        <div class="preview" style="width: 100%; text-align: center;">
            <button type="button" id="image_variacao_remove_{{ $index }}_0" class="btn btn-link-danger btn-sm btn-danger">x</button>
            <button type="button" id="image_variacao_add_{{ $index }}_0" class="btn btn-link-primary btn-sm btn-primary">+</button>
            <img id="image_variacao_preview_{{ $index }}_0" src="/imgs/no-image.png" style="max-width: 100%; width: 100%; height: auto; display: block;">
        </div>
        <label id="image_variacao_input_label_{{ $index }}_0" for="image_variacao_input_{{ $index }}_0" style="text-align: center; display: block; margin: 5px 0;">Imagem</label>
        <input type="file" id="image_variacao_input{{ $index }}_0" name="image_variacao_list[{{ $index }}][]" accept="image/*">
    </div>

	@endforeach
	@endif

		<!-- <input type="file" name="imagem_variacao[{{ $index }}][]" accept="image/*" onchange="showPreview(event);"> -->
		<!-- <input class="ignore" accept="image/*" type="file" class="form-control" name="imagem_variacao[]" value=""> -->


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
@foreach($item->itens as $index => $i)
<tr class="dynamic-form">
	<td>
		<input type="text" class="form-control" name="descricao_variacao[]" value="{{ $i->nome }}" required readonly>
	</td>
	<td>
		<input type="tel" class="form-control moeda" name="valor_venda_variacao[]" value="">
	</td>

	<td>
		<input type="tel" class="form-control ignore" name="codigo_barras_variacao[]" value="">
	</td>
	<td>
		<input type="text" class="form-control ignore" name="referencia_variacao[]" value="">
	</td>
	<td>
        <input type="number" step="1" min="0" class="form-control" name="estoque_variacao[]" value="">
{{--		<input type="text" class="form-control ignore qtd" name="estoque_variacao[]" value="">--}}
	</td>
	<td>

    <div id="image_variacao_frame_{{ $index }}_0" class="card form-input" style="max-width: 100%; width: 150px; margin: 0;">
        <div class="preview" style="width: 100%; text-align: center;">
            <button type="button" id="image_variacao_remove_{{ $index }}_0" class="btn btn-link-danger btn-sm btn-danger">x</button>
            <button type="button" id="image_variacao_add_{{ $index }}_0" class="btn btn-link-primary btn-sm btn-primary">+</button>
            <img id="image_variacao_preview_{{ $index }}_0" src="/imgs/no-image.png" style="max-width: 100%; width: 100%; height: auto; display: block;">
        </div>
        <label id="image_variacao_input_label_{{ $index }}_0" for="image_variacao_input_{{ $index }}_0" style="text-align: center; display: block; margin: 5px 0;">Imagem</label>
        <input type="file" id="image_variacao_input_{{ $index }}_0" name="image_variacao[{{ $index }}][]" accept="image/*">
    </div>

		<!-- <div id="imagem-frame" class="card mt-3 form-input" style="max-width: 100%;">
			<div class="preview">
				<button type="button" id="btn-remove-imagem" data-index="0" class="btn btn-link-danger btn-sm btn-danger">x</button>
				<button type="button" id="btn-add-imagem" data-index="0" class="btn btn-link-primary btn-sm btn-primary">+</button>
				@isset($item)
				<img id="image_preview" src="{{ $item->img }}" style="max-width: 100%; height: auto;">
				@else
				<img id="image_preview" src="/imgs/no-image.png" style="max-width: 100%; height: auto;">
				@endif
			</div>
			<label for="file-ip-1">Imagem</label>
			<input type="file" id="imagem_input" name="image[]" data-index="0" accept="image/*" onchange="showPreview(event);">
		</div> -->
	</td>
	<!-- <td>
		<button type="button" class="btn btn-sm btn-danger btn-remove-tr-variacao">
			<i class="ri-subtract-line"></i>
		</button>
	</td> -->
</tr>
@endforeach
@endif

<script>
	$('input[id^=image_variacao_input_]').on('change', image_variacao_input_on_change);
</script>