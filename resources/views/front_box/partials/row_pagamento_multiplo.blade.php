<tr>
    <td>
        <input readonly type="text" name="nome_pagamento[]" class="form-control"
        value="{{ $tipo }}">
        <input readonly type="hidden" name="tipo_pagamento_row[]" class="form-control"
        value="{{ $tipo_pagamento_row }}">
    </td>
	<td>
		<input readonly type="date" name="data_vencimento_row[]" class="form-control data_multiplo"
        value="{{ $data_vencimento_row }}">
	</td>
	<td>
		<input readonly type="text" name="valor_integral_row[]" class="form-control valor_integral"
        value="{{ $valor_integral_row }}">
	</td>
    <td>
		<input readonly type="text" name="obs_row[]" class="form-control"
        value="{{ $obs_row }}">
	</td>
	<td>
		<button class="btn btn-sm btn-danger btn-delete-row">
			<i class="ri-delete-back-2-line"></i>
		</button>
	</td>
</tr>
