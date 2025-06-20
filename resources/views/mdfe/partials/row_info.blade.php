<tr>
	<td>
		<input readonly type="sel" name="tp_und_transp_row[]" class="form-control" value="{{ $tp_und_transp }}">
	</td>   
	<td>
		<input readonly type="text" name="id_und_transp_row[]" class="form-control" value="{{ $id_und_transp }}">
	</td>
    <td>
        <input readonly type="tel" name="quantidade_rateio_row[]" class="form-control"
        value="{{ $quantidade_rateio }}">
    </td>
    <td>
        <input readonly type="tel" name="quantidade_rateio_carga_row[]" class="form-control"
        value="{{ $quantidade_rateio_carga }}">
    </td>
    <td>
        <input readonly type="tel" name="chave_nfe_row[]" class="form-control"
        value="{{ $chave_nfe }}">
    </td>
    <td>
        <input readonly type="tel" name="chave_cte_row[]" class="form-control"
        value="{{ $chave_cte }}">
    </td>
    <td>
        <input readonly type="hidden" name="municipio_descarregamento_row[]" class="form-control"
        value="{{ $municipio_descarregamento }}">

        <input readonly type="text" class="form-control"
        value="{{ $cidade->info }}">
    </td>
    <td>
        <input readonly type="tel" name="lacres_transporte_row[]" class="form-control"
        value="{{ json_encode($lacres_transporte) }}">
    </td>
    <td>
        <input readonly type="tel" name="lacres_unidade_row[]" class="form-control"
        value="{{ json_encode($lacres_unidade) }}">
    </td>
    
    <td>
      <button class="btn btn-sm btn-danger btn-delete-row">
         <i class="ri-delete-bin-line"></i>
     </button>
 </td>
</tr>
