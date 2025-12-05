@foreach($data as $item)
<tr>
    <td>
        <input type="checkbox" class="checkbox" value="{{$item->id}}" name="">
    </td>
    <td>{{ __data_pt($item->created_at, 1) }}</td>
    <td>{{ $item->cliente ? $item->cliente->info : '' }}</td>
    <td>{{ __moeda($item->total) }}</td>
    <td>{{ $item->chave }}</td>
    <td>{{ $item->numero }}</td>
</tr>
@endforeach
