@foreach($data as $item)
<tr>
    <td>
        <input readonly type="text" name="nome_pagamento[]" class="form-control"
        value="{{ \App\Models\Nfce::getTipoPagamento($tipo_pagamento) }}">
        <input readonly type="hidden" name="tipo_pagamento_row[]" class="form-control"
        value="{{ $tipo_pagamento }}">
    </td>
	<td>
		<input readonly type="date" name="data_vencimento_row[]" class="form-control data_multiplo"
        value="{{ $item['vencimento'] }}">
	</td>
	<td>
		<input  type="text" name="valor_integral_row[]" class="form-control valor_integral"
        value="{{ __moeda($item['valor']) }}">
	</td>
    <td>
		<input type="text" name="obs_row[]" class="form-control"
        value="">
	</td>
	<td>
		<button class="btn btn-sm btn-danger btn-delete-row">
			<i class="ri-delete-back-2-line"></i>
		</button>
	</td>
</tr>
@endforeach
