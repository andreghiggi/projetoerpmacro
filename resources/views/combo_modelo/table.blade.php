
<tr class="dynamic-form">
	<input type="hidden" name="produto_combo_id[]" value="{{ $item->id }}">
	<td style="width: 420px">
		<span>{{ $item->nome }}</span>
	</td>
	<td style="width: 120px">
		<input type="tel" class="form-control qtd-combo quantidade" name="quantidade_combo[]" value="1">
	</td>
	<td>
		<input type="tel" class="form-control moeda valor-compra-combo" name="valor_compra_combo[]" value="{{ __moeda($item->valor_compra) }}">
	</td>
	<td>
		<input type="tel" class="form-control moeda subtotal-combo" name="subtotal_combo[]" value="{{ __moeda($item->valor_compra) }}">
	</td>
	<td>
		<button type="button" class="btn btn-sm btn-danger btn-remove-tr-combo">
			<i class="ri-subtract-line"></i>
		</button>
	</td>
</tr>
