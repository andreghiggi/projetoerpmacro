@foreach($data as $item)
<tr>
    <td>
        <input type="checkbox" class="checkbox" value="{{$item->id}}" name="">
    </td>
    <td>{{ __data_pt($item->created_at, 1) }}</td>
    <td>{{ $item->fornecedor ? $item->fornecedor->info : '' }}</td>
    <td>{{ __moeda($item->total) }}</td>
    <td>
        {{ $item->chave ? $item->chave : $item->chave_importada }}
        @if($item->chave_importada)
        <b class="text-danger">Importada</b>
        @endif
    </td>
    <td>{{ $item->numero }}</td>
</tr>
@endforeach
