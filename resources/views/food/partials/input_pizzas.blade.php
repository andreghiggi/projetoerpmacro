@foreach($produtos as $p)
<input type="hidden" name="pizza[]" value="{{ $p->hash_delivery }}">
@endforeach