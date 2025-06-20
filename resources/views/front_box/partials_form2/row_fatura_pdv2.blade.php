@foreach($data as $item)
<tr class="dynamic-form">
    <td width="300">
        <select name="tipo_pagamento_row[]" class="form-control tipo_pagamento select2">
            <option value="">Selecione..</option>
            @foreach($tiposPagamento as $key => $c)
            <option @if($key == $tipo_pagamento) selected @endif value="{{$key}}">{{$c}}</option>
            @endforeach
        </select>
    </td>
    <td width="150">
        <input type="date" class="form-control data_vencimento" name="data_vencimento_row[]" value="{{ $item['vencimento'] }}">
    </td>
    <td width="150">
        <input type="tel" class="form-control moeda valor_integral_row" name="valor_integral_row[]" value="{{ __moeda($item['valor']) }}">
    </td>
    <td>
        <input type="text" name="obs_row[]" class="form-control ignore" value="">
    </td>
    <td width="30">
        <button class="btn btn-sm btn-danger btn-remove-tr">
            <i class="ri-delete-back-2-line"></i>
        </button>
    </td>
</tr>
@endforeach