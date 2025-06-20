<tr class="tr_{{ $request->key }}">
	<td>{{ $request->dimensao_valor_unitario_m2 }}</td>
	<td>{{ $request->dimensao_largura }}</td>
	<td>{{ $request->dimensao_altura }}</td>
	<td>{{ $request->dimensao_quantidade }}</td>
	<td>{{ $request->dimensao_m2_total }}</td>
	<td>{{ $request->dimensao_espessura }}</td>
	<td class="sub_total">{{ $request->dimensao_sub_total }}</td>
	<td>{{ $request->dimensao_observacao }}</td>
	<td>
		<button data-key="{{ $request->key }}" type="button" class="btn btn-danger btn-remove-tr-dimensao btn-sm">
			<i class="ri-delete-bin-line"></i>
		</button>
	</td>
</tr>