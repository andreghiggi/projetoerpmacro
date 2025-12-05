@foreach($data as $item)
<tr class="dynamic-form">
    <td width="300">
        <select name="tipo_pagamento[]" class="form-control tipo_pagamento select2">
            <option value="">Selecione..</option>
            @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
            <option @if($item['tipo_pagamento'] == $key) selected @endif value="{{$key}}">{{$c}}</option>
            @endforeach
        </select>
    </td>
    <td width="150">
        <input value="{{ $item['vencimento'] }}" type="date" class="form-control" name="data_vencimento[]">
    </td>
    <td width="150">
        <input value="{{ __moeda($item['valor']) }}" type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]">
    </td>
    <td width="30">
        <button class="btn btn-danger btn-remove-tr">
            <i class="ri-delete-bin-line"></i>
        </button>
    </td>
</tr>
@endforeach