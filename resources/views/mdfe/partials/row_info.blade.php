<tr>
	<td>
        <input type="hidden" name="tp_und_transp_row[]" class="form-control" value="{{ $tp_und_transp }}">
        <input style="width: 180px" readonly type="" class="form-control" value="{{ \App\Models\Mdfe::tiposUnidadeTransporte()[$tp_und_transp] }}">
    </td>   
    <td>
        <input readonly style="width: 150px" type="text" name="id_und_transp_row[]" class="form-control" value="{{ $id_und_transp }}">
    </td>
    <td>
        <input readonly style="width: 150px" type="tel" name="quantidade_rateio_row[]" class="form-control"
        value="{{ $quantidade_rateio }}">
    </td>
    <td>
        <input readonly style="width: 150px" type="tel" name="quantidade_rateio_carga_row[]" class="form-control"
        value="{{ $quantidade_rateio_carga }}">
    </td>
    <td>
        <input readonly type="tel" name="chave_nfe_row[]" class="form-control"
        value="{{ $chave_nfe }}">
        <div style="width: 400px"></div>
    </td>
    <td>
        <input readonly type="tel" name="chave_cte_row[]" class="form-control"
        value="{{ $chave_cte }}">
        <div style="width: 400px"></div>
    </td>
    <td>

        <input style="width: 250px" readonly type="text" class="form-control"
        value="{{ $cidade->info }}">
        <input readonly type="hidden" name="municipio_descarregamento_row[]" class="form-control"
        value="{{ $municipio_descarregamento }}">
    </td>
    <td>
        <input style="width: 150px" readonly type="tel" name="lacres_transporte_row[]" class="form-control"
        value="{{ $lacres_transporte ? json_encode($lacres_transporte) : '' }}">
    </td>
    <td>
        <input style="width: 150px" readonly type="tel" name="lacres_unidade_row[]" class="form-control"
        value="{{ $lacres_unidade ? json_encode($lacres_unidade) : '' }}">
    </td>

    <td>
        <div style="width: 100px">
            <button type="button" class="btn btn-sm btn-warning btn-edit-row">
                <i class="ri-edit-line"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-delete-row">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    </td>
</tr>
