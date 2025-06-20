@foreach ($itens as $item)
<tr>
    <td>
        <input readonly type="text" class="form-control" value="{{ $item->lote }}">
    </td>
    <td>
        <input readonly type="text" class="form-control" value="{{ __data_pt($item->data_vencimento, 0) }}">
    </td>
    <td>
        <input readonly type="tel" class="form-control" value="{{ $item->quantidade }}">
    </td>
</tr>
@endforeach
