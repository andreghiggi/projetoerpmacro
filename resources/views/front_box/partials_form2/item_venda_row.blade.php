<div class="row mt-1 products product-line-{{$code}}">
	<div class="col-md-2">
		<img src="{{ $item->img }}" class="img-cart">
	</div>
	<input readonly type="hidden" name="key" class="form-control" value="{{ $item->key }}">
	<div class="col-md-5 text-left cart-data">
		<span class="title">{{ substr($item->nome, 0, 30) }}</span><br>
		<span class="price">R$ {{ __moeda($item->valor_unitario )}}</span><br>
		<i class="ri-pencil-fill text-primary" onclick="editItem('{{$code}}', '{{$item->id}}')"></i>
		<i class="ri-close-circle-line text-danger" onclick="removeItem('{{$code}}')"></i>
	</div>

	<input type="hidden" class="produto_id" name="produto_id[]" value="{{ $item->id }}">
	<input type="hidden" class="quantidade" name="quantidade[]" value="1">
	<input type="hidden" class="valor_unitario" name="valor_unitario[]" value="{{ __moeda($item->valor_unitario) }}">
	<input type="hidden" class="subtotal_item" name="subtotal_item[]" value="{{ __moeda($item->valor_unitario) }}">

	<div class="col-md-5">
		<div class="d-flex" style="float: right;">
			<span class="increment-decrement btn btn-light rounded-circle" data-code="{{$code}}">-</span> 
			<input min="0" value="1" class="fw-semibold cart-qty m-0 px-2 qtd-row"> 
			<span class="increment-decrement btn btn-light rounded-circle btn-incrementa" data-code="{{$code}}">+</span>
		</div>
	</div>

	@if($item->precoComPromocao())
	<div class="col-md-12">
		<p>Promoção: <strong class="text-primary">{{ __data_pt($item->precoComPromocao()->data_inicio, 0) }}</strong> até <strong class="text-primary">{{ __data_pt($item->precoComPromocao()->data_fim, 0) }}</strong></p>
	</div>
	@endif
</div>