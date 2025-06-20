<tr>
	<td>
		<input readonly type="text" name="nome[]" class="form-control"
        value="{{ $nome }}">
		<input readonly type="hidden" name="produto_id[]" class="form-control" value="{{$servico->id}}">
	</td>
	<td>
		<input readonly type="tel" name="quantidade[]" class="form-control qtd-item" value="{{ $qtd }}">
	</td>
	<td>
		<input readonly type="tel" name="total[]" class="form-control" value="{{ __moeda($valor) }}">
	</td>
    <td>
		<input readonly type="tel" name="subtotal[]" class="form-control" value="{{ __moeda($valor) }}">
	</td>
	<td>
		<button class="btn btn-sm btn-danger btn-delete-row">
			<i class="bx bx-trash"></i>
		</button>
	</td>
</tr>
